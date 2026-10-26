# Đồng bộ dữ liệu mới và menu động

Nhận code mới, bật MySQL XAMPP, kiểm tra `.env` rồi chạy PowerShell trong thư mục `backend`:

```powershell
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan migrate --path=database/migrations/2026_10_26_000003_add_menu_sort_order.php
& "C:\xampp\php\php.exe" artisan db:seed --class=CatalogExpansionSeeder
& "C:\xampp\php\php.exe" artisan db:seed --class=MenuSampleSeeder
```

CatalogExpansionSeeder thêm 5 danh mục, 15 sản phẩm và 5 thương hiệu (Logitech, Dell, Xiaomi, Kingston, JBL) trên database hiện tại; dữ liệu đã có cùng slug/SKU được giữ lại. Giá, tồn kho và hình minh họa là dữ liệu mẫu. Ảnh SVG đi kèm source trong `backend/public/images/catalog`.

MenuSampleSeeder tạo 6 liên kết điều hướng ban đầu. Admin `/admin/menus` hỗ trợ thêm/sửa, thứ tự, ẩn/hiện, thùng rác và khôi phục. Menu hiển thị trên cửa hàng lấy qua `/api/menus`; liên kết danh mục/thương hiệu/chủ đề/trang đơn được xác định từ đối tượng đã chọn. Liên kết tới đối tượng bị ẩn hoặc xóa không hiển thị công khai. Thay đổi được áp dụng khi tải lại trang cửa hàng.

Không dùng `migrate:fresh` để đồng bộ. Các lệnh này bổ sung dữ liệu, không sao chép toàn bộ dữ liệu giữa các máy.
