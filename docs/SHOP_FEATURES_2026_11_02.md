# Cập nhật 4 chức năng shop

## Cập nhật database cho thành viên nhóm

Từ thư mục backend chạy:

```powershell
php artisan migrate --path=database/migrations/2026_11_02_000001_create_shop_features.php
php artisan queue:work database --tries=3 --timeout=60
```

Nếu worker đang chạy thì dừng bằng Ctrl+C và chạy lại để nhận code mới.
Migration tạo `coupons`, `coupon_usages`, `favorites`. Không sửa hoặc xóa bảng cũ.

## Sử dụng

- `/admin/coupons`: tạo/sửa mã, giảm theo % hoặc số tiền, đơn tối thiểu, trần giảm, thời gian, tổng lượt; tắt mã bằng bỏ chọn hoạt động. Mỗi đơn một mã, lượt được ghi nhận khi đặt đơn thành công kể cả đơn hủy. Không có chức năng xóa lịch sử sử dụng.
- `/cart`: mở bước đặt hàng, nhập mã rồi Áp dụng. Đổi nội dung giỏ hàng phải áp dụng lại. Backend kiểm tra lại khi đặt đơn, tổng tiền trên database và payOS là tổng sau giảm.
- `/admin`: thống kê mặc định tháng hiện tại; lọc ngày đặt. Doanh thu chỉ tính đơn hoàn thành + đã thanh toán, sau giảm giá. Sản phẩm bán chạy tính số lượng của cùng nhóm đơn. Tồn thấp từ 0 tới 5 là trạng thái hiện tại, không phụ thuộc khoảng ngày.
- Nút trái tim trên card/trang chi tiết; `/favorites`: danh sách theo tài khoản, phân trang 10. Sản phẩm ẩn/xóa mềm không hiển thị. Lưu/bỏ yêu thích không thay đổi giỏ hàng.
- Email xác nhận đặt hàng giữ luồng hiện tại. Khi trạng thái đơn hoặc thanh toán thay đổi bằng Eloquent, observer đưa email snapshot vào queue sau commit. Gửi vào email nhận hàng của đơn; lưu lại cùng trạng thái không gửi trùng. Không gửi email bù cho thay đổi trước khi triển khai.
- COD được ghi nhận đã thanh toán khi admin chuyển Hoàn thành; nhân viên cần xác nhận đã thu tiền trước thao tác. Hủy đơn bởi admin hoàn tồn kho đúng một lần. Hủy đơn đã thanh toán không tự hoàn tiền.

## Các API mới

Admin (auth:sanctum + admin): GET `/api/admin/dashboard`, GET/POST `/api/admin/coupons`, PUT `/api/admin/coupons/{id}`.
Khách đăng nhập: POST `/api/coupons/preview`, GET `/api/favorites`, GET `/api/favorites/ids`, PUT/DELETE `/api/favorites/{productId}`.

## Kiểm tra

`php artisan test --filter=ShopFeaturesTest` dùng SQLite :memory: và mail fake; không gửi Gmail hoặc tạo giao dịch payOS thật.
