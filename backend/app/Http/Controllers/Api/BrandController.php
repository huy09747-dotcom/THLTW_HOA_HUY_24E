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
}