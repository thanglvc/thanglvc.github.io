<?php

namespace Tests\Feature\Http\Controllers;

use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\DB;
use LogicException;
use PDOException;

beforeEach(function (): void {
    if (! $this->app->environment('testing')
        || config('database.default') !== 'sqlite'
        || config('database.connections.sqlite.database') !== ':memory:') {
        throw new LogicException('Product update tests require the testing environment and an in-memory SQLite database.');
    }
});

test('UBE01 updates product with all four fields via PUT and returns 200', function (): void {
    $categoryA = CategoryFactory::new()->create(['name' => 'Điện tử']);
    $categoryB = CategoryFactory::new()->create(['name' => 'Sách']);
    $productP = ProductFactory::new()->create([
        'name' => 'Bàn phím',
        'price' => 199000,
        'category_id' => $categoryA->getKey(),
        'description' => 'Mô tả cũ',
    ]);
    $productQ = ProductFactory::new()->create(['category_id' => $categoryA->getKey()]);
    $origCreatedAt = $productP->created_at->toISOString();

    $payload = [
        'name' => 'Bàn phím mới',
        'price' => '299000.00',
        'category_id' => $categoryB->getKey(),
        'description' => 'Mô tả mới',
    ];

    $response = $this->putJson("/api/products/{$productP->getKey()}", $payload);

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json');

    $data = $response->json('data');
    expect($data['id'])->toBe($productP->getKey())
        ->and($data['name'])->toBe('Bàn phím mới')
        ->and($data['price'])->toBe('299000.00')
        ->and($data['category']['id'])->toBe($categoryB->getKey())
        ->and($data['description'])->toBe('Mô tả mới')
        ->and($data['created_at'])->toBe($origCreatedAt);

    // Q and categories unchanged
    expect($productQ->fresh()->category_id)->toBe($categoryA->getKey());
});

test('UBE02 updates a single field via PATCH and returns 200', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create([
        'name' => 'Tên gốc',
        'price' => 100000,
        'description' => 'Mô tả gốc',
        'category_id' => $category->getKey(),
    ]);

    $response = $this->patchJson("/api/products/{$product->getKey()}", ['price' => '12.50']);

    $response->assertOk();
    $data = $response->json('data');
    expect($data['price'])->toBe('12.50')
        ->and($data['name'])->toBe('Tên gốc')
        ->and($data['description'])->toBe('Mô tả gốc');
});

test('UBE03 leaves fields unchanged when PATCH receives empty body', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create([
        'name' => 'Tên gốc',
        'price' => 100000,
        'category_id' => $category->getKey(),
    ]);
    $productBefore = $product->fresh();

    $response = $this->patchJson("/api/products/{$product->getKey()}", []);

    $response->assertOk();
    expect($product->fresh()->name)->toBe($productBefore->name)
        ->and($product->fresh()->price)->toBe($productBefore->price);
});

test('UBE04 rejects PUT when any required field is missing', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $validPayload = [
        'name' => 'Tên mới',
        'price' => 150000,
        'category_id' => $category->getKey(),
        'description' => 'Mô tả',
    ];

    foreach (['name', 'price', 'category_id', 'description'] as $field) {
        $payload = $validPayload;
        unset($payload[$field]);
        $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }
});

test('UBE05 allows null or whitespace description on PUT', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $payload = [
        'name' => 'Tên mới',
        'price' => 150000,
        'category_id' => $category->getKey(),
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response->assertOk();
    expect($response->json('data.description'))->toBeNull();

    $payload['description'] = '   ';
    $response2 = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response2->assertOk();
    expect($response2->json('data.description'))->toBeNull();
});

test('UBE06 clears description when PATCH receives null description', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create([
        'description' => 'Đang có mô tả',
        'category_id' => $category->getKey(),
    ]);

    $response = $this->patchJson("/api/products/{$product->getKey()}", ['description' => null]);
    $response->assertOk();
    expect($response->json('data.description'))->toBeNull();
});

