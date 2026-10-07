<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('require.json')->group(function (): void {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::apiResource('products', ProductController::class);
});

// apiResource() đăng ký các route CRUD tương ứng với index, store, show, update, destroy.
// Nhóm require.json áp dụng middleware bạn vừa viết cho các route này.
