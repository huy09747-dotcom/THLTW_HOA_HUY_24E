<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietGioHang;
use App\Services\CouponDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['page'=>'sometimes|integer|min:1|max:2147483647']);
        $coupons = DB::table('coupons')->select('coupons.*')->selectSub(
            DB::table('coupon_usages')->selectRaw('COUNT(*)')->whereColumn('coupon_id', 'coupons.id'), 'used'
        )->orderByDesc('id')->paginate(10);
        return response()->json($coupons);
    }

    public function save(Request $request, ?int $id = null)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        $data = $request->validate([
            'code' => ['required','string','max:40','regex:/^[A-Z0-9_-]+$/',Rule::unique('coupons')->ignore($id)],
            'type' => 'required|in:percent,fixed', 'value' => 'required|integer|min:1|max:1000000000',
            'min_order' => 'required|integer|min:0|max:1000000000',
            'max_discount' => 'nullable|integer|min:1|max:1000000000',
            'usage_limit' => 'nullable|integer|min:1|max:1000000',
            'starts_at' => 'nullable|date', 'ends_at' => 'nullable|date|after:starts_at',
            'active' => 'required|boolean',
        ]);
        foreach (['starts_at', 'ends_at'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = \Carbon\Carbon::parse($data[$field])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            }
        }
        if ($data['type'] === 'percent' && $data['value'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['value'=>'Phần trăm giảm tối đa là 100%.']);
        }
        DB::transaction(function () use ($id, $data) {
            if ($id) {
                abort_unless(DB::table('coupons')->where('id',$id)->lockForUpdate()->first(),404);
                DB::table('coupons')->where('id',$id)->update($data + ['updated_at'=>now()]);
            } else DB::table('coupons')->insert($data + ['created_at'=>now(),'updated_at'=>now()]);
        });
        return response()->json(['message'=>'Đã lưu mã giảm giá.']);
    }

    public function preview(Request $request, CouponDiscount $discounts)
    {
        $data = $request->validate(['coupon_code'=>'required|string|max:40']);
        $items = ChiTietGioHang::whereHas('giohang', fn($q)=>$q->where('nguoidung_id',$request->user()->id))->with('sanpham')->get();
        $subtotal=0;
        foreach ($items as $item) {
            $p=$item->sanpham;
            abort_unless($p && $p->status==1 && $p->is_available && $p->final_price !== null && $item->soluong <= $p->qty,422,'Vui lòng kiểm tra lại giỏ hàng.');
            $subtotal += $p->final_price * $item->soluong;
        }
        abort_unless($subtotal>0,422,'Giỏ hàng đang trống.');
        $result=DB::transaction(fn()=>$discounts->calculate($data['coupon_code'],$subtotal));
        return response()->json(['data'=>['code'=>$result['coupon']->code,'subtotal'=>$subtotal,'discount'=>$result['discount'],'total'=>$subtotal-$result['discount']]]);
    }
}