test('UBE07 rejects PATCH setting required fields to null', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    foreach (['name', 'price', 'category_id'] as $field) {
        $response = $this->patchJson("/api/products/{$product->getKey()}", [$field => null]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }
});

test('UBE08 normalizes whitespace and supports Unicode on update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $payload = [
        'name' => '  Bàn phím Việt  ',
        'price' => ' 12.50 ',
        'category_id' => $category->getKey(),
        'description' => "  dòng 1\ndòng 2  ",
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response->assertOk();
    expect($response->json('data.name'))->toBe('Bàn phím Việt')
        ->and($response->json('data.price'))->toBe('12.50')
        ->and($response->json('data.description'))->toBe("dòng 1\ndòng 2");
});

test('UBE09 allows duplicate product name on update', function (): void {
    $category = CategoryFactory::new()->create();
    $productP = ProductFactory::new()->create(['name' => 'Tên P', 'category_id' => $category->getKey()]);
    $productQ = ProductFactory::new()->create(['name' => 'Tên Q', 'category_id' => $category->getKey()]);

    $payload = [
        'name' => 'Tên Q',
        'price' => 100000,
        'category_id' => $category->getKey(),
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$productP->getKey()}", $payload);
    $response->assertOk();
    expect($response->json('data.name'))->toBe('Tên Q');
});

test('UBE10 ignores out-of-scope fields in update payload', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $id = $product->getKey();

    $payload = [
        'id' => 999999,
        'name' => 'Tên mới',
        'price' => 100000,
        'category_id' => $category->getKey(),
        'description' => null,
        'created_at' => '2000-01-01T00:00:00Z',
        'unknown_field' => 'ignored',
    ];

    $response = $this->putJson("/api/products/{$id}", $payload);
    $response->assertOk();
    expect($response->json('data.id'))->toBe($id);
});

test('UBE11 rejects empty or non-string name on PUT', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $base = ['price' => 10000, 'category_id' => $category->getKey(), 'description' => null];

    foreach (['', '   ', 123, [], ['obj']] as $name) {
        $response = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['name' => $name]));
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }
});

test('UBE12 checks Unicode name boundary: 255 valid, 256 invalid', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['price' => 10000, 'category_id' => $category->getKey(), 'description' => null];

    $name255 = str_repeat('a', 255);
    $res255 = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['name' => $name255]));
    $res255->assertOk();

    $name256 = str_repeat('a', 256);
    $res256 = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['name' => $name256]));
    $res256->assertUnprocessable()->assertJsonValidationErrors(['name']);
});

test('UBE13 accepts valid price boundaries: 0, 12.5, 12.50, 99999999.99', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'category_id' => $category->getKey(), 'description' => null];

    foreach ([0, '0', '12.5', '12.50', '99999999.99'] as $price) {
        $res = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['price' => $price]));
        $res->assertOk();
    }
});

test('UBE14 rejects empty, negative, or over-max price on update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'category_id' => $category->getKey(), 'description' => null];

    foreach (['', -0.01, '100000000'] as $price) {
        $res = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['price' => $price]));
        $res->assertUnprocessable()->assertJsonValidationErrors(['price']);
    }
});

test('UBE15 rejects invalid price format or >2 decimal places', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'category_id' => $category->getKey(), 'description' => null];

    foreach (['abc', '1.234', [], ['val']] as $price) {
        $res = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['price' => $price]));
        $res->assertUnprocessable()->assertJsonValidationErrors(['price']);
    }
});

test('UBE16 rejects non-existent category on update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $payload = [
        'name' => 'Tên',
        'price' => 10000,
        'category_id' => 999999,
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
});

test('UBE17 rejects category with invalid type or null', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'price' => 10000, 'description' => null];

    foreach ([null, 'abc', 1.5, [], ['x']] as $catId) {
        $res = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['category_id' => $catId]));
        $res->assertUnprocessable()->assertJsonValidationErrors(['category_id']);
    }
});

