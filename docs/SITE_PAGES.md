# Trang đơn và cấu hình website

## Sử dụng

- `/admin/pages`: chọn Giới thiệu, Chính sách mua hàng hoặc Liên hệ; nhập tiêu đề, mô tả và nội dung; chọn Hiển thị rồi lưu để xuất bản. Bản nháp/Ẩn không được API công khai trả về.
- Nội dung dùng văn bản thuần, giữ xuống dòng và hiển thị an toàn; không thực thi HTML/JavaScript.
- `/admin/settings`: tên website, khẩu hiệu, email/điện thoại liên hệ, địa chỉ, giờ làm việc, chân trang và mô tả SEO.
- Phía khách hàng: `/about`, `/purchase-policy`, `/contact`; có liên kết ở chân trang. Giới thiệu và Liên hệ cũng có trên thanh điều hướng.
- Cấu hình được dùng ở đầu trang, chân trang, metadata cửa hàng và khối thông tin liên hệ. Tải lại cửa hàng sau khi lưu để nhận cấu hình mới.
- Trang chưa có nội dung xuất bản hiển thị thông báo đang cập nhật. Không tự tạo chính sách mua hàng thay chủ cửa hàng.

## Database

Trang đơn dùng bảng `baiviet` hiện có với `post_type=page`. Ba slug cố định là `about`, `purchase-policy`, `contact`. Slug đã thuộc bài viết sẽ báo xung đột để tránh ghi đè bài viết.

Cấu hình dùng bảng `site_settings` mới, một bản ghi ID 1. Nếu cài dự án trên máy khác, chạy tại thư mục backend:

```powershell
php artisan migrate --path=database/migrations/2026_10_19_000001_create_site_settings_table.php --force
```

Không cần chạy lại SQL khởi tạo hay xóa database.

## API

| Quyền | Endpoint | Chức năng |
|---|---|---|
| Công khai | GET `/api/pages/{slug}` | Đọc trang đã xuất bản |
| Công khai | GET `/api/site-settings` | Đọc các trường cấu hình công khai |
| Admin hoạt động | GET `/api/admin/pages` | Ba trang để chỉnh sửa, gồm cả nháp |
| Admin hoạt động | PUT `/api/admin/pages/{slug}` | Tạo/cập nhật nội dung và trạng thái |
| Admin hoạt động | GET/PUT `/api/admin/site-settings` | Xem/lưu cấu hình |

API cũ `/api/baiviets` chỉ trả bài viết đang hiển thị, không trả trang đơn hoặc bản nháp.

## Kiểm tra

`php artisan test --filter='SitePagesTest|ContentAdminTest'`: 10 kiểm thử đạt, 141 assertions. Bao gồm quyền admin, chặn user/khách, xuất bản/ẩn/nháp, cấu hình singleton, dữ liệu không hợp lệ, slug xung đột và tách trang đơn khỏi bài viết.

ESLint trên các file frontend thay đổi: đạt.

Build Next.js: đạt, có đủ `/admin/pages`, `/admin/settings`, `/about`, `/purchase-policy`, `/contact`.

Trong lần triển khai này, lệnh migration trên database dự án chưa được thực thi: hệ thống duyệt tự động báo mô hình quá tải ở cả hai lần thử. Cần chạy lệnh ở mục Database trước khi sử dụng lưu cấu hình. Chưa kiểm tra thao tác trên trình duyệt với dữ liệu thực.

Kiểm tra thủ công khi có tài khoản admin:
1. Nhập và xuất bản từng trang; mở liên kết phía cửa hàng, xác nhận xuống dòng và nội dung đúng.
2. Chuyển về nháp/ẩn; tải lại trang công khai, nội dung cũ không còn hiển thị.
3. Đổi cấu hình rồi tải lại cửa hàng: kiểm tra tên, khẩu hiệu, chân trang, thông tin liên hệ và mô tả SEO.
4. Thử email/điện thoại không hợp lệ; form phải báo lỗi và không báo lưu thành công.
5. Kiểm tra các trang và menu trên màn hình điện thoại.
