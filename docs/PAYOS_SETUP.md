# Thanh toán payOS — HH Shop

## Đã tích hợp

- Chọn **Chuyển khoản QR / payOS** khi đặt hàng.
- Mở chi tiết đơn, chọn **Tạo liên kết thanh toán**, rồi **Mở mã QR thanh toán**.
- Trang payOS hiển thị QR và số tiền lấy từ đơn trong database; không dùng số tiền do trình duyệt gửi lên.
- Tiền vào tài khoản ngân hàng đã liên kết với kênh payOS. Số tài khoản không được hard-code trong frontend.
- Webhook xác thực HMAC SHA256; backend còn truy vấn payOS để đối chiếu mã giao dịch, tổng tiền, số tiền đã nhận và trạng thái PAID.
- Chỉ cập nhật payment_status sang paid, không tự chuyển trạng thái giao hàng.
- Gửi webhook nhiều lần không ghi nhận thanh toán nhiều lần.
- Trang chi tiết kiểm tra mỗi 10 giây khi đang mở, tối đa khoảng 10 phút; có nút kiểm tra thủ công. Webhook hoạt động độc lập với trình duyệt.
- Không đổi schema, không cần chạy migration.

## Cấu hình

Khóa thật chỉ đặt trong `backend/.env` (không commit):

```dotenv
PAYOS_CLIENT_ID=...
PAYOS_API_KEY=...
PAYOS_CHECKSUM_KEY=...
FRONTEND_URL=http://127.0.0.1:3000
```

Dùng đúng địa chỉ frontend trên để phiên đăng nhập vẫn còn sau khi quay về từ payOS.
Sau khi sửa .env, chạy `php artisan config:clear` tại thư mục backend.
File `.env.payments.example` chỉ chứa tên biến rỗng cho người cùng nhóm.

## Webhook trên máy local

Route: `POST /api/payments/payos/webhook`.

Đã chuẩn bị proxy chỉ chuyển tiếp route này; các đường dẫn khác trả 404. Proxy không đọc khóa payOS, không in nội dung giao dịch và không chuyển lỗi chi tiết từ Laravel ra Internet.

Tại thư mục backend, mở terminal riêng:

```powershell
node scripts/payos-webhook-proxy.cjs
```

Proxy nghe tại `http://127.0.0.1:8010`, chuyển tiếp đến Laravel `http://127.0.0.1:8000`.
Cần đường hầm HTTPS tới proxy trước khi đăng ký webhook. Người dùng đã cho phép mở Cloudflare; payOS đã xác nhận webhook thành công.

URL của phiên hiện tại:
`https://rochester-june-priced-disclaimer.trycloudflare.com/api/payments/payos/webhook`

Để mở lại sau khi tắt máy, chạy proxy ở terminal trên, rồi mở terminal khác:

```powershell
& "C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8010 --no-autoupdate
```

Lấy URL HTTPS mới được in ra, thêm `/api/payments/payos/webhook`, rồi đăng ký lại bằng lệnh bên dưới.

Sau khi có URL HTTPS, đăng ký bằng lệnh (thay địa chỉ mẫu):

```powershell
php scripts/register-payos-webhook.php "https://TEN-MIEN/api/payments/payos/webhook"
```

Lệnh đọc khóa từ .env, gọi API confirm-webhook; payOS gửi dữ liệu mẫu có chữ ký để kiểm tra endpoint. Lệnh không tạo thanh toán và không chuyển tiền.

Giữ Laravel, proxy và đường hầm chạy để nhận webhook. Nếu dùng đường hầm tạm, địa chỉ có thể đổi khi khởi động lại, cần đăng ký URL mới. Khi webhook chưa kết nối, nút kiểm tra và kiểm tra định kỳ trên trang đơn vẫn đối chiếu với API payOS.

## Kiểm tra

Đã chạy PHPUnit bằng SQLite :memory: và HTTP giả lập: tạo link đúng tiền, tái sử dụng link, quyền sở hữu đơn, từ chối chữ ký giả/sai tiền, kiểm tra kết quả server, webhook trùng. ESLint các file frontend liên quan đạt.

Đã kiểm tra API payOS thật bằng GET mã thanh toán không tồn tại: API phản hồi mã 101 (Mã thanh toán không tồn tại). Đây là kiểm tra kết nối, không phải giao dịch thử thành công.

Chưa thực hiện chuyển tiền thật. Sau khi webhook được đăng ký, người dùng cần tự thực hiện một giao dịch phù hợp để kiểm tra toàn bộ luồng. payOS là môi trường tiền thật.

## File chính

- `backend/app/Services/PayosPayment.php`: tạo link, chữ ký, xác minh và ghi nhận thanh toán.
- `backend/app/Http/Controllers/Api/PaymentController.php`: API tạo/kiểm tra thanh toán và nhận webhook.
- `backend/config/payments.php`: đọc cấu hình từ .env.
- `backend/routes/api.php`: route webhook ở cuối file.
- `frontend/src/components/shop/OrderPayment.js`: giao diện thanh toán và kiểm tra trạng thái.
- `backend/tests/Feature/PayosPaymentTest.php`: kiểm thử bảo vệ luồng thanh toán.

Tài liệu API: https://payos.vn/docs/api/
