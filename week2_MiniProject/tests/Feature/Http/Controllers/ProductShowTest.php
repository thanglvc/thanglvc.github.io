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
        throw new LogicException('Product show tests require the testing environment and an in-memory SQLite database.');
    }
});

test('DBE01 returns 200 with product details and loaded category relation', function (): void {
    $category = CategoryFactory::new()->create(['name' => 'Điện tử']);
    $otherCategory = CategoryFactory::new()->create(['name' => 'Sách']);
    $productP = ProductFactory::new()->create([
        'name' => 'Bàn phím',
        'price' => 199000,
        'description' => 'Bàn phím dùng để thực hành',
        'category_id' => $category->getKey(),
    ]);
    $productQ = ProductFactory::new()->create(['category_id' => $otherCategory->getKey()]);

    $response = $this->get("/api/products/{$productP->getKey()}", ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json');

    $data = $response->json('data');
    expect($data['id'])->toBe($productP->getKey())
        ->and($data['name'])->toBe('Bàn phím')
        ->and($data['price'])->toBe('199000.00')
        ->and($data['description'])->toBe('Bàn phím dùng để thực hành')
        ->and($data['category']['id'])->toBe($category->getKey())
        ->and($data['category']['name'])->toBe('Điện tử')
        ->and($data)->toHaveKeys(['id', 'name', 'price', 'description', 'category', 'created_at', 'updated_at']);

    $this->assertDatabaseCount('products', 2);
});

test('DBE02 returns 404 when product ID does not exist', function (): void {
    $response = $this->get('/api/products/999999', ['Accept' => 'application/json']);

    $response->assertNotFound()
        ->assertExactJson(['message' => 'Resource not found.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
});

test('DBE03 returns 404 after product was deleted', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $id = $product->getKey();

    $product->delete();

    $response = $this->get("/api/products/{$id}", ['Accept' => 'application/json']);

    $response->assertNotFound();
});

test('DBE04 returns correct schema for nullable description, price 0, and max price', function (): void {
    $category = CategoryFactory::new()->create();
    $productP = ProductFactory::new()->create([
        'price' => 0,
        'description' => null,
        'category_id' => $category->getKey(),
    ]);
    $productQ = ProductFactory::new()->create([
        'name' => str_repeat('a', 255),
        'price' => 99999999.99,
        'category_id' => $category->getKey(),
    ]);

    $resP = $this->get("/api/products/{$productP->getKey()}", ['Accept' => 'application/json']);
    $resP->assertOk();
    expect($resP->json('data.price'))->toBe('0.00')
        ->and($resP->json('data.description'))->toBeNull();

    $resQ = $this->get("/api/products/{$productQ->getKey()}", ['Accept' => 'application/json']);
    $resQ->assertOk();
    expect($resQ->json('data.price'))->toBe('99999999.99')
        ->and($resQ->json('data.name'))->toBe(str_repeat('a', 255));
});

test('DBE05 works without Content-Type header on GET show', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->get("/api/products/{$product->getKey()}", ['Accept' => 'application/json']);

    $response->assertOk();
});

test('DBE06 does not modify timestamps or database records on show', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $originalAttributes = $product->fresh()->getAttributes();

    $this->get("/api/products/{$product->getKey()}", ['Accept' => 'application/json']);
    $this->get("/api/products/{$product->getKey()}", ['Accept' => 'application/json']);

    expect($product->fresh()->getAttributes())->toBe($originalAttributes);
});

test('DBE07 returns 500 when product show query fails', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $triggered = false;
    DB::connection()->beforeExecuting(function (string $query) use (&$triggered): void {
        if (! $triggered && str_contains(strtolower($query), 'from "products"')) {
            $triggered = true;
            throw new PDOException('Simulated show query failure.');
        }
    });

    $response = $this->get("/api/products/{$product->getKey()}", ['Accept' => 'application/json']);

    $response->assertInternalServerError()
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    expect($triggered)->toBeTrue();
});
