<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CouponDiscount
{
    public function calculate(?string $code, int $subtotal): array
    {
        if (!$code) return ['coupon' => null, 'discount' => 0];
        $coupon = DB::table('coupons')->where('code', strtoupper(trim($code)))->lockForUpdate()->first();
        if (!$coupon || !$coupon->active
            || ($coupon->starts_at && now()->lt($coupon->starts_at))
            || ($coupon->ends_at && now()->gt($coupon->ends_at))) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
        }
        if ($subtotal < $coupon->min_order) {
            throw ValidationException::withMessages(['coupon_code' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã.']);
        }
        if ($coupon->usage_limit !== null && DB::table('coupon_usages')->where('coupon_id', $coupon->id)->count() >= $coupon->usage_limit) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá đã hết lượt sử dụng.']);
        }
        $discount = $coupon->type === 'percent' ? (int) floor($subtotal * $coupon->value / 100) : (int) $coupon->value;
        if ($coupon->max_discount !== null) $discount = min($discount, (int) $coupon->max_discount);
        return ['coupon' => $coupon, 'discount' => min($subtotal, $discount)];
    }
}
