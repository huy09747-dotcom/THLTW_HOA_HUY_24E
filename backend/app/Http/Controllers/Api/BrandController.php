<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    // Danh sách thương hiệu
    public function index(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);

        $query = Brand::with('products');

        // Tìm kiếm theo tên hoặc slug
        if (!empty($data['search'])) {
            $search = trim($data['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo trạng thái
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        $brands = $query
            ->latest('id')
            ->paginate(
                (int) ($data['per_page'] ?? 10),
                ['*'],
                'page',
                (int) ($data['page'] ?? 1)
            )
            ->appends(
                $request->only([
                    'search',
                    'status',
                    'per_page',
                ])
            );

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

        if ($brand->products()->withTrashed()->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'products' => 'Khong the xoa: van con san pham thuoc thuong hieu nay.',
            ]);
        }

        $brand->delete();

        return response()->json([
            'message' => 'Da xoa thuong hieu thanh cong'
        ]);
    }

    // Danh sách trong thùng rác
    public function trash(): AnonymousResourceCollection
    {
        $items = Brand::onlyTrashed()->orderByDesc('id')->paginate(10);

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
        if ($item->products()->withTrashed()->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'products' => 'Khong the xoa: van con san pham thuoc thuong hieu nay.',
            ]);
        }

        $item->forceDelete();

        return response()->json(['message' => 'Da xoa vinh vien.']);
    }
}
