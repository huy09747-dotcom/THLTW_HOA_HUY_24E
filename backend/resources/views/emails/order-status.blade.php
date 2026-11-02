<!doctype html>
<html lang="vi">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="font-family:Arial,sans-serif;background:#f3f7f5;padding:24px;color:#183b33;line-height:1.7">
    <div style="max-width:600px;background:white;margin:auto;padding:24px;border-radius:12px">
        <h1 style="font-size:24px">Cập nhật đơn hàng {{ $code }}</h1>
        <p>Xin chào {{ $order['name'] }},</p>
        <p>Trạng thái đơn hàng: <strong>{{ [1=>'Chờ xác nhận',2=>'Đã xác nhận — đang đóng gói',3=>'Đang giao',4=>'Hoàn thành',5=>'Đã hủy'][$order['status']] ?? 'Đang cập nhật' }}</strong></p>
        <p>Thanh toán: <strong>{{ ['paid'=>'Đã thanh toán','unpaid'=>'Chưa thanh toán','refunded'=>'Đã hoàn tiền'][$order['payment_status']] ?? 'Đang cập nhật' }}</strong></p>
        <p><a href="{{ rtrim(config('payments.frontend'),'/').(!empty($order['guest_session_id']) ? '/guest-orders/' : '/orders/').$order['id'] }}">Xem chi tiết đơn hàng</a></p>
        <p>Cảm ơn bạn đã mua sắm tại HH Shop.</p>
    </div>
</body>
</html>
