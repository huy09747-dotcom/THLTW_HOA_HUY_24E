<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockController extends Controller
{
    public function index(Request $r)
    {
        $data = $r->validate(['page' => 'sometimes|integer|min:1|max:2147483647', 'search' => 'nullable|string|max:255', 'type' => 'nullable|in:in,out', 'from' => 'nullable|date', 'to' => 'nullable|date|after_or_equal:from']);
        $q = DB::table('stock_documents');
        if (! empty($data['search'])) {
            $q->where(fn ($q) => $q->where('reference', 'like', '%'.$data['search'].'%')->orWhere('reason', 'like', '%'.$data['search'].'%'));
        }
        if (! empty($data['type'])) {
            $q->where('type', $data['type']);
        }
        if (! empty($data['from'])) {
            $q->whereDate('created_at', '>=', $data['from']);
        }
        if (! empty($data['to'])) {
            $q->whereDate('created_at', '<=', $data['to']);
        }
        $rows = $q->orderByDesc('id')->paginate(10);
        $lines = DB::table('stock_document_items')->whereIn('document_id', $rows->pluck('id'))->get()->groupBy('document_id');
        $rows->getCollection()->transform(function ($row) use ($lines) {
            $row->items = $lines->get($row->id, collect());

            return $row;
        });

        return response()->json($rows);
    }

    public function products(Request $r)
    {
        $r->validate(['page' => 'sometimes|integer|min:1', 'search' => 'nullable|string|max:255']);
        $q = Product::query();
        if ($r->filled('search')) {
            $q->where('name', 'like', '%'.$r->search.'%');
        }

        return response()->json($q->orderBy('name')->paginate(100, ['id', 'name', 'qty', 'price_buy']));
    }

    public function store(Request $r)
    {
        $data = $r->validate(['type' => 'required|in:in,out', 'reason' => 'required|string|max:1000', 'items' => 'required|array|min:1|max:50', 'items.*.product_id' => 'required|integer|distinct|min:1', 'items.*.qty' => 'required|integer|min:1|max:1000000', 'items.*.unit_cost' => 'required|integer|min:0|max:1000000000']);
        $id = DB::transaction(function () use ($data, $r) {
            $products = Product::whereIn('id', array_column($data['items'], 'product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $id = DB::table('stock_documents')->insertGetId(['type' => $data['type'], 'reference' => 'KHO-'.Str::ulid(), 'reason' => $data['reason'], 'created_by' => $r->user()->id, 'created_at' => now(), 'updated_at' => now()]);
            foreach ($data['items'] as $line) {
                $p = $products->get($line['product_id']);
                abort_unless($p, 422, 'Sản phẩm không tồn tại.');
                $before = (int) $p->qty;
                $after = $data['type'] === 'in' ? $before + $line['qty'] : $before - $line['qty'];
                abort_if($after < 0 || $after > 4294967295, 422, 'Số lượng xuất vượt tồn kho hoặc số lượng nhập quá lớn.');
                $p->qty = $after;
                $p->save();
                DB::table('stock_document_items')->insert(['document_id' => $id, 'product_id' => $p->id, 'product_name' => $p->name, 'qty' => $line['qty'], 'unit_cost' => $line['unit_cost'], 'stock_before' => $before, 'stock_after' => $after]);
            }

            return $id;
        }, 3);

        return response()->json(['message' => 'Đã ghi nhận phiếu kho.', 'data' => ['id' => $id]], 201);
    }
}
