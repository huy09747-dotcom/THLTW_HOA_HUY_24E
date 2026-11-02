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

        $donhang = \Illuminate\Support\Facades\DB::transaction(function () use ($id, $validated) {
            $order = Donhang::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_if((int) $order->status === 5 && (int) $validated['status'] !== 5,
                422, 'Khong the thay doi don da huy.');
            if ((int) $validated['status'] === 5 && (int) $order->status !== 5) {
                foreach ($order->items()->orderBy('product_id')->get() as $item) {
                    \App\Models\Product::withTrashed()->whereKey($item->product_id)->increment('qty', (int) $item->qty);
                }
            }
            if ((int) $validated['status'] === 4 && $order->payment_method === 'cod') {
                $order->payment_status = 'paid';
            }
            $order->status = $validated['status'];
            $order->save();
            return $order;
        });

        return response()->json([
            'message' => 'Cap nhat trang thai don hang thanh cong.',
            'data' => $donhang,
        ]);
    }

    public function cancelMyOrder(Request $request, int $id)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id) {
            $order = Donhang::where('user_id', $request->user()->id)
                ->whereKey($id)->lockForUpdate()->firstOrFail();
            if ((int) $order->status === 5) return;
            abort_unless((int) $order->status === 1 && $order->payment_status === 'unpaid',
                422, 'Chi co the huy don chua thanh toan va dang cho xac nhan.');
            foreach ($order->items()->orderBy('product_id')->get() as $item) {
                \App\Models\Product::withTrashed()->whereKey($item->product_id)
                    ->increment('qty', (int) $item->qty);
            }
            $order->status = 5;
            $order->save();
        });
        return response()->json(['message' => 'Da huy don hang.']);
    }

    public function myOrder(Request $request, int $id)
    {
        $order = Donhang::where('user_id', $request->user()->id)->with('items')->findOrFail($id);
        $data = $order->only(['id', 'code', 'name', 'phone', 'email', 'address', 'note', 'status', 'subtotal', 'discount', 'total', 'payment_method', 'payment_status', 'created_at']);
        $data['items'] = $order->items->map(fn ($item) => $item->only(['id', 'product_id', 'item_name', 'price', 'qty', 'amount']));
        return response()->json(['data' => $data]);
    }

    // Lịch sử đơn hàng của người dùng đang đăng nhập
    public function myOrders(Request $request)
    {
        $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647']);
        $donhangs = Donhang::where(
            'user_id',
            $request->user()->id
        )
            ->latest('id')
            ->paginate(10);

        return \App\Http\Resources\DonhangResource::collection($donhangs);
    }
}