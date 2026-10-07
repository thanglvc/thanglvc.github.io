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
        throw new LogicException('Category tests require the testing environment and an in-memory SQLite database.');
    }
});

test('CAT01 returns only category IDs and names in ID order without changing records', function (): void {
    $laterCategory = CategoryFactory::new()->create(['id' => 20, 'name' => 'Điện tử']);
    $earlierCategory = CategoryFactory::new()->create(['id' => 10, 'name' => 'Sách']);
    $product = ProductFactory::new()->create(['category_id' => $laterCategory->getKey()]);
    $originalCategories = [$earlierCategory->fresh()->getAttributes(), $laterCategory->fresh()->getAttributes()];
    $originalProduct = $product->fresh()->getAttributes();

    $response = $this->get('/api/categories', ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['data' => [
            ['id' => 10, 'name' => 'Sách'],
            ['id' => 20, 'name' => 'Điện tử'],
        ]]);
    $this->assertDatabaseCount('categories', 2);
    $this->assertDatabaseCount('products', 1);
    expect([$earlierCategory->fresh()->getAttributes(), $laterCategory->fresh()->getAttributes()])->toBe($originalCategories);
    expect($product->fresh()->getAttributes())->toBe($originalProduct);
});

test('CAT02 returns 200 with an empty list when no category exists', function (): void {
    $this->assertDatabaseCount('categories', 0);

    $response = $this->get('/api/categories', ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['data' => []]);
    $this->assertDatabaseCount('categories', 0);
    $this->assertDatabaseCount('products', 0);
});

test('CAT03 returns 500 without changing records when the category query fails', function (): void {
    $category = CategoryFactory::new()->create(['name' => 'Điện tử']);
    $product = ProductFactory::new()->create(['category_id' => $category->getKey()]);
    $originalCategory = $category->fresh()->getAttributes();
    $originalProduct = $product->fresh()->getAttributes();
    $triggered = false;
    DB::connection()->beforeExecuting(function (string $query) use (&$triggered): void {
        if (! $triggered && str_contains(strtolower($query), 'from "categories"')) {
            $triggered = true;
            throw new PDOException('Simulated category read failure.');
        }
    });

    $response = $this->get('/api/categories', ['Accept' => 'application/json']);

    $response->assertInternalServerError()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson(['message' => 'Internal server error.', 'errors' => []]);
    expect(json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)->errors)->toBeObject();
    expect($triggered)->toBeTrue();
    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseCount('products', 1);
    expect($category->fresh()->getAttributes())->toBe($originalCategory);
    expect($product->fresh()->getAttributes())->toBe($originalProduct);
});
