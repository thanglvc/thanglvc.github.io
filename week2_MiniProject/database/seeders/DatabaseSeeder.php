<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::firstOrCreate(['name' => 'Điện tử']);
        $books = Category::firstOrCreate(['name' => 'Sách']);

        for ($index = 1; $index <= 12; $index++) {
            Product::firstOrCreate(
                ['name' => 'Sản phẩm mẫu '.$index],
                [
                    'category_id' => $index % 2 === 0
                        ? $books->id
                        : $electronics->id,
                    'price' => $index * 10000,
                    'description' => 'Dữ liệu để luyện CRUD.',
                ]
            );
        }
    }
}
