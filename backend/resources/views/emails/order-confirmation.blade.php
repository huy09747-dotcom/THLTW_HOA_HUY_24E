<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xác nhận đơn hàng {{ $displayCode }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f3f7f5;font-family:Arial,sans-serif;color:#183b33;line-height:1.6">
    <div style="max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #dce7e2;border-radius:12px">
        <h1 style="font-size:24px;margin:0 0 20px">HH Shop — Đã nhận đơn hàng</h1>
        <p>Xin chào {{ $order->name }},</p>
        <p>Cảm ơn bạn đã đặt hàng tại HH Shop.</p>
        <p>Mã đơn hàng: <strong>{{ $displayCode }}</strong></p>

        <h2 style="font-size:18px;margin-top:24px">Thông tin nhận hàng</h2>
        <p style="margin:8px 0">Địa chỉ: {{ $order->address }}</p>
        <p style="margin:8px 0">Số điện thoại: {{ $order->phone }}</p>

        <h2 style="font-size:18px;margin-top:24px">Sản phẩm đã đặt</h2>
        <table cellpadding="10" style="border-collapse:collapse;width:100%;font-size:14px">
            <thead>
                <tr style="background:#edf6f2;text-align:left">
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th style="text-align:right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr style="border-bottom:1px solid #e5ede9">
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td style="text-align:right;white-space:nowrap">{{ number_format($item->amount, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Tạm tính: {{ number_format($order->subtotal, 0, ',', '.') }} đ</p>
        <p>Giảm giá: − {{ number_format($order->discount, 0, ',', '.') }} đ</p>
        <p style="font-size:18px">Tổng tiền: <strong>{{ number_format($order->total, 0, ',', '.') }} đ</strong></p>
        <p>Phương thức thanh toán:
            <strong>{{ [
                'cod' => 'Thanh toán khi nhận hàng',
                'payos' => 'Thanh toán chuyển khoản',
            ][$order->payment_method] ?? $order->payment_method }}</strong>
        </p>
        <p style="color:#60766d;font-size:14px">Bạn có thể xem trạng thái thanh toán và tiến trình giao hàng trong chi tiết đơn hàng.</p>
        <p>
            <a href="{{ rtrim(config('payments.frontend'), '/').($order->guest_session_id ? '/guest-orders/' : '/orders/').$order->id }}"
               style="display:inline-block;padding:12px 20px;background:#176454;color:#ffffff;text-decoration:none;border-radius:8px">
                Xem đơn hàng
            </a>
        </p>
    </div>
</body>
</html>
