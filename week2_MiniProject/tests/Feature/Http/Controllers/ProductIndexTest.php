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
        throw new LogicException('Product index tests require the testing environment and an in-memory SQLite database.');
    }
});

test('LBE01 returns paginated products in descending ID order with category relation and metadata', function (): void {
    $category = CategoryFactory::new()->create(['name' => 'Điện tử']);
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create([
            'id' => $i,
            'name' => "Sản phẩm $i",
            'price' => 10000 * $i,
            'category_id' => $category->getKey(),
        ]);
    }

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json');

    $json = $response->json();
    expect($json['data'])->toHaveCount(10)
        ->and($json['data'][0]['id'])->toBe(11)
        ->and($json['data'][9]['id'])->toBe(2)
        ->and($json['meta']['per_page'])->toBe(10)
        ->and($json['meta']['current_page'])->toBe(1)
        ->and($json['meta']['last_page'])->toBe(2)
        ->and($json['meta']['total'])->toBe(11)
        ->and($json['data'][0]['category']['name'])->toBe('Điện tử');

    $this->assertDatabaseCount('products', 11);
});

test('LBE02 returns page 2 with remaining product', function (): void {
    $category = CategoryFactory::new()->create();
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create([
            'id' => $i,
            'category_id' => $category->getKey(),
        ]);
    }

    $response = $this->get('/api/products?page=2', ['Accept' => 'application/json']);

    $response->assertOk();
    $json = $response->json();
    expect($json['data'])->toHaveCount(1)
        ->and($json['data'][0]['id'])->toBe(1)
        ->and($json['meta']['current_page'])->toBe(2)
        ->and($json['meta']['last_page'])->toBe(2)
        ->and($json['links']['next'])->toBeNull()
        ->and($json['links']['prev'])->not->toBeNull();
});

test('LBE03 returns page 1 when page query parameter is omitted', function (): void {
    $category = CategoryFactory::new()->create();
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create(['id' => $i, 'category_id' => $category->getKey()]);
    }

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertOk();
    $json = $response->json();
    expect($json['meta']['current_page'])->toBe(1)
        ->and($json['meta']['from'])->toBe(1)
        ->and($json['meta']['to'])->toBe(10);
});

test('LBE04 defaults to page 1 when page is invalid', function (): void {
    $category = CategoryFactory::new()->create();
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create(['id' => $i, 'category_id' => $category->getKey()]);
    }

    $invalidPages = ['0', '-1', 'abc', '1.5'];
    foreach ($invalidPages as $page) {
        $response = $this->get("/api/products?page=$page", ['Accept' => 'application/json']);
        $response->assertOk();
        expect($response->json('meta.current_page'))->toBe(1);
    }
});

test('LBE05 returns empty data array when requested page exceeds last page', function (): void {
    $category = CategoryFactory::new()->create();
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create(['id' => $i, 'category_id' => $category->getKey()]);
    }

    $response = $this->get('/api/products?page=3', ['Accept' => 'application/json']);

    $response->assertOk();
    $json = $response->json();
    expect($json['data'])->toBe([])
        ->and($json['meta']['current_page'])->toBe(3)
        ->and($json['meta']['last_page'])->toBe(2)
        ->and($json['meta']['total'])->toBe(11)
        ->and($json['meta']['from'])->toBeNull()
        ->and($json['meta']['to'])->toBeNull();
});

test('LBE06 returns empty data when no product exists in database', function (): void {
    $this->assertDatabaseCount('products', 0);

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertOk();
    $json = $response->json();
    expect($json['data'])->toBe([])
        ->and($json['meta']['total'])->toBe(0)
        ->and($json['meta']['last_page'])->toBe(1)
        ->and($json['meta']['from'])->toBeNull();
});

test('LBE07 handles boundary product counts 1, 10, 20 correctly', function (): void {
    $category = CategoryFactory::new()->create();

    // 1 product
    $p1 = ProductFactory::new()->create(['id' => 1, 'category_id' => $category->getKey()]);
    $res1 = $this->get('/api/products', ['Accept' => 'application/json']);
    expect($res1->json('data'))->toHaveCount(1)
        ->and($res1->json('meta.last_page'))->toBe(1);

    // up to 10 products
    for ($i = 2; $i <= 10; $i++) {
        ProductFactory::new()->create(['id' => $i, 'category_id' => $category->getKey()]);
    }
    $res10 = $this->get('/api/products', ['Accept' => 'application/json']);
    expect($res10->json('data'))->toHaveCount(10)
        ->and($res10->json('meta.last_page'))->toBe(1);

    // up to 20 products
    for ($i = 11; $i <= 20; $i++) {
        ProductFactory::new()->create(['id' => $i, 'category_id' => $category->getKey()]);
    }
    $res20p1 = $this->get('/api/products?page=1', ['Accept' => 'application/json']);
    $res20p2 = $this->get('/api/products?page=2', ['Accept' => 'application/json']);
    expect($res20p1->json('data'))->toHaveCount(10)
        ->and($res20p2->json('data'))->toHaveCount(10)
        ->and($res20p1->json('meta.last_page'))->toBe(2);
});

test('LBE08 ignores unsupported parameters per_page and search', function (): void {
    $category = CategoryFactory::new()->create();
    for ($i = 1; $i <= 11; $i++) {
        ProductFactory::new()->create(['id' => $i, 'name' => "SP $i", 'category_id' => $category->getKey()]);
    }

    $response = $this->get('/api/products?per_page=100&search=khong-tim-thay', ['Accept' => 'application/json']);

    $response->assertOk();
    $json = $response->json();
    expect($json['meta']['per_page'])->toBe(10)
        ->and($json['meta']['total'])->toBe(11)
        ->and($json['data'])->toHaveCount(10);
});

test('LBE09 works without Content-Type header on GET', function (): void {
    $category = CategoryFactory::new()->create();
    ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertOk();
    $this->assertDatabaseCount('products', 1);
});

test('LBE10 returns correct schema for nullable description, price 0, and long name', function (): void {
    $category = CategoryFactory::new()->create(['name' => 'Điện tử']);
    $longName = str_repeat('a', 255);
    ProductFactory::new()->create([
        'name' => $longName,
        'price' => 0,
        'description' => null,
        'category_id' => $category->getKey(),
    ]);

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertOk();
    $item = $response->json('data.0');
    expect($item['name'])->toBe($longName)
        ->and($item['price'])->toBe('0.00')
        ->and($item['description'])->toBeNull()
        ->and($item['category']['name'])->toBe('Điện tử');
});

test('LBE11 returns 500 when product list query fails', function (): void {
    $category = CategoryFactory::new()->create();
    ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $triggered = false;
    DB::connection()->beforeExecuting(function (string $query) use (&$triggered): void {
        if (! $triggered && str_contains(strtolower($query), 'from "products"')) {
            $triggered = true;
            throw new PDOException('Simulated database error on products index.');
        }
    });

    $response = $this->get('/api/products', ['Accept' => 'application/json']);

    $response->assertInternalServerError()
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    expect($triggered)->toBeTrue();
    $this->assertDatabaseCount('products', 1);
});
