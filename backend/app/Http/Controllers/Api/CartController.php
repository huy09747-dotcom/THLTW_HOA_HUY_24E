<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    private function checkUser(Request $request): void
    {
        abort_unless($request->user()->status === 1, 403, 'Tai khoan khong hoat dong.');
    }

    private function checkProduct(Product $product, int $quantity): void
    {
        if ((int) $product->status !== 1 || ! $product->is_available || $product->price_sale === null) {
            throw ValidationException::withMessages(['product_id' => 'San pham hien khong the dat mua.']);
        }
        if ($quantity > min(999, (int) $product->qty)) {
            throw ValidationException::withMessages(['quantity' => 'So luong vuot qua ton kho hoac gioi han 999.']);
        }
    }

    public function index(Request $request)
    {
        $this->checkUser($request);
        $items = ChiTietGioHang::whereHas('giohang', fn ($q) => $q->where('nguoidung_id', $request->user()->id))
            ->with('sanpham')
            ->orderBy('id')
            ->get()
            ->map(function ($item) {
                $product = $item->sanpham;
                $available = $product && (int) $product->status === 1 && $product->is_available
                    && $product->price_sale !== null && $item->soluong <= $product->qty;

                return [
                    'id' => $item->id,
                    'product_id' => $item->sanpham_id,
                    'name' => $product?->name ?? 'San pham khong con ton tai',
                    'image' => $product?->image,
                    'price' => $product?->price_sale,
                    'quantity' => (int) $item->soluong,
                    'stock' => (int) ($product?->qty ?? 0),
                    'available' => (bool) $available,
                    'subtotal' => $available ? (int) $product->price_sale * $item->soluong : 0,
                ];
            });

        return response()->json([
            'data' => $items,
            'total' => $items->sum('subtotal'),
            'count' => $items->sum('quantity'),
        ]);
    }

    public function store(CartItemRequest $request)
    {
        $this->checkUser($request);
        $data = $request->validated();
        DB::transaction(function () use ($request, $data) {
            // Khóa tài khoản để hai yêu cầu đồng thời không tạo giỏ/dòng trùng nhau.
            User::whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();
            $product = Product::whereKey($data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();
            $cart = GioHang::where('nguoidung_id', $request->user()->id)
                ->orderBy('id')
                ->first();
            $item = ChiTietGioHang::whereHas('giohang', fn ($q) => $q->where('nguoidung_id', $request->user()->id))
                ->where('sanpham_id', $product->id)
                ->first();
            $quantity = (int) $data['quantity'] + (int) ($item?->soluong ?? 0);
            $this->checkProduct($product, $quantity);
            if (! $cart) {
                $cart = new GioHang;
                $cart->nguoidung_id = $request->user()->id;
                $cart->save();
            }
            if (! $item) {
                $item = new ChiTietGioHang;
                $item->giohang_id = $cart->id;
                $item->sanpham_id = $product->id;
            }
            $item->soluong = $quantity;
            $item->save();
        });

        return $this->index($request)->setStatusCode(201);
    }

    public function update(CartItemRequest $request, $id)
    {
        $this->checkUser($request);
        DB::transaction(function () use ($request, $id) {
            User::whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();
            $item = ChiTietGioHang::whereHas('giohang', fn ($q) => $q->where('nguoidung_id', $request->user()->id))
                ->findOrFail($id);
            $product = Product::whereKey($item->sanpham_id)
                ->lockForUpdate()
                ->firstOrFail();
            $quantity = (int) $request->validated('quantity');
            $this->checkProduct($product, $quantity);
            $item->soluong = $quantity;
            $item->save();
        });

        return $this->index($request);
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser($request);
        DB::transaction(function () use ($request, $id) {
            User::whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();
            ChiTietGioHang::whereHas('giohang', fn ($q) => $q->where('nguoidung_id', $request->user()->id))
                ->findOrFail($id)
                ->delete();
        });

        return $this->index($request);
    }
}
