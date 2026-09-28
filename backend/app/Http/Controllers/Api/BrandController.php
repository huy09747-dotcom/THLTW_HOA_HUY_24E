<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    // Danh sách thương hiệu
    public function index(): AnonymousResourceCollection
    {
        $brands = Brand::with('products')->get();

        return BrandResource::collection($brands);
    }

    // Thêm thương hiệu
    public function store(Request $request)
    {
        $request->validate([
            'tenthuonghieu' => 'required|string|max:255',
            'trangthai' => 'required|integer',
        ]);

        $brand = Brand::create([
            'tenthuonghieu' => $request->tenthuonghieu,
            'trangthai' => $request->trangthai,
        ]);

        return new BrandResource($brand);
    }

    // Sửa thương hiệu
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'tenthuonghieu' => 'required|string|max:255',
            'trangthai' => 'required|integer',
        ]);

        $brand->update([
            'tenthuonghieu' => $request->tenthuonghieu,
            'trangthai' => $request->trangthai,
        ]);

        return new BrandResource($brand);
    }

    // Xóa thương hiệu
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        $brand->delete();

        return response()->json([
            'message' => 'Đã xóa thương hiệu thành công'
        ]);
    }
}