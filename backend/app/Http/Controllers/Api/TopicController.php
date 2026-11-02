<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChudeBaiViet;
use App\Support\AdminFilters;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(AdminFilters::apply(ChudeBaiViet::query(), $r, ['name', 'slug'])->latest('id')->paginate(10));
    }

    public function save(Request $r, ?int $id = null)
    {
        $data = $r->validate(['name' => ['required', 'string', 'max:255', Rule::unique('chudebaiviet')->ignore($id)], 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:255', Rule::unique('chudebaiviet')->ignore($id)], 'description' => 'nullable|string|max:2000', 'sort_order' => 'required|integer|min:0|max:9999', 'status' => 'required|integer|in:0,1']);
        $item = $id ? ChudeBaiViet::findOrFail($id) : new ChudeBaiViet;
        $item->fill($data)->save();

        return response()->json(['message' => 'Đã lưu chủ đề.', 'data' => $item]);
    }

    public function destroy(int $id)
    {
        $item = ChudeBaiViet::findOrFail($id);
        abort_if($item->baiviets()->withTrashed()->exists(), 422, 'Chủ đề còn bài viết, không thể xóa.');
        $item->delete();

        return response()->json(['message' => 'Đã xóa chủ đề.']);
    }
}
