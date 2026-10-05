<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    // Danh sách thương hiệu
    public function index(): AnonymousResourceCollection
    {
        $brands = Brand::with('products')
            ->latest()
            ->paginate(10);

        return BrandResource::collection($brands);
    }

    // Chi tiết thương hiệu
    public function show($id)
    {
        $brand = Brand::with('products')
            ->findOrFail($id);

        return new BrandResource($brand);
    }

    // Thêm thương hiệu
    public function store(BrandRequest $request)
    {
        $brand = Brand::create($request->validated());

        return new BrandResource($brand);
    }

    // Sửa thương hiệu
    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $brand->update($request->validated());

        return new BrandResource($brand);
    }

    // Xóa thương hiệu
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        $brand->delete();

        return response()->json([
            'message' => 'Da xoa thuong hieu thanh cong'
        ]);
    }

    // Danh sách trong thùng rác
    public function trash(): AnonymousResourceCollection
    {
        $items = Brand::onlyTrashed()->orderBy('id')->paginate(10);

        return BrandResource::collection($items);
    }

    // Khôi phục bản ghi đã xóa mềm
    public function restore($id)
    {
        $item = Brand::onlyTrashed()->findOrFail($id);
        $item->restore();

        return response()->json(['message' => 'Khoi phuc thanh cong.']);
    }

    // Xóa vĩnh viễn bản ghi trong thùng rác
    public function forceDelete($id)
    {
        $item = Brand::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return response()->json(['message' => 'Da xoa vinh vien.']);
    }
}
