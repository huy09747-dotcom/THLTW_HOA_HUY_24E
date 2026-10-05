<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    // Danh sách danh mục
    public function index(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);

        /*
         * Giữ nguyên logic cũ:
         * mặc định chỉ lấy danh mục đang active.
         */
        $query = Category::active();

        // Tìm kiếm theo tên hoặc slug
        if (!empty($data['search'])) {
            $search = trim($data['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        /*
         * Nếu người dùng chọn trạng thái,
         * cho phép lọc cả danh mục hoạt động và danh mục ẩn.
         */
        if (isset($data['status'])) {
            $query = Category::query();

            if (!empty($data['search'])) {
                $search = trim($data['search']);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            }

            $query->where('status', $data['status']);
        }

        $categories = $query
            ->orderBy('id')
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
            'message' => 'Da chuyen danh muc vao thung rac'
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
            'message' => 'Khoi phuc danh muc thanh cong'
        ]);
    }

    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()
            ->findOrFail($id);

        $category->forceDelete();

        return response()->json([
            'message' => 'Da xoa vinh vien danh muc'
        ]);
    }
}