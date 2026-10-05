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
                    || $product->price_sale === null || $product->price_sale < 0
                    || $qty < 1 || $qty > 999 || $qty > $product->qty) {
                    throw ValidationException::withMessages([
                        'cart' => 'San pham khong kha dung hoac khong du ton kho. Vui long kiem tra gio hang.',
                    ]);
                }

                $amount = (int) $product->price_sale * $qty;
                $total += $amount;
                $lines[] = [
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'price' => (int) $product->price_sale,
                    'qty' => $qty,
                    'amount' => $amount,
                ];
                $product->qty -= $qty;
                $product->save();
            }

            $order = Donhang::create(array_merge($request->validated(), [
                'user_id' => $request->user()->id,
                'code' => 'HH-'.strtoupper((string) Str::ulid()),
                'status' => 2,
                'order_type' => 'delivery',
                'subtotal' => $total,
                'discount' => 0,
                'total' => $total,
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
            ]));
            $order->items()->createMany($lines);
            ChiTietGioHang::whereIn('id', $items->pluck('id'))->delete();

            return $order;
        }, 3);

        return response()->json([
            'message' => 'Dat hang thanh cong.',
            'data' => $order->only(['id', 'code', 'total', 'payment_method', 'payment_status']),
        ], 201);
    }
}
