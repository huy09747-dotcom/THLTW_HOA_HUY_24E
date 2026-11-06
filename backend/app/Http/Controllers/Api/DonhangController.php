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

        $request->validate(['search'=>'nullable|string|max:255','status'=>'nullable|integer|between:1,5','from'=>'nullable|date','to'=>'nullable|date|after_or_equal:from']);
        $query=\App\Support\AdminFilters::apply(Donhang::with('nguoidung'),$request,['code','name','phone','email']);
        if ($request->filled('search') && preg_match('/^HH-(\d+)$/i',$request->search,$match)) {
            $query=Donhang::with('nguoidung')->whereKey((int)$match[1]);
            if ($request->filled('status')) $query->where('status',$request->status);
        }
        if ($request->filled('from')) $query->whereDate('created_at','>=',$request->from);
        if ($request->filled('to')) $query->whereDate('created_at','<=',$request->to);
        $donhangs = $query
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

        $donhang->setAttribute('history', \Illuminate\Support\Facades\DB::table('order_status_history')->where('order_id',$id)->orderBy('id')->get());
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
            $allowed=[1=>[1,2,5],2=>[2,3,5],3=>[3,4],4=>[4],5=>[5]];
            abort_unless(in_array((int)$validated['status'],$allowed[(int)$order->status] ?? [],true),422,'Không thể chuyển trạng thái theo thứ tự này.');
            abort_if((int)$validated['status']===2 && $order->payment_method!=='cod' && $order->payment_status!=='paid',422,'Đơn chuyển khoản cần thanh toán trước khi xác nhận.');
            abort_if((int)$validated['status']===4 && $order->payment_method!=='cod' && $order->payment_status!=='paid',422,'Đơn chưa thanh toán.');
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