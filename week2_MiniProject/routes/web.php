<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('products.index');
});

Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products/create', function () {
    return view('products.create');
})->name('products.create');

Route::get('/products/{id}', function (string $id) {
    return view('products.show', ['id' => $id]);
})->whereNumber('id')->name('products.show');

Route::get('/products/{id}/edit', function (string $id) {
    return view('products.edit', ['id' => $id]);
})->whereNumber('id')->name('products.edit');
