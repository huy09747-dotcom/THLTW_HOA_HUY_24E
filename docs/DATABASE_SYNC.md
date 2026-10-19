# Đồng bộ database cho thành viên nhóm

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