test('UBE18 accepts integer string category ID on update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $payload = [
        'name' => 'Tên',
        'price' => 10000,
        'category_id' => (string) $category->getKey(),
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response->assertOk();
});

test('UBE19 validates description boundaries: 2000 valid, 2001 invalid', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'price' => 10000, 'category_id' => $category->getKey()];

    $desc2000 = str_repeat('a', 2000);
    $res2000 = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['description' => $desc2000]));
    $res2000->assertOk();

    $desc2001 = str_repeat('a', 2001);
    $res2001 = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['description' => $desc2001]));
    $res2001->assertUnprocessable()->assertJsonValidationErrors(['description']);
});

test('UBE20 rejects description of invalid type', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $base = ['name' => 'Tên', 'price' => 10000, 'category_id' => $category->getKey()];

    foreach ([123, [], ['desc']] as $desc) {
        $res = $this->putJson("/api/products/{$product->getKey()}", array_merge($base, ['description' => $desc]));
        $res->assertUnprocessable()->assertJsonValidationErrors(['description']);
    }
});

test('UBE21 returns all four field errors when multiple fields invalid on PUT', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $payload = [
        'name' => '',
        'price' => -10,
        'category_id' => 999999,
        'description' => str_repeat('a', 2001),
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'price', 'category_id', 'description']);
});

test('UBE22 validates only sent fields in PATCH request', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->patchJson("/api/products/{$product->getKey()}", ['price' => -50]);
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['price'])
        ->assertJsonMissingValidationErrors(['name', 'category_id', 'description']);
});

test('UBE23 rejects request with missing or non-JSON Content-Type with 415', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->call(
        'PUT',
        "/api/products/{$product->getKey()}",
        [],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'text/plain'],
        json_encode(['name' => 'Tên mới'])
    );

    $response->assertStatus(415)
        ->assertExactJson(['message' => 'Content-Type must be application/json.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
});

test('UBE24 returns 404 when product ID does not exist on update', function (): void {
    $category = CategoryFactory::new()->create();
    $payload = [
        'name' => 'Tên',
        'price' => 10000,
        'category_id' => $category->getKey(),
        'description' => null,
    ];

    $resPut = $this->putJson('/api/products/999999', $payload);
    $resPut->assertNotFound();

    $resPatch = $this->patchJson('/api/products/999999', ['name' => 'Tên']);
    $resPatch->assertNotFound();
});

test('UBE25 returns 500 when database error occurs during update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $triggered = false;
    DB::connection()->beforeExecuting(function (string $query) use (&$triggered): void {
        if (! $triggered && str_contains(strtolower($query), 'update "products"')) {
            $triggered = true;
            throw new PDOException('Simulated update query failure.');
        }
    });

    $payload = [
        'name' => 'Tên mới',
        'price' => 20000,
        'category_id' => $category->getKey(),
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);

    $response->assertInternalServerError()
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect($triggered)->toBeTrue();
});

test('UBE26 returns 500 when relation load fails after update', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['name' => 'Cũ', 'category_id' => $category->getKey()]);

    $updated = false;
    DB::connection()->beforeExecuting(function (string $query) use (&$updated): void {
        if (str_contains(strtolower($query), 'update "products"')) {
            $updated = true;
        } elseif ($updated && str_contains(strtolower($query), 'from "categories"')) {
            throw new PDOException('Simulated relation load error after update.');
        }
    });

    $payload = [
        'name' => 'Tên mới sau ghi',
        'price' => 20000,
        'category_id' => $category->getKey(),
        'description' => null,
    ];

    $response = $this->putJson("/api/products/{$product->getKey()}", $payload);

    $response->assertInternalServerError();
    // Verify product was updated before the relation load failed
    expect($product->fresh()->name)->toBe('Tên mới sau ghi');
});
