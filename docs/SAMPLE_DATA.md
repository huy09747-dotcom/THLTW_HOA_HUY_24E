# Dữ liệu mẫu dùng chung

Seeder: `backend/database/seeders/ShopSampleSeeder.php`.

- 5 danh mục, 15 sản phẩm (mỗi danh mục 3 sản phẩm).
- 1 thương hiệu HH Shop, dùng lại nếu đã có.

## Chạy trên Laragon hoặc XAMPP

1. Lấy code có file Seeder.
2. Bật MySQL, cấu hình `backend/.env` theo database local của mình.
3. Database cần có cấu trúc hiện tại của project. SQL cấu trúc nằm ở `docs/THLTW_DB_HOA_HUY_24E.sql`; không import đè database đang có.
4. Trong thư mục `backend`, chạy:

```sh
php artisan db:seed --class=ShopSampleSeeder
```

Không cần migration cho dữ liệu này. Không chạy `migrate:fresh`, `migrate:refresh` hay Seeder mặc định để thêm bộ mẫu.

## Cách hoạt động

- Tìm danh mục bằng slug, thương hiệu bằng tên, sản phẩm bằng SKU.
- Tự lấy ID trên từng máy để liên kết, không cố định ID.
- Chỉ thêm bản ghi chưa có; không ghi đè hoặc xóa dữ liệu cũ.
- Chạy lại không tạo trùng; sản phẩm đã xóa mềm không tự khôi phục.
- Nếu danh mục hoặc thương hiệu dùng cho bộ mẫu đã xóa mềm, Seeder dừng và hoàn tác giao dịch.
- Giá, tồn kho dùng để kiểm thử. Ảnh dùng URL từ DummyJSON, cần Internet.
- Không tạo tài khoản, đổi mật khẩu hay chia sẻ `.env`.

Frontend tiếp tục lấy dữ liệu qua Laravel API.
