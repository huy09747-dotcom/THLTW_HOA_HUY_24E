<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    // Danh sách danh mục
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::active()
            ->orderBy('id')
            ->get();

        return CategoryResource::collection($categories);
    }
    // Thêm danh mục
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::create($data);
        $category->refresh();

        return new CategoryResource($category);
    }
    // Sửa danh mục
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();
        $category->update($data);
        return new CategoryResource($category);
    }
    // Xóa tạm -> đưa vào thùng rác
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'message' => 'Đã chuyển danh mục vào thùng rác'
        ]);
    }
    // Danh sách trong thùng rác
    public function trash(): AnonymousResourceCollection
    {
        $categories = Category::onlyTrashed()
            ->orderBy('id')
            ->get();
        return CategoryResource::collection($categories);
    }

    // Khôi phục
    public function restore($id)
    {
        $category = Category::onlyTrashed()
            ->findOrFail($id);
        $category->restore();
        return response()->json([
            'message' => 'Khôi phục danh mục thành công'
        ]);
    }
    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()
            ->findOrFail($id);
        $category->forceDelete();
        return response()->json([
            'message' => 'Đã xóa vĩnh viễn danh mục'
        ]);
    }
}
