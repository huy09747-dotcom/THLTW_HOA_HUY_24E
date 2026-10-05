<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    // Danh sách sản phẩm
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['category', 'brand'])
            ->latest();

        // Tìm kiếm theo tên, slug hoặc SKU
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Lọc theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Lọc theo cho phép đặt mua
        if ($request->has('is_available') && $request->input('is_available') !== '') {
            $query->where('is_available', $request->input('is_available'));
        }

        // Lọc sản phẩm nổi bật
        if ($request->has('is_featured') && $request->input('is_featured') !== '') {
            $query->where('is_featured', $request->input('is_featured'));
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }

    // Chi tiết sản phẩm
    public function show($slug)
    {
        $product = Product::with(['category', 'brand'])
            ->where('slug', $slug)->firstOrFail();

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

    // Danh sách trong thùng rác
    public function trash(): AnonymousResourceCollection
    {
        $items = Product::onlyTrashed()->with(['category', 'brand'])->orderBy('id')->paginate(10);

        return ProductResource::collection($items);
    }

    // Khôi phục bản ghi đã xóa mềm
    public function restore($id)
    {
        $item = Product::onlyTrashed()->findOrFail($id);
        $item->restore();

        return response()->json(['message' => 'Khoi phuc thanh cong.']);
    }

    // Xóa vĩnh viễn bản ghi trong thùng rác
    public function forceDelete($id)
    {
        $item = Product::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return response()->json(['message' => 'Da xoa vinh vien.']);
    }
}
