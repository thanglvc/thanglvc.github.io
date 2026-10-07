<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

// Controller này trả danh sách danh mục gồm id và name, để người gọi API chọn category_id khi tạo sản phẩm.

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Category::query()
                ->orderBy('id')
                ->get(['id', 'name']),
        ]);
    }
}
