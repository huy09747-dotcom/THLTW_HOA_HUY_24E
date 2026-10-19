# Kiểm tra chức năng tài khoản

## Phạm vi
- Khách hàng đăng ký tại `/register`, đăng nhập tại `/login`.
- Quản trị viên đăng nhập tại `/admin/login`. Hai form gửi audience riêng; sai vai trò không được cấp token.
- Đăng ký luôn tạo tài khoản user đang hoạt động, bất kể roles/status gửi thêm.
- `/profile`: xem và sửa tên, tên đăng nhập, email, điện thoại, địa chỉ. Không cho sửa quyền hoặc trạng thái.
- `/orders`: đơn hàng của tài khoản đang đăng nhập, 10 đơn/trang, mới nhất trước.
- `/admin/users`: admin chặn/mở chặn user. API không tìm tài khoản admin trong tập user, trả 404 khi cố chặn admin.
- Chặn user thu hồi toàn bộ token. API cũng kiểm tra trạng thái tài khoản sau xác thực.

## Kiểm tra trên trình duyệt
1. Đăng ký tài khoản mới, kiểm tra chuyển tới hồ sơ; email/điện thoại trùng phải báo lỗi.
2. Đăng nhập user tại `/login`; đăng nhập admin tại `/admin/login`. Thử ngược vai trò phải báo lỗi.
3. Sửa hồ sơ rồi tải lại trang, xác nhận dữ liệu đã lưu. Email trùng tài khoản khác phải bị từ chối.
4. Mở Đơn hàng; thử tài khoản chưa có đơn và tài khoản có hơn 10 đơn; kiểm tra phân trang.
5. Đăng xuất rồi mở lại hồ sơ/đơn hàng: phải yêu cầu đăng nhập.
6. Admin chặn user đang có phiên đăng nhập; user không thể tải dữ liệu được bảo vệ hoặc đăng nhập lại.
7. Mở chặn user: phải đăng nhập lại; đơn hàng cũ vẫn còn.
8. Thử API chặn chính admin và admin khác: đều bị từ chối, trạng thái admin không đổi.
9. Kiểm tra form và bảng đơn hàng trên màn hình nhỏ.

## Kiểm thử tự động
- Backend: `php artisan test --filter='AccountTest|AdminUserTest'`.
- Frontend: `npm run build` và ESLint trên các file thay đổi.
- Kiểm thử backend dùng SQLite trong bộ nhớ, không sửa dữ liệu cửa hàng.

## Kết quả trong lần triển khai
- 11 kiểm thử tài khoản/quản lý user đạt, 92 assertions.
- Build Next.js thành công, có route `/profile` và `/orders`.
- Toàn bộ backend: 29 đạt, 1 lỗi ở CartApiTest gọi `/api/products/1`; controller sản phẩm hiện tìm theo slug, còn fixture cũ không có slug. Hai file này không bị thay đổi trong phần tài khoản.
- Checklist trình duyệt ở trên chưa được chạy với dữ liệu thực.
