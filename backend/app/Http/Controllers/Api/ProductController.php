<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with('brand')->get();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'danhmuc_id' => 'required|integer',
            'thuonghieu_id' => 'required|integer',
            'tensanpham' => 'required|string|max:255',
            'hinhanh' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'giakhuyenmai' => 'nullable|numeric',
            'mota' => 'nullable|string',
            'soluongton' => 'required|integer',
            'trangthai' => 'required|integer',
        ]);

        $product = Product::create([
            'danhmuc_id' => $request->danhmuc_id,
            'thuonghieu_id' => $request->thuonghieu_id,
            'tensanpham' => $request->tensanpham,
            'hinhanh' => $request->hinhanh,
            'gia' => $request->gia,
            'giakhuyenmai' => $request->giakhuyenmai,
            'mota' => $request->mota,
            'soluongton' => $request->soluongton,
            'trangthai' => $request->trangthai,
        ]);

        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'danhmuc_id' => 'required|integer',
            'thuonghieu_id' => 'required|integer',
            'tensanpham' => 'required|string|max:255',
            'hinhanh' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'giakhuyenmai' => 'nullable|numeric',
            'mota' => 'nullable|string',
            'soluongton' => 'required|integer',
            'trangthai' => 'required|integer',
        ]);

        $product->update([
            'danhmuc_id' => $request->danhmuc_id,
            'thuonghieu_id' => $request->thuonghieu_id,
            'tensanpham' => $request->tensanpham,
            'hinhanh' => $request->hinhanh,
            'gia' => $request->gia,
            'giakhuyenmai' => $request->giakhuyenmai,
            'mota' => $request->mota,
            'soluongton' => $request->soluongton,
            'trangthai' => $request->trangthai,
        ]);

        return new ProductResource($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Đã xóa sản phẩm thành công'
        ]);
    }
}