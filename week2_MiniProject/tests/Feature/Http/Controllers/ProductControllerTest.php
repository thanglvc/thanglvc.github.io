<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use LogicException;
use PDOException;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\travelTo;

beforeEach(function (): void {
    if (! $this->app->environment('testing')
        || config('database.default') !== 'sqlite'
        || config('database.connections.sqlite.database') !== ':memory:') {
        throw new LogicException('Product tests require the testing environment and an in-memory SQLite database.');
    }
});

// Chuẩn bị dữ liệu
function createProductFixture(): array
{
    travelTo('2026-10-05 08:00:00 UTC');
    app()->setLocale('en');

    // Factory là công cụ tạo dữ liệu phục vụ test. Gọi create() sẽ lưu bản ghi vào DB test.
    $category = CategoryFactory::new()->create(['name' => 'Điện tử']);
    $otherCategory = CategoryFactory::new()->create(['name' => 'Sách']);

    return [
        'category' => $category,
        'otherCategory' => $otherCategory,
        'payload' => [
            'name' => 'Bàn phím',
            'price' => 199000,
            'category_id' => $category->getKey(),
            'description' => 'Bàn phím dùng để thực hành',
        ],
        'categories' => Category::query()->orderBy('id')->get()
            ->map(fn (Category $category): array => $category->getAttributes())->all(),
    ];
}

/**
 * Check the response contract and the complete persisted product.
 *
 * @param  array<string, mixed>  $payload
 */
function assertCreatedProduct(
    TestResponse $response,
    array $payload,
    Category $category,
    string $expectedPrice,
    ?string $expectedDescription,
    int $expectedCount
): int {
    $response->assertCreated()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJsonStructure([
            'data' => [
                'id', 'name', 'price', 'description',
                'category' => ['id', 'name'],
                'created_at', 'updated_at',
            ],
        ])
        ->assertJsonPaths([
            'data.name' => $payload['name'],
            'data.price' => $expectedPrice,
            'data.description' => $expectedDescription,
            'data.category.id' => $category->getKey(),
            'data.category.name' => $category->name,
            'data.created_at' => '2026-10-05T08:00:00.000000Z',
            'data.updated_at' => '2026-10-05T08:00:00.000000Z',
        ]);

    $id = $response->json('data.id');
    expect($id)->toBeInt()->toBeGreaterThan(0);

    assertDatabaseCount('products', $expectedCount);
    assertDatabaseHas('products', [
        'id' => $id,
        'name' => $payload['name'],
        'price' => $expectedPrice,
        'description' => $expectedDescription,
        'category_id' => $category->getKey(),
        'created_at' => '2026-10-05 08:00:00',
        'updated_at' => '2026-10-05 08:00:00',
    ]);

    return $id;
}

/**
 * Compare all category attributes with the pre-request snapshot.
 *
 * @param  array<int, array<string, mixed>>  $expected
 */
function assertCategoriesUnchanged(array $expected): void
{
    $actual = Category::query()->orderBy('id')->get()
        ->map(fn (Category $category): array => $category->getAttributes())->all();

    expect($actual)->toBe($expected);
}

/**
 * Fail one real database operation without replacing Eloquent or the query builder.
 */
function injectProductDatabaseFailure(string $phase): object
{
    $state = (object) ['triggered' => false, 'productInserted' => false];
    $connection = DB::connection();

    $connection->listen(static function (QueryExecuted $event) use ($state): void {
        if (str_starts_with(strtolower($event->sql), 'insert into "products"')) {
            $state->productInserted = true;
        }
    });

    $connection->beforeExecuting(static function (string $query) use ($phase, $state): void {
        if ($state->triggered) {
            return;
        }

        $query = strtolower($query);
        $readsCategories = str_starts_with($query, 'select') && str_contains($query, '"categories"');
        $insertsProduct = str_starts_with($query, 'insert into "products"');
        $shouldFail = match ($phase) {
            'validation' => $readsCategories && ! $state->productInserted,
            'insert' => $insertsProduct,
            'category' => $readsCategories && $state->productInserted,
        };

        if ($shouldFail) {
            $state->triggered = true;

            throw new PDOException('Injected private database failure: '.$phase);
        }
    });

    return $state;
}

