<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiViet;
use App\Models\Menu;
use App\Support\AdminFilters;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(AdminFilters::apply(BaiViet::where('post_type', 'page'), $r, ['title', 'slug'])->latest('id')->paginate(10));
    }

    public function show(string $slug)
    {
        return response()->json(['data' => BaiViet::where('post_type', 'page')->where('slug', $slug)->where('status', 1)->firstOrFail()->only(['id', 'title', 'slug', 'description', 'detail', 'status'])]);
    }

    public function save(Request $r, ?int $id = null)
    {
        $reserved = ['admin', 'api', 'products', 'cart', 'orders', 'profile', 'login', 'register', 'news', 'favorites', 'forgot-password', 'reset-password', 'pages'];
        $data = $r->validate(['title' => 'required|string|max:255', 'slug' => ['required', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::notIn($reserved), Rule::unique('baiviet')->ignore($id)], 'description' => 'nullable|string|max:2000', 'detail' => 'required|string|max:100000', 'status' => 'required|integer|in:0,1,2']);
        $item = $id ? BaiViet::where('post_type', 'page')->findOrFail($id) : new BaiViet(['post_type' => 'page']);
        if (! $item->exists) {
            $item->created_by = $r->user()->id;
        }
        abort_if($item->exists && in_array($item->slug, ['about', 'contact', 'purchase-policy']) && $item->slug !== $data['slug'], 422, 'Không đổi đường dẫn trang hệ thống.');
        $item->fill($data);
        $item->updated_by = $r->user()->id;
        $item->save();

        return response()->json(['message' => 'Đã lưu trang.', 'data' => $item]);
    }

    public function update(Request $r, string $slug)
    {
        $item = BaiViet::where('post_type', 'page')->where('slug', $slug)->firstOrFail();
        $r->merge(['slug' => $slug]);

        return $this->save($r, $item->id);
    }

    public function destroy(int $id)
    {
        $item = BaiViet::where('post_type', 'page')->findOrFail($id);
        abort_if(Menu::where('type', 'page')->where('table_id', $id)->exists(), 422, 'Trang đang được menu sử dụng. Hãy sửa menu trước khi xóa.');
        $item->delete();

        return response()->json(['message' => 'Đã xóa trang.']);
    }
}
