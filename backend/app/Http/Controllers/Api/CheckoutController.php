<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\ChiTietGioHang;
use App\Models\Donhang;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request)
    {
        $method=$request->validated('payment_method','cod');
        app(\App\Services\SandboxPayment::class)->ensureConfigured($method);
        $order = DB::transaction(function () use ($request) {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

            $items = ChiTietGioHang::whereHas('giohang', function ($query) use ($request) {
                $query->where('nguoidung_id', $request->user()->id);
            })->orderBy('sanpham_id')->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Gio hang dang trong.']);
            }

            $products = Product::whereIn('id', $items->pluck('sanpham_id'))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $lines = [];
            $total = 0;

            foreach ($items as $item) {
                $product = $products->get($item->sanpham_id);
                $qty = (int) $item->soluong;
                if (! $product || (int) $product->status !== 1 || ! $product->is_available
                    || $product->final_price === null || $product->final_price < 0
                    || $qty < 1 || $qty > 999 || $qty > $product->qty) {
                    throw ValidationException::withMessages([
                        'cart' => 'San pham khong kha dung hoac khong du ton kho. Vui long kiem tra gio hang.',
                    ]);
                }

                $amount = (int) $product->final_price * $qty;
                $total += $amount;
                $lines[] = [
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'price' => (int) $product->final_price,
                    'qty' => $qty,
                    'amount' => $amount,
                ];
                $product->qty -= $qty;
                $product->save();
            }

            $coupon = app(\App\Services\CouponDiscount::class)->calculate($request->validated('coupon_code'), $total);
            if ($request->validated('payment_method', 'cod') === 'payos' && $total === $coupon['discount']) {
                throw ValidationException::withMessages(['payment_method' => 'Đơn được giảm toàn bộ tiền, vui lòng chọn thanh toán khi nhận hàng.']);
            }
            $order = Donhang::create(array_merge($request->validated(), [
                'user_id' => $request->user()->id,
                'code' => 'HH-'.strtoupper((string) Str::ulid()),
                'status' => 1,
                'order_type' => 'delivery',
                'subtotal' => $total,
                'discount' => $coupon['discount'],
                'total' => $total - $coupon['discount'],
                'payment_method' => $request->validated('payment_method','cod'),
                'payment_status' => 'unpaid',
            ]));
            $order->items()->createMany($lines);
            if ($coupon['coupon']) {
                DB::table('coupon_usages')->insert([
                    'coupon_id'=>$coupon['coupon']->id, 'user_id'=>$request->user()->id,
                    'order_id'=>$order->id, 'discount'=>$coupon['discount'],
                    'created_at'=>now(), 'updated_at'=>now(),
                ]);
            }
            ChiTietGioHang::whereIn('id', $items->pluck('id'))->delete();

            return $order;
        }, 3);

        $emailStatus='queued';
        try { \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\OrderConfirmation($order)); }
        catch (\Throwable $e) { report($e); $emailStatus='failed'; }
        return response()->json([
            'message' => 'Dat hang thanh cong.',
            'data' => array_merge($order->only(['id', 'code', 'total', 'payment_method', 'payment_status']), ['email_status'=>$emailStatus]),
        ], 201);
    }
}
