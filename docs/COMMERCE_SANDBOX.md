# Review, thanh toán sandbox, email và bài viết

## Cài đặt cho thành viên nhóm (XAMPP/Laragon)

Nhận code mới, cấu hình database trong `.env`, chạy từ thư mục `backend`:

```powershell
php artisan migrate --path=database/migrations/2026_10_26_000001_create_reviews_and_payments.php
php artisan migrate --path=database/migrations/2026_10_26_000002_add_review_visibility.php
php artisan db:seed --class=NewsSampleSeeder
php artisan config:clear
```

Migration chỉ tạo `product_reviews` và `payment_attempts`. Seeder tạo hai chủ đề Tin tức/Sự kiện và hai bài mẫu, không ghi đè nội dung đã có; yêu cầu ít nhất một admin hoạt động.

## Cấu hình sandbox trong backend/.env

```dotenv
FRONTEND_URL=http://localhost:3000
PAYMENT_CALLBACK_BASE=https://your-backend-tunnel.example

MOMO_PARTNER_CODE=
MOMO_ACCESS_KEY=
MOMO_SECRET_KEY=

PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
# Tỷ giá VND cho 1 USD do nhóm chọn để thử nghiệm; không phải tỷ giá thị trường.
PAYPAL_VND_PER_USD=25000

VNPAY_TMN_CODE=
VNPAY_HASH_SECRET=
```

Không commit `.env`. Chỉ dùng khóa sandbox do từng cổng cấp. Backend cố định các endpoint sandbox, không gọi endpoint production. Các phương thức thiếu khóa bị vô hiệu hóa trên form và bị API từ chối trước khi trừ kho/tạo đơn.

- MoMo: IPN `POST {PAYMENT_CALLBACK_BASE}/api/payments/momo/ipn` phải truy cập được từ Internet. Dùng ứng dụng/tài khoản MoMo thử nghiệm theo tài liệu của cổng.
- VNPAY: đăng ký IPN `GET {PAYMENT_CALLBACK_BASE}/api/payments/vnpay/ipn` trong cấu hình merchant sandbox. Thẻ nội địa dùng `vnp_BankCode=VNBANK`.
- PayPal: dùng sandbox business app để lấy client ID/secret và sandbox personal account để trả tiền. Tổng đơn gốc vẫn là VND; server chốt số cent USD theo tỷ giá cấu hình ở lần tạo giao dịch. Giao diện hiển thị USD trước khi mở cổng.
- URL frontend trả về phải được cổng chấp nhận; dùng địa chỉ frontend HTTPS phù hợp khi cần.
- Chọn phương thức ở giỏ hàng → tạo đơn → mở chi tiết → Tạo liên kết thanh toán → Đến cổng → quay về → Kiểm tra thanh toán. PayPal được capture và xác minh qua API server; MoMo/VNPAY chờ IPN đã kiểm chữ ký và số tiền. Query string do trình duyệt gửi về không được dùng để đánh dấu đã thanh toán.
- Một giao dịch cố định cho mỗi đơn, dùng lại khi nhấn nút nhiều lần. Chưa có luồng đổi cổng/thay phiên hết hạn, hoàn tiền hoặc hủy online tự động; không đổi `payment_status` thủ công để giả lập thành công. URL VNPAY hết hạn sau 15 phút.

## Email đơn hàng

Email được xếp vào database queue sau khi transaction đơn hàng đã commit. Lỗi xếp hàng không rollback đơn và được phản ánh qua `email_status`. Chạy worker từ `backend`:

```powershell
php artisan queue:work database --tries=3 --timeout=60
```

Bảng `jobs` phải có (đã có trên máy hiện tại). Máy mới có thể chạy riêng migration `0001_01_01_000002_create_jobs_table.php` nếu chưa có bảng.

Hiện `MAIL_MAILER=log` chỉ ghi email vào log, không gửi đến hộp thư. Để kiểm tra email thật, dùng SMTP sandbox của bạn, ví dụ cấu hình theo thông số nhà cung cấp:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=your-verified-sender@example.com
MAIL_FROM_NAME="HH Shop"
```

Sau khi đổi cấu hình: `php artisan config:clear`, `php artisan queue:restart`, chạy lại worker. Chỉ gửi mail thử đến hộp thư thuộc quyền kiểm soát của nhóm. Email xác nhận đặt hàng không có nghĩa đơn đã thanh toán.

## Chức năng

- Admin `/admin/reviews`: tìm theo tên sản phẩm/người viết/nội dung, lọc 1–5 sao và ẩn/hiện, phân trang 10 đánh giá, thống kê theo bộ lọc. Có nút ẩn/hiện và xóa vĩnh viễn với xác nhận. Không sửa nội dung hoặc điểm thay người viết.
- Review ẩn không xuất hiện trong danh sách công khai và không tính điểm trung bình. Người viết vẫn xem/sửa đánh giá của mình; sửa nội dung không tự bỏ trạng thái ẩn. Đánh giá mới mặc định hiển thị. Việc xóa vĩnh viễn cho phép người dùng tạo đánh giá mới; nên dùng ẩn khi cần kiểm duyệt thay vì xóa.
- Đã chạy migration hiển thị trên máy hiện tại. Kiểm tra sau cập nhật: 47 tests / 456 assertions đạt; ESLint và build frontend đạt. Chưa kiểm tra thủ công bằng trình duyệt.

- Review: đọc công khai có phân trang, số lượng và trung bình sao. Đăng nhập để tạo/cập nhật một đánh giá mỗi sản phẩm; chỉ xóa đánh giá của mình. Điểm 1–5, nội dung tối đa 2000 ký tự. Chưa yêu cầu đã mua hàng, không gắn nhãn người mua đã xác minh.
- Giỏ hàng: giữ luồng thêm/sửa số lượng/xóa và kiểm tra tồn kho đang có.
- Tin tức: `/news`, lọc theo chủ đề; chi tiết `/news/{slug}`. Admin dùng mục Bài viết và chọn chủ đề Tin tức/Sự kiện. Chỉ bài đã xuất bản thuộc chủ đề hoạt động được đọc công khai.

## Kiểm tra

```powershell
php artisan test --filter="CommerceTest|PublicNewsTest|CheckoutTest"
```

Kiểm thử HTTP giả lập xác minh chữ ký, số tiền, quyền truy cập và replay không thay thế giao dịch end-to-end trên cổng sandbox. Muốn kiểm tra toàn luồng cần khóa sandbox, callback công khai và tài khoản thử nghiệm; không tạo khóa giả.

Kết quả triển khai: toàn bộ backend đạt 45 tests / 431 assertions; ESLint các file frontend thay đổi đạt và build Next.js thành công. Migration đã chạy trên máy hiện tại; đã nạp hai chủ đề và hai bài mẫu. Chưa xác nhận thanh toán end-to-end với tài khoản sandbox hay gửi SMTP vào hộp thư thực. Hiện mailer dùng `log`.

Tài liệu cổng:
- https://developers.momo.vn/v3/vi/docs/payment/api/wallet/onetime/
- https://developer.paypal.com/docs/api/orders/v2/
- https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
