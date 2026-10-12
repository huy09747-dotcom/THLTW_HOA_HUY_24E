<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donhang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonhangController extends Controller
{
    // Danh sách đơn hàng dành cho admin
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
        ]);

        $donhangs = Donhang::with('nguoidung')
            ->latest('id')
            ->paginate(10);

        return response()->json($donhangs);
    }
    // Xem chi tiết một đơn hàng
    public function show($id)
    {
        $donhang = Donhang::with([
            'nguoidung',
            'items.sanpham',
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Chi tiet don hang.',
            'data' => $donhang,
        ]);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],
        ]);

        $donhang = Donhang::findOrFail($id);

        $donhang->status = $validated['status'];
        $donhang->save();

        return response()->json([
            'message' => 'Cap nhat trang thai don hang thanh cong.',
            'data' => $donhang,
        ]);
    }

    // Lịch sử đơn hàng của người dùng đang đăng nhập
    public function myOrders(Request $request)
    {
        $donhangs = Donhang::where(
            'user_id',
            $request->user()->id
        )
            ->with('items.sanpham')
            ->latest('id')
            ->get();

        return response()->json([
            'message' => 'Lich su don hang.',
            'data' => $donhangs,
        ]);
    }
}