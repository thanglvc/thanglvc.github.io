<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete(); // Foreign key to categories.id, prevent deleting category with products
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable(); // allows null
            $table->timestamps(); // Add created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products'); // Drop products table when runc cmd: php artisan migrate:rollback
    }
};
