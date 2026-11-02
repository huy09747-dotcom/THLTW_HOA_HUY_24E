<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donhang;
use App\Models\Product;
use App\Services\PayosPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestCartController extends Controller
{
    public function session()
    {
        $token = bin2hex(random_bytes(32));
        DB::table('guest_sessions')->insert(['token_hash' => hash('sha256', $token), 'items' => '[]', 'expires_at' => now()->addDays(30), 'created_at' => now(), 'updated_at' => now()]);

        return response()->json(['token' => $token], 201);
    }

    public function index(Request $r)
    {
        $session = DB::table('guest_sessions')->find($r->attributes->get('guest_session')->id);
        $items = json_decode($session->items ?? '[]', true);
        $products = Product::whereIn('id', array_column($items, 'product_id'))->get()->keyBy('id');
        $rows = [];
        $total = 0;
        $count = 0;
        foreach ($items as $item) {
            $p = $products->get($item['product_id']);
            $qty = $item['quantity'];
            $available = $p && $p->status == 1 && $p->is_available && $p->final_price !== null && $qty <= $p->qty;
            $amount = $available ? $p->final_price * $qty : 0;
            $total += $amount;
            $count += $qty;
            $rows[] = ['id' => $item['product_id'], 'product_id' => $item['product_id'], 'name' => $p?->name ?? 'Sản phẩm không còn khả dụng', 'slug' => $p?->slug, 'image' => $p?->image, 'price' => $p?->final_price, 'quantity' => $qty, 'stock' => $p?->qty ?? 0, 'available' => (bool) $available, 'subtotal' => $amount];
        }

        return response()->json(['data' => $rows, 'count' => $count, 'total' => $total]);
    }

    public function change(Request $r, ?int $id = null)
    {
        $data = $r->isMethod('delete') ? [] : $r->validate(['product_id' => $id ? 'sometimes|integer|min:1' : 'required|integer|min:1', 'quantity' => 'required|integer|between:1,999']);
        $productId = $id ?? $data['product_id'];
        DB::transaction(function () use ($r, $data, $productId) {
            $session = DB::table('guest_sessions')->where('id', $r->attributes->get('guest_session')->id)->lockForUpdate()->first();
            $items = collect(json_decode($session->items ?? '[]', true))->keyBy('product_id');
            if ($r->isMethod('delete')) {
                $items->forget($productId);
            } else {
                $p = Product::where('status', 1)->findOrFail($productId);
                $qty = $data['quantity'] + ($r->isMethod('post') ? ($items->get($productId)['quantity'] ?? 0) : 0);
                abort_unless($p->is_available && $p->final_price !== null && $qty <= min(999, $p->qty), 422, 'Sản phẩm không khả dụng hoặc không đủ tồn kho.');
                abort_if(! $items->has($productId) && $items->count() >= 100, 422, 'Giỏ hàng tối đa 100 sản phẩm.');
                $items->put($productId, ['product_id' => $productId, 'quantity' => $qty]);
            }
            DB::table('guest_sessions')->where('id', $session->id)->update(['items' => $items->values()->toJson(), 'updated_at' => now()]);
        });

        return $this->index($r);
    }

    private function order(Request $r, int $id): Donhang
    {
        return Donhang::where('guest_session_id', $r->attributes->get('guest_session')->id)->findOrFail($id);
    }

    public function show(Request $r, int $id)
    {
        return response()->json(['data' => $this->order($r, $id)->load('items')]);
    }

    public function payment(Request $r, int $id)
    {
        $r->validate(['return_origin' => 'nullable|string|max:255']);

        return response()->json(['data' => app(PayosPayment::class)->start($this->order($r, $id), $r->input('return_origin'))]);
    }

    public function verify(Request $r, int $id)
    {
        $order = $this->order($r, $id);
        abort_unless($order->payment_method === 'payos', 422);
        app(PayosPayment::class)->verify($order);

        return response()->json(['data' => ['payment_status' => $order->fresh()->payment_status]]);
    }
}
