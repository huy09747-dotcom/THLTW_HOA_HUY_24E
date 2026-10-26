# Đồng bộ database cho thành viên nhóm

## Bản cập nhật review, thanh toán, tin tức ngày 26/10

Xem `COMMERCE_SANDBOX.md`. Tại thư mục `backend`:

```powershell
php artisan migrate --path=database/migrations/2026_10_26_000001_create_reviews_and_payments.php
php artisan migrate --path=database/migrations/2026_10_26_000002_add_review_visibility.php
php artisan db:seed --class=NewsSampleSeeder
php artisan config:clear
```

Cổng sandbox cần khóa riêng trong `.env`; email cần SMTP và worker database queue. Không dùng dữ liệu thanh toán thật.

Nhận phiên bản code và các file SQL mới nhất của nhóm trước khi chạy. Các lệnh bên dưới dùng MySQL CLI trong terminal Laragon, tại thư mục gốc repository (nơi có `backend`, `frontend`, `docs`). Nếu dùng tên database hoặc tài khoản khác, thay tương ứng theo `.env` của thành viên.

## Đã có database dự án

```powershell
mysql -u root -p --default-character-set=utf8mb4 thltw_huy_hoa_24e --execute="source docs/DATABASE_UPDATE_2026_10_19.sql"
```

Nhập mật khẩu MySQL khi được hỏi; nếu tài khoản root không đặt mật khẩu thì nhấn Enter. Script tạo bảng `cauhinh` nếu thiếu và thêm cấu hình mặc định nếu bảng trống. Bảng và dữ liệu có sẵn không bị xóa hoặc ghi đè. Nếu bảng `cauhinh` có sẵn nhưng khác cấu trúc, cần đối chiếu các cột; script không tự ALTER cấu trúc khác.

Nếu chưa nhận bản cập nhật `discount_percent` trước đó, chạy thêm:

```powershell
mysql -u root -p --default-character-set=utf8mb4 thltw_huy_hoa_24e --execute="source docs/DATABASE_UPDATE_2026_10_12.sql"
```

Sau đó vào thư mục backend để xóa cấu hình Laravel đã cache:

```powershell
cd backend
php artisan config:clear
```

Có thể mở `DATABASE_UPDATE_2026_10_19.sql` bằng HeidiSQL, chọn đúng database dự án và chạy toàn bộ nếu không dùng CLI.

## Máy chưa có database

File SQL nền tự tạo database `thltw_huy_hoa_24e`. Tại thư mục gốc repository:

```powershell
mysql -u root -p --default-character-set=utf8mb4 --execute="source docs/THLTW_DB_HOA_HUY_24E.sql"
mysql -u root -p --default-character-set=utf8mb4 thltw_huy_hoa_24e --execute="source docs/DATABASE_UPDATE_2026_10_12.sql"
mysql -u root -p --default-character-set=utf8mb4 thltw_huy_hoa_24e --execute="source docs/DATABASE_UPDATE_2026_10_19.sql"
```

Các lệnh này đồng bộ cấu trúc và cấu hình mặc định, không sao chép dữ liệu khách hàng, đơn hàng, tài khoản hoặc nội dung trang từ máy khác. Trang Giới thiệu/Chính sách/Liên hệ dùng bảng `baiviet` sẵn có; nhập và xuất bản nội dung trong `/admin/pages` trên database của thành viên.

## Nạp nội dung mẫu cho ba trang và cấu hình

Sau khi nhận code mới, có bảng `cauhinh` và có ít nhất một tài khoản admin đang hoạt động, chạy trong thư mục `backend` (dùng được trong XAMPP Shell):

```powershell
php artisan db:seed --class=SiteContentSampleSeeder
```

Seeder tạo 3 trang, mỗi trang 10 mục và xuất bản ngay; bổ sung các trường cấu hình còn trống. Nội dung nằm trong `backend/database/seeders/data/site-content.json` và được ghi vào database, frontend đọc qua API. Không tự ghi đè trang hoặc giá trị cấu hình có sẵn; chạy lại không tạo thêm trang trùng slug.

Email `.example`, số điện thoại `0000 000 000`, địa chỉ phòng thực hành và liên kết `example.com` là dữ liệu minh họa. Logo mẫu được lưu trong `frontend/public/hh-shop-demo.svg`. URL frontend mặc định là `http://localhost:3000`; nếu dùng cổng khác, đặt `FRONTEND_URL` trong `.env` backend trước khi seed hoặc sửa URL logo/favicon ở trang quản trị sau đó.
