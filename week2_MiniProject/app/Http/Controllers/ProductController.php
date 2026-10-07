<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection // lấy danh sách
    {
        $products = Product::query()
            ->with('category:id,name') // tải trước danh mục cho cả danh sách bằng một query riêng
            ->orderByDesc('id') // tải sp mới trước
            ->paginate(10);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse // tạo SP từ data đã validate
    {
        $product = Product::create($request->validated());
        $product->load('category:id,name'); // load() tải relationship sau khi đã có model.

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('category:id,name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product // dùng route model binding: khi đăng ký route ở phần sau, Laravel sẽ tìm sản phẩm theo ID trong URL và trả 404 nếu không tồn tại.
    ): ProductResource {
        $product->update($request->validated());

        return new ProductResource($product->load('category:id,name'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