test('returns 201 and persists valid product input :dataset', function (
    array $changes,
    array $omittedFields, // delete field
    string $expectedPrice,
    ?string $expectedDescription,
    bool $useOtherCategory
): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], $changes);
    $category = $useOtherCategory ? $fixture['otherCategory'] : $fixture['category'];
    $payload['category_id'] = $category->getKey();

    foreach ($omittedFields as $field) {
        unset($payload[$field]);
    }

    $response = $this->postJson('/api/products', $payload);

    assertCreatedProduct($response, $payload, $category, $expectedPrice, $expectedDescription, 1);
    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC01 complete valid body' => [[], [], '199000.00', 'Bàn phím dùng để thực hành', false],
    'TC02 omitted description' => [[], ['description'], '199000.00', null, false],
    'TC03 null description' => [['description' => null], [], '199000.00', null, false],
    'TC05 another existing category' => [[], [], '199000.00', 'Bàn phím dùng để thực hành', true],
    'TC22 one-character name' => [['name' => 'A'], [], '199000.00', 'Bàn phím dùng để thực hành', false],
    'TC23 254-character name' => [['name' => str_repeat('a', 254)], [], '199000.00', 'Bàn phím dùng để thực hành', false],
    'TC24 255-character name' => [['name' => str_repeat('a', 255)], [], '199000.00', 'Bàn phím dùng để thực hành', false],
    'TC27 minimum price' => [['price' => 0], [], '0.00', 'Bàn phím dùng để thực hành', false],
    'TC28 above minimum price' => [['price' => 0.01], [], '0.01', 'Bàn phím dùng để thực hành', false],
    'TC29 below maximum price' => [['price' => 99999999.98], [], '99999999.98', 'Bàn phím dùng để thực hành', false],
    'TC30 maximum price' => [['price' => 99999999.99], [], '99999999.99', 'Bàn phím dùng để thực hành', false],
    'TC32 integer price' => [['price' => 10], [], '10.00', 'Bàn phím dùng để thực hành', false],
    'TC33 one decimal place' => [['price' => 10.5], [], '10.50', 'Bàn phím dùng để thực hành', false],
    'TC34 two decimal places' => [['price' => 10.55], [], '10.55', 'Bàn phím dùng để thực hành', false],
    'TC36 empty description' => [['description' => ''], [], '199000.00', null, false],
    'TC37 1999-character description' => [['description' => str_repeat('a', 1999)], [], '199000.00', str_repeat('a', 1999), false],
    'TC38 2000-character description' => [['description' => str_repeat('a', 2000)], [], '199000.00', str_repeat('a', 2000), false],
]);

test('TC04 returns 201 for a duplicate name without updating the original product', function (): void {
    $fixture = createProductFixture();
    $existing = ProductFactory::new()->create([
        'category_id' => $fixture['category']->getKey(),
        'name' => 'Bàn phím',
        'price' => 100000,
        'description' => 'Sản phẩm ban đầu',
    ]);
    $original = $existing->fresh()->getAttributes();

    $response = $this->postJson('/api/products', $fixture['payload']);

    $id = assertCreatedProduct($response, $fixture['payload'], $fixture['category'], '199000.00', 'Bàn phím dùng để thực hành', 2);
    expect($id)->not->toBe($existing->getKey());
    expect($existing->fresh()->getAttributes())->toBe($original);
    assertCategoriesUnchanged($fixture['categories']);
});

test('TC06 returns distinct product IDs for two identical valid requests', function (): void {
    $fixture = createProductFixture();

    $firstResponse = $this->postJson('/api/products', $fixture['payload']);
    $firstId = assertCreatedProduct($firstResponse, $fixture['payload'], $fixture['category'], '199000.00', 'Bàn phím dùng để thực hành', 1);

    $secondResponse = $this->postJson('/api/products', $fixture['payload']);
    $secondId = assertCreatedProduct($secondResponse, $fixture['payload'], $fixture['category'], '199000.00', 'Bàn phím dùng để thực hành', 2);

    expect($secondId)->not->toBe($firstId);
    assertCategoriesUnchanged($fixture['categories']);
});

