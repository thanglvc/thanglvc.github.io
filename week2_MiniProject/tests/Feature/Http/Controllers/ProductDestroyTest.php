<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\Schema;
use LogicException;
use RuntimeException;

beforeEach(function (): void {
    if (! $this->app->environment('testing')
        || config('database.default') !== 'sqlite'
        || config('database.connections.sqlite.database') !== ':memory:') {
        throw new LogicException('Product destroy tests require the testing environment and an in-memory SQLite database.');
    }
});

test('XBE01 deletes the specified product, returns 204 no content, and keeps other product and category intact', function (): void {
    $category = CategoryFactory::new()->create(['name' => 'Danh mục A']);
    $productP = ProductFactory::new()->create([
        'name' => 'Bàn phím P',
        'price' => 199000,
        'category_id' => $category->getKey(),
        'description' => 'Mô tả P',
    ]);
    $productQ = ProductFactory::new()->create([
        'name' => 'Bàn phím Q',
        'price' => 299000,
        'category_id' => $category->getKey(),
        'description' => 'Mô tả Q',
    ]);

    $response = $this->delete('/api/products/'.$productP->getKey(), [], ['Accept' => 'application/json']);

    $response->assertNoContent();
    expect($response->getContent())->toBe('');

    $this->assertDatabaseMissing('products', ['id' => $productP->getKey()]);
    $this->assertDatabaseHas('products', ['id' => $productQ->getKey()]);
    $this->assertDatabaseHas('categories', ['id' => $category->getKey()]);

    $this->get('/api/products/'.$productP->getKey(), ['Accept' => 'application/json'])
        ->assertNotFound();
});

test('XBE02 returns 404 when deleting a non-existent product ID', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->delete('/api/products/999999', [], ['Accept' => 'application/json']);

    $response->assertNotFound()
        ->assertJson([
            'message' => 'Resource not found.',
            'errors' => [],
        ]);

    $this->assertDatabaseHas('products', ['id' => $product->getKey()]);
});

test('XBE03 returns 204 on first delete and 404 on second delete without affecting other products', function (): void {
    $category = CategoryFactory::new()->create();
    $productP = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $productQ = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response1 = $this->delete('/api/products/'.$productP->getKey(), [], ['Accept' => 'application/json']);
    $response1->assertNoContent();

    $response2 = $this->delete('/api/products/'.$productP->getKey(), [], ['Accept' => 'application/json']);
    $response2->assertNotFound()
        ->assertJson([
            'message' => 'Resource not found.',
            'errors' => [],
        ]);

    $this->assertDatabaseHas('products', ['id' => $productQ->getKey()]);
});

test('XBE04 succeeds with 204 when request has Accept header but no Content-Type and no body', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->call(
        'DELETE',
        '/api/products/'.$product->getKey(),
        [],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json']
    );

    $response->assertNoContent();
    $this->assertDatabaseMissing('products', ['id' => $product->getKey()]);
});

test('XBE05 deleting the last product returns 204 and empty list on subsequent GET', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $response = $this->delete('/api/products/'.$product->getKey(), [], ['Accept' => 'application/json']);
    $response->assertNoContent();

    $this->assertDatabaseCount('products', 0);
    $this->assertDatabaseHas('categories', ['id' => $category->getKey()]);

    $listResponse = $this->get('/api/products', ['Accept' => 'application/json']);
    $listResponse->assertOk();
    expect($listResponse->json('data'))->toBe([]);
});

test('XBE06 hard deletes the product record without soft deletes', function (): void {
    $category = CategoryFactory::new()->create();
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    $this->delete('/api/products/'.$product->getKey(), [], ['Accept' => 'application/json'])
        ->assertNoContent();

    $this->assertDatabaseMissing('products', ['id' => $product->getKey()]);
    expect(Schema::hasColumn('products', 'deleted_at'))->toBeFalse();
});

test('XBE07 returns 500 when exception occurs before delete and preserves product in database', function (): void {
    $category = CategoryFactory::new()->create();
    $productP = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $productQ = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    Product::deleting(function () {
        throw new RuntimeException('Database failure before deletion');
    });

    $response = $this->delete('/api/products/'.$productP->getKey(), [], ['Accept' => 'application/json']);

    $response->assertInternalServerError();
    $this->assertDatabaseHas('products', ['id' => $productP->getKey()]);
    $this->assertDatabaseHas('products', ['id' => $productQ->getKey()]);
    $this->assertDatabaseHas('categories', ['id' => $category->getKey()]);
});

test('XBE08 returns 500 when exception occurs after delete but deletion is not rolled back without transaction', function (): void {
    $category = CategoryFactory::new()->create();
    $productP = ProductFactory::new()->create(['category_id' => $category->getKey()]);

    Product::deleted(function () {
        throw new RuntimeException('Database failure after deletion');
    });

    $response = $this->delete('/api/products/'.$productP->getKey(), [], ['Accept' => 'application/json']);

    $response->assertInternalServerError();
    $this->assertDatabaseMissing('products', ['id' => $productP->getKey()]);
    $this->assertDatabaseHas('categories', ['id' => $category->getKey()]);
});
