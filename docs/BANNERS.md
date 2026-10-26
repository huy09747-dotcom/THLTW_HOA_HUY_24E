# Banner trang chủ

Vào `/admin/banners` để tải ảnh, đổi tiêu đề, mô tả, liên kết, thứ tự, trạng thái hoặc xóa banner. Chọn vị trí **Trình chiếu trang chủ** và trạng thái **Hiển thị** để xuất hiện trên cửa hàng. Ảnh upload tối đa 5 MB. Nội dung lấy động từ bảng `banner` có sẵn.

Trang chủ tự chuyển mỗi 5 giây, có nút trước/sau ở hai cạnh và chấm chọn slide. Tự chuyển tạm dừng khi rê chuột, đặt focus trong banner, chuyển tab trình duyệt; tôn trọng cài đặt giảm chuyển động của hệ điều hành.

## Đồng bộ máy thành viên (XAMPP)

Sau khi pull code, bật MySQL và kiểm tra `.env` backend trỏ đúng database đã import (có bảng `banner`). Trong thư mục backend:

```powershell
php artisan db:seed --class=BannerSeeder
php artisan storage:link
```

Nếu terminal chưa nhận PHP, thay `php` bằng `C:\xampp\php\php.exe`. Seeder tạo 3 banner và không ghi đè nội dung đã sửa; không cần tạo lại bảng. Ba ảnh nằm trong `backend/public/images/banners/` và cần được đưa vào Git cùng source. Ảnh tải thêm từ admin nằm trong `backend/storage/app/public/images/`; cần sao chép thư mục storage khi chuyển máy nếu muốn giữ ảnh đã upload.

## Ảnh được tạo

Tool mode: built-in `image_gen`, tạo mới, nền không trong suốt. Đã tạo và lưu ba ảnh PNG vào `backend/public/images/banners/hh-phone.png`, `hh-laptop.png`, `hh-accessories.png`.

Prompt điện thoại: Create a premium photorealistic 3D product advertising background for HH Shop technology ecommerce. Wide landscape 1536x1024. Two elegant unbranded smartphones, sage green rear camera phone and dark emerald screen front phone, arranged on rounded mint podiums on the RIGHT HALF of composition. LEFT HALF is clean pale mint negative space reserved for HTML headings. Soft studio light, realistic aluminum and glass, restrained forest green and ivory palette, modern editorial design, subtle circular shadows, high quality product visualization. No text, no letters, no logos, no watermark. Keep devices within right 55% with generous safe margins.

Prompt laptop: Premium photorealistic 3D technology ecommerce banner background, wide landscape. Unbranded thin silver laptop open with abstract emerald screen, wireless mouse and notebook on pale sage sculptural podiums on RIGHT HALF. LEFT HALF clean pale mint negative space for HTML text. Soft studio light, luxurious realistic aluminum, forest green and ivory palette, subtle shadows, editorial product photography, no text, no letters, no logos, no watermark.

Prompt phụ kiện: Premium photorealistic 3D ecommerce technology banner, wide landscape. Elegant unbranded ivory over-ear headphones, wireless earbuds in case and compact sage charger arranged on rounded mint podiums on RIGHT HALF. LEFT HALF empty pale mint space for HTML headings. Soft studio lighting, realistic materials, restrained forest green and ivory palette, subtle shadows, modern editorial product photography. No text, no logos, no watermark.
