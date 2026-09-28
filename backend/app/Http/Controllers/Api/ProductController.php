<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    // Danh sách sản phẩm
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with(['category', 'brand'])
            ->latest()
            ->paginate(10);

        return ProductResource::collection($products);
    }

    // Chi tiết sản phẩm
    public function show($id)
    {
        $product = Product::with(['category', 'brand'])
            ->findOrFail($id);

        return new ProductResource($product);
    }

    // Thêm sản phẩm
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        $product->load(['category', 'brand']);

        return new ProductResource($product);
    }

    // Sửa sản phẩm
    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());

        $product->load(['category', 'brand']);

        return new ProductResource($product);
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Da xoa san pham thanh cong'
        ]);
    }
}