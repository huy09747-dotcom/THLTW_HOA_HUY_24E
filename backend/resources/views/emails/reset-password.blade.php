<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HH Shop - Đặt lại mật khẩu</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f3f7f5;font-family:Arial,sans-serif;color:#183b33;line-height:1.7">
    <div style="max-width:600px;margin:0 auto;padding:28px;background:#ffffff;border:1px solid #dce7e2;border-radius:12px">
        <p style="font-weight:bold;color:#176454">HH SHOP</p>
        <h1 style="font-size:24px">Xin chào {{ $name }},</h1>
        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu tài khoản HH Shop của bạn.</p>
        <p style="margin:24px 0">
            <a href="{{ $url }}" style="display:inline-block;padding:12px 24px;border-radius:8px;background:#176454;color:#ffffff;text-decoration:none;font-weight:bold">
                Đặt lại mật khẩu
            </a>
        </p>
        <p>Liên kết có hiệu lực trong {{ $minutes }} phút và chỉ dùng được một lần.</p>
        <p>Nếu bạn không yêu cầu, hãy bỏ qua email này. Mật khẩu hiện tại vẫn được giữ nguyên.</p>
        <p>HH Shop</p>
        <div style="border-top:1px solid #dce7e2;margin-top:24px;padding-top:16px;font-size:13px;color:#60766d">
            <p>Nếu không bấm được nút “Đặt lại mật khẩu”, hãy sao chép và dán liên kết bên dưới vào trình duyệt:</p>
            <a href="{{ $url }}" style="color:#176454;word-break:break-all">{{ $url }}</a>
        </div>
    </div>
</body>
</html>