test('returns 422 with field errors and creates no product :dataset', function (
    array $changes,
    array $omittedFields,
    array $expectedErrors,
    bool $useMissingCategory
): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], $changes);

    foreach ($omittedFields as $field) {
        unset($payload[$field]);
    }

    if ($useMissingCategory) {
        $payload['category_id'] = $fixture['otherCategory']->getKey() + 1;
        $this->assertDatabaseMissing('categories', ['id' => $payload['category_id']]);
    }

    $response = $this->postJson('/api/products', $payload);

    $response->assertUnprocessable()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJsonStructure(['message', 'errors' => array_keys($expectedErrors)])
        ->assertJsonPath('message', 'Validation failed.')
        ->assertOnlyJsonValidationErrors($expectedErrors);
    $this->assertDatabaseCount('products', 0);
    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC07 missing name' => [[], ['name'], ['name' => 'The name field is required.'], false],
    'TC08 empty name' => [['name' => ''], [], ['name' => 'The name field is required.'], false],
    'TC09 null name' => [['name' => null], [], ['name' => 'The name field is required.'], false],
    'TC10 non-string name' => [['name' => 123], [], ['name' => 'The name field must be a string.'], false],
    'TC11 whitespace-only name' => [['name' => '   '], [], ['name' => 'The name field is required.'], false],
    'TC12 missing price' => [[], ['price'], ['price' => 'The price field is required.'], false],
    'TC13 null price' => [['price' => null], [], ['price' => 'The price field is required.'], false],
    'TC14 non-numeric price' => [['price' => 'abc'], [], ['price' => 'The price field must be a number.'], false],
    'TC15 missing category' => [[], ['category_id'], ['category_id' => 'The category id field is required.'], false],
    'TC16 null category' => [['category_id' => null], [], ['category_id' => 'The category id field is required.'], false],
    'TC17 fractional category ID' => [['category_id' => 1.5], [], ['category_id' => 'The category id field must be an integer.'], false],
    'TC18 non-numeric category ID' => [['category_id' => 'abc'], [], ['category_id' => 'The category id field must be an integer.'], false],
    'TC19 nonexistent category' => [[], [], ['category_id' => 'The selected category id is invalid.'], true],
    'TC20 non-string description' => [['description' => 123], [], ['description' => 'The description field must be a string.'], false],
    'TC25 name over 255 characters' => [['name' => str_repeat('a', 256)], [], ['name' => 'The name field must not be greater than 255 characters.'], false],
    'TC26 negative price' => [['price' => -0.01], [], ['price' => 'The price field must be at least 0.'], false],
    'TC31 price above maximum' => [['price' => 100000000], [], ['price' => 'The price field must not be greater than 99999999.99.'], false],
    'TC35 three decimal places' => [['price' => 10.555], [], ['price' => 'The price field must have 0-2 decimal places.'], false],
    'TC39 description over 2000 characters' => [['description' => str_repeat('a', 2001)], [], ['description' => 'The description field must not be greater than 2000 characters.'], false],
]);

test('TC21 returns 422 for every missing required field in an empty JSON object', function (): void {
    $fixture = createProductFixture();

    $response = $this->call('POST', '/api/products', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
    ], '{}');

    $response->assertUnprocessable()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJsonStructure(['message', 'errors' => ['name', 'price', 'category_id']])
        ->assertJsonPath('message', 'Validation failed.')
        ->assertOnlyJsonValidationErrors([
            'name' => 'The name field is required.',
            'price' => 'The price field is required.',
            'category_id' => 'The category id field is required.',
        ]);
    $this->assertDatabaseCount('products', 0);
    assertCategoriesUnchanged($fixture['categories']);
});

test('TC40 returns 415 for a non-JSON Content-Type and creates no product', function (): void {
    $fixture = createProductFixture();

    $response = $this->call('POST', '/api/products', [], [], [], [
        'CONTENT_TYPE' => 'text/plain',
        'HTTP_ACCEPT' => 'application/json',
    ], json_encode($fixture['payload'], JSON_THROW_ON_ERROR));

    $response->assertUnsupportedMediaType()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['message' => 'Content-Type must be application/json.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    $this->assertDatabaseCount('products', 0);
    assertCategoriesUnchanged($fixture['categories']);
});

test('TC41 returns 415 for a truly absent Content-Type and creates no product', function (): void {
    $fixture = createProductFixture();
    $request = Request::create('/api/products', 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ], json_encode($fixture['payload'], JSON_THROW_ON_ERROR));

    // Symfony adds a form Content-Type to POST requests unless it is explicitly removed.
    $request->headers->remove('Content-Type');
    $request->server->remove('CONTENT_TYPE');
    $kernel = $this->app->make(Kernel::class);

    $response = TestResponse::fromBaseResponse($kernel->handle($request), $request);
    $kernel->terminate($request, $response->baseResponse);

    expect($response->baseRequest->headers->has('Content-Type'))->toBeFalse();
    $response->assertUnsupportedMediaType()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['message' => 'Content-Type must be application/json.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    $this->assertDatabaseCount('products', 0);
    assertCategoriesUnchanged($fixture['categories']);
});

test('returns 500 with the correct persisted state after a database failure :dataset', function (
    string $phase,
    int $expectedCount,
    bool $expectedInsert
): void {
    $fixture = createProductFixture();
    $failure = injectProductDatabaseFailure($phase);

    $response = $this->postJson('/api/products', $fixture['payload']);

    $response->assertInternalServerError()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    expect($failure->triggered)->toBeTrue();
    expect($failure->productInserted)->toBe($expectedInsert);
    $this->assertDatabaseCount('products', $expectedCount);

    if ($expectedInsert) {
        $this->assertDatabaseHas('products', [
            'name' => 'Bàn phím',
            'price' => '199000.00',
            'description' => 'Bàn phím dùng để thực hành',
            'category_id' => $fixture['category']->getKey(),
            'created_at' => '2026-10-05 08:00:00',
            'updated_at' => '2026-10-05 08:00:00',
        ]);
    }

    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC42 category validation fails before insert' => ['validation', 0, false],
    'TC43 insert fails before writing a product' => ['insert', 0, false],
    'TC44 category loading fails after a successful insert' => ['category', 1, true],
]);

test('returns 201 with two decimal places for the FE price string :dataset', function (
    string $price,
    string $expectedPrice
): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], ['price' => $price]);

    $response = $this->postJson('/api/products', $payload);

    assertCreatedProduct($response, $payload, $fixture['category'], $expectedPrice, $payload['description'], 1);
    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC45 leading decimal point' => ['.5', '0.50'],
    'TC46 trailing decimal zero' => ['12.50', '12.50'],
    'TC47 maximum price string' => ['99999999.99', '99999999.99'],
]);

