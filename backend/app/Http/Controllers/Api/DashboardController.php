<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donhang;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data=$request->validate(['from'=>'nullable|date','to'=>'nullable|date|after_or_equal:from']);
        $from=Carbon::parse($data['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to=Carbon::parse($data['to'] ?? now()->toDateString())->endOfDay();
        abort_if($from->gt($to) || $from->diffInDays($to)>366,422,'Chọn khoảng thời gian tối đa 366 ngày.');
        $orders=Donhang::whereBetween('created_at',[$from,$to]);
        $completed=(clone $orders)->where('status',4)->where('payment_status','paid');
        $daily=(clone $completed)->selectRaw('DATE(created_at) as day, SUM(total) as revenue, COUNT(*) as orders')->groupByRaw('DATE(created_at)')->orderBy('day')->get();
        $top=DB::table('chitietdonhang as i')->join('donhang as o','i.order_id','=','o.id')
            ->whereNull('o.deleted_at')->where('o.status',4)->where('o.payment_status','paid')
            ->whereBetween('o.created_at',[$from,$to])->selectRaw('i.product_id, MAX(i.item_name) as name, SUM(i.qty) as qty')
            ->groupBy('i.product_id')->orderByDesc('qty')->limit(5)->get();
        return response()->json(['data'=>[
            'orders'=>(clone $orders)->count(), 'revenue'=>(int)(clone $completed)->sum('total'),
            'pending'=>(clone $orders)->where('status',1)->count(),
            'cancelled'=>(clone $orders)->where('status',5)->count(),
            'statuses'=>(clone $orders)->selectRaw('status, COUNT(*) as total')->groupBy('status')->get(),
            'daily'=>$daily, 'top_products'=>$top,
            'low_stock'=>Product::where('status',1)->where('qty','<=',5)->orderBy('qty')->limit(10)->get(['id','name','qty']),
        ]]);
    }
}
