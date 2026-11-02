<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AdminFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate(['name' => 'required|string|max:255', 'email' => 'required|email|max:255', 'phone' => 'required|string|regex:/^\+?[0-9]{9,15}$/', 'title' => 'required|string|max:255', 'content' => 'required|string|max:10000']);
        DB::table('lienhe')->insert($data + ['status' => 2, 'created_at' => now(), 'updated_at' => now()]);

        return response()->json(['message' => 'Đã gửi liên hệ. HH Shop sẽ tiếp nhận và phản hồi qua thông tin bạn cung cấp.'], 201);
    }

    public function index(Request $r)
    {
        return response()->json(AdminFilters::apply(DB::table('lienhe')->whereNull('deleted_at'), $r, ['name', 'email', 'title'])->orderByDesc('id')->paginate(10));
    }

    public function update(Request $r, int $id)
    {
        $data = $r->validate(['status' => 'required|integer|in:1,2', 'reply_content' => 'nullable|string|max:10000']);
        abort_unless(DB::table('lienhe')->whereNull('deleted_at')->where('id', $id)->exists(), 404);
        DB::table('lienhe')->where('id', $id)->update($data + ['replay_id' => $r->user()->id, 'reply_at' => now(), 'updated_at' => now()]);

        return response()->json(['message' => 'Đã lưu phản hồi và trạng thái xử lý.']);
    }

    public function destroy(int $id)
    {
        $item = DB::table('lienhe')->whereNull('deleted_at')->where('id', $id)->first();
        abort_unless($item, 404);
        abort_unless((int) $item->status === 1, 422, 'Chỉ xóa liên hệ đã xử lý.');
        DB::table('lienhe')->where('id', $id)->update(['deleted_at' => now()]);

        return response()->json(['message' => 'Đã xóa liên hệ.']);
    }
}