test('TC48 trims outer spaces while preserving inner spaces and description line breaks', function (): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], [
        'name' => '  Bàn  phím  ',
        'price' => ' 12.50 ',
        'description' => "  Dòng 1\nDòng  2  ",
    ]);
    $expected = array_replace($payload, [
        'name' => 'Bàn  phím',
        'description' => "Dòng 1\nDòng  2",
    ]);

    $response = $this->postJson('/api/products', $payload);

    assertCreatedProduct($response, $expected, $fixture['category'], '12.50', $expected['description'], 1);
    assertCategoriesUnchanged($fixture['categories']);
});

test('TC49 stores a whitespace-only description as null', function (): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], ['description' => "  \n  "]);

    $response = $this->postJson('/api/products', $payload);

    assertCreatedProduct($response, $payload, $fixture['category'], '199000.00', null, 1);
    assertCategoriesUnchanged($fixture['categories']);
});

test('returns 201 at the Unicode character limit :dataset', function (array $changes): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], $changes);

    $response = $this->postJson('/api/products', $payload);

    assertCreatedProduct($response, $payload, $fixture['category'], '199000.00', $payload['description'], 1);
    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC50 name with 255 Unicode characters' => [['name' => str_repeat('😀', 255)]],
    'TC52 description with 2000 Unicode characters' => [['description' => str_repeat('😀', 2000)]],
]);

test('returns 422 without inserting for the additional boundary :dataset', function (
    array $changes,
    array $expectedErrors
): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], $changes);

    $response = $this->postJson('/api/products', $payload);

    $response->assertUnprocessable()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJsonStructure(['message', 'errors' => array_keys($expectedErrors)])
        ->assertJsonPath('message', 'Validation failed.')
        ->assertOnlyJsonValidationErrors($expectedErrors);
    assertDatabaseCount('products', 0);
    assertCategoriesUnchanged($fixture['categories']);
})->with([
    'TC51 name with 256 Unicode characters' => [
        ['name' => str_repeat('😀', 256)],
        ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'TC53 description with 2001 Unicode characters' => [
        ['description' => str_repeat('😀', 2001)],
        ['description' => 'The description field must not be greater than 2000 characters.'],
    ],
    'TC54 price string with three decimal places including zero' => [
        ['price' => '12.500'],
        ['price' => 'The price field must have 0-2 decimal places.'],
    ],
]);

test('TC55 ignores unvalidated fields and generates its own ID and timestamps', function (): void {
    $fixture = createProductFixture();
    $payload = array_replace($fixture['payload'], [
        'id' => 987654,
        'created_at' => '2000-01-01 00:00:00',
        'isSubmitting' => true,
    ]);

    $response = $this->postJson('/api/products', $payload);

    $id = assertCreatedProduct($response, $payload, $fixture['category'], '199000.00', $payload['description'], 1);
    expect($id)->not->toBe(987654);
    assertCategoriesUnchanged($fixture['categories']);
});

test('TC56 returns 500 without inserting when the category disappears after validation', function (): void {
    $fixture = createProductFixture();
    $categoryId = $fixture['category']->getKey();
    $otherCategory = $fixture['otherCategory']->fresh()->getAttributes();
    $deleted = false;
    DB::connection()->beforeExecuting(function (string $query) use ($categoryId, &$deleted): void {
        if (! $deleted && str_starts_with(strtolower($query), 'insert into "products"')) {
            $deleted = true;
            Category::query()->whereKey($categoryId)->delete();
        }
    });

    $response = $this->postJson('/api/products', $fixture['payload']);

    $response->assertInternalServerError()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    expect($deleted)->toBeTrue();
    assertDatabaseCount('products', 0);
    assertDatabaseCount('categories', 1);
    expect($fixture['otherCategory']->fresh()->getAttributes())->toBe($otherCategory);
});
