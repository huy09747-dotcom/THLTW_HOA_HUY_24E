# Đồng bộ database cho nhóm — 12/10/2026

## Thay đổi cần đồng bộ

Phần khuyến mãi đã bổ sung một cột vào bảng `sanpham`:

| Cột | Kiểu | Mặc định | Ý nghĩa |
| --- | --- | --- | --- |
| discount_percent | TINYINT UNSIGNED, NOT NULL | 0 | Phần trăm giảm giá; API kiểm tra từ 0 đến 100 |

Các phần quản lý người dùng, Post, Menu và bộ lọc Shop vừa làm sử dụng bảng/cột sẵn có, không bổ sung bảng hoặc cột mới.

## Quy ước giá thống nhất

- `price_buy`: giá nhập.
- `price_sale`: giá bán trước khuyến mãi.
- `discount_percent`: phần trăm khuyến mãi.
- `final_price`: giá tính trong Model và trả qua API, không phải cột database.
- Công thức: `round(price_sale * (100 - discount_percent) / 100)`.
- Giá bán và giá sau giảm không được thấp hơn giá nhập.
- Sản phẩm sale có `discount_percent > 0` và có giá bán.
- Không dùng điều kiện `price_sale < price_buy` để nhận diện sale.

## Máy đã có database

Lấy cùng phiên bản code của nhóm, bật MySQL và kiểm tra `.env` trỏ đúng database local.

Trong HeidiSQL, chọn database của project rồi chạy:

```sql
SHOW COLUMNS FROM sanpham LIKE 'discount_percent';
```

### Nếu chưa có cột: chọn một trong hai cách

**Cách 1 — migration Laravel:** mở terminal tại thư mục `backend` và chạy:

```powershell
php artisan migrate --path=database/migrations/2026_10_12_000001_add_discount_percent_to_sanpham.php
```

**Cách 2 — SQL:** mở `docs/DATABASE_UPDATE_2026_10_12.sql` trong HeidiSQL, chọn đúng database rồi chạy. Script kiểm tra cột trước khi thêm nên có thể chạy lại khi cột đã tồn tại. Bảng `sanpham` phải có sẵn.

Chỉ cần một cách. Nếu đã thêm bằng SQL, không chạy lại migration này: migration hiện tại thêm cột trực tiếp và sẽ báo trùng cột. Cách SQL không ghi lịch sử vào bảng `migrations`.

### Nếu cột đã có

Không cần thêm lại. Kiểm tra kiểu và mặc định theo bảng ở đầu tài liệu. Nếu khác, cần đối chiếu trước khi thay đổi; script cập nhật không tự sửa cột đã tồn tại.

Bản ghi cũ nhận mức giảm 0 khi thêm cột. Muốn kiểm tra nhóm sale, vào Admin → Sản phẩm → Sửa và nhập phần trăm khuyến mãi phù hợp.

## Máy tạo database mới

File `docs/THLTW_DB_HOA_HUY_24E.sql` đã có cột `discount_percent`. Sau khi import cấu trúc này, không chạy lại migration thêm cột trên. Không import lại toàn bộ cấu trúc để cập nhật database đang dùng.

## Code phải đồng bộ cùng database

Lấy cùng phiên bản repository, đặc biệt:

- `backend/app/Models/Product.php`
- `backend/app/Http/Requests/ProductRequest.php`
- `backend/app/Http/Resources/ProductResource.php`
- `backend/app/Http/Controllers/Api/ProductController.php`
- `backend/app/Http/Controllers/Api/CartController.php`
- `backend/app/Http/Controllers/Api/CheckoutController.php`
- Phần frontend hiển thị giá và nhập phần trăm khuyến mãi.

Không chỉ thay riêng ProductController bằng bản bỏ `discount_percent`, vì giá lọc, giá hiển thị và giá đặt hàng sẽ không cùng quy ước.

## Kiểm tra sau cập nhật

- Shop → sản phẩm sale: API `/api/products?status=1&on_sale=1` không báo thiếu cột.
- Shop → lọc khoảng giá: kết quả dùng giá sau khuyến mãi.
- Ví dụ giá nhập 8.000.000, giá bán 10.000.000, giảm 10%: giá cuối 9.000.000 đồng.
- Không chạy `migrate:fresh` hoặc `migrate:refresh` để đồng bộ thay đổi này.

Tài liệu và script này phục vụ cập nhật trên máy thành viên; việc tạo file không tự chạy SQL lên database.
