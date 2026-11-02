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
        $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|min:1',
            'brand_id' => 'nullable|integer|min:1',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0' . ($request->filled('min_price') ? '|gte:min_price' : ''),
            'on_sale' => 'sometimes|in:0,1',
            'status' => 'nullable|integer|in:0,1,2',
            'is_available' => 'nullable|in:0,1',
            'is_featured' => 'nullable|in:0,1',
        ], [
            'max_price.gte' => 'Gia cao nhat phai lon hon hoac bang gia thap nhat.',
            'min_price.min' => 'Gia khong duoc am.',
            'max_price.min' => 'Gia khong duoc am.',
        ]);

        $query = Product::withReviewSummary()->with(['category', 'brand'])
            ->latest('id');

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

        if ($request->boolean('on_sale')) {
            $query->where('discount_percent', '>', 0)->whereNotNull('price_sale');
        }

        // Lọc theo giá thực tế sau khuyến mãi, giống giá hiển thị ở Shop.
        $finalPrice = 'ROUND(price_sale * (100 - COALESCE(discount_percent, 0)) / 100.0)';
        if ($request->filled('min_price')) {
            $query->whereRaw($finalPrice . ' >= CAST(? AS DECIMAL(20, 2))', [$request->input('min_price')]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw($finalPrice . ' <= CAST(? AS DECIMAL(20, 2))', [$request->input('max_price')]);
        }

        $products = $query->paginate(10)->withQueryString();

        return ProductResource::collection($products);
    }

    // Chi tiết sản phẩm
    public function show($slug)
    {
        $product = Product::withReviewSummary()->with(['category', 'brand'])
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
        $items = Product::onlyTrashed()->with(['category', 'brand'])->orderByDesc('id')->paginate(10);

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
