# THLTW_HOA_HUY_24E — HH Shop

Dự án bán hàng gồm backend Laravel 12, frontend Next.js 16 và MySQL. Bản nộp giữ đầy đủ chức năng khách hàng, quản trị và thanh toán chuyển khoản payOS.

## Môi trường

- PHP 8.2 trở lên, Composer 2 và các extension Laravel yêu cầu, đặc biệt `pdo_mysql`.
- Node.js 20.9 trở lên và npm.
- MySQL 8. Bản SQL được xuất từ MySQL 8.4 ngày 06/11/2026.

## Cài đặt sau khi clone

Chạy các lệnh từ thư mục gốc repository, nơi có `backend`, `frontend` và `docs`.

### 1. Database

1. Mở Laragon, bật MySQL và mở phpMyAdmin.
2. Ở trang chính phpMyAdmin, bấm tab Import (Nhập).
3. Chọn file docs/THLTW_DB_HOA_HUY_24E.sql rồi bấm Import hoặc Go (Thực hiện).
4. Khi báo thành công, làm mới danh sách bên trái và mở thltw_huy_hoa_24e để kiểm tra các bảng.

File SQL tự tạo database thltw_huy_hoa_24e với mã hóa utf8mb4 rồi nạp dữ liệu. Tài khoản MySQL dùng trong phpMyAdmin cần quyền tạo database. Nếu nhập lại vào database đã có, file SQL sẽ tạo lại các bảng trùng tên và nạp dữ liệu từ bản nộp.

### 2. Backend

File `backend/.env` được chia sẻ trong repo private cho mục đích chấm bài. Sửa `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` theo MySQL trên máy nhận. Dùng `FRONTEND_URL=http://127.0.0.1:3000` khi chạy local.

```powershell
cd backend
composer install
php artisan config:clear
php artisan storage:link
php artisan migrate:status
php artisan serve --host=127.0.0.1 --port=8000
```

Database đã có dữ liệu và đủ migration tại thời điểm xuất. Các ảnh upload được lưu trong `backend/storage/app/public` và được Git theo dõi. Lệnh `storage:link` tạo liên kết truy cập ảnh trên máy nhận.

### 3. Frontend

Mở terminal thứ hai tại thư mục gốc.

```powershell
cd frontend
npm ci
npm run dev
```

Mở `http://127.0.0.1:3000`. File `frontend/.env` cấu hình API local tại `http://127.0.0.1:8000`. Nếu máy có `.env.local` cũ trỏ đến tunnel, cần sửa hai biến trong file đó cho giống `.env` rồi khởi động lại frontend.

### 4. Email và hàng đợi

Để xử lý email gửi qua hàng đợi, giữ terminal thứ ba chạy trong `backend`.

```powershell
php artisan queue:work --tries=1
```

Cấu hình SMTP trong `backend/.env` cần còn hiệu lực. Đăng nhập bằng tài khoản đã có trong database hoặc đăng ký tài khoản mới. Mật khẩu tài khoản hiện có được giữ dưới dạng băm, dùng thông tin đăng nhập chủ dự án đã cung cấp.

## Thanh toán chuyển khoản payOS

Ba biến `PAYOS_CLIENT_ID`, `PAYOS_API_KEY`, `PAYOS_CHECKSUM_KEY` nằm ở `backend/.env`. Chọn chuyển khoản khi đặt hàng, mở chi tiết đơn, tạo liên kết và mở QR thanh toán. Tiền được chuyển vào tài khoản ngân hàng gắn với kênh payOS. Đây là giao dịch tiền thật.

Trang chi tiết đơn kiểm tra lại thanh toán qua API payOS khi đang mở và có nút kiểm tra thủ công. Để nhận thông báo thanh toán khi khách đã đóng trang, cần cấu hình webhook truy cập được qua HTTPS.

Trong `backend`, mở terminal chạy proxy chỉ tiếp nhận webhook.

```powershell
node scripts/payos-webhook-proxy.cjs
```

Mở terminal khác chạy Cloudflare Tunnel (cần cài `cloudflared`).

```powershell
cloudflared tunnel --url http://127.0.0.1:8010 --no-autoupdate
```

Lấy URL HTTPS mới của tunnel rồi đăng ký webhook từ thư mục `backend`.

```powershell
php scripts/register-payos-webhook.php "https://TEN-MIEN-TUNNEL/api/payments/payos/webhook"
```

Giữ backend, proxy và tunnel chạy để nhận webhook. Mỗi khi URL tunnel đổi, đăng ký lại. Việc đăng ký webhook trên máy khác cập nhật địa chỉ nhận thông báo của cùng kênh payOS, vì vậy nhóm cần thống nhất máy nhận webhook trong lúc demo.

## Dữ liệu và cấu hình bản nộp

- `docs/THLTW_DB_HOA_HUY_24E.sql` là bản database đầy đủ, thay các file SQL nền và cập nhật trước đây.
- Bản SQL đã được nhập thử vào database tạm, đối chiếu cấu trúc cột và số dòng của 37 bảng.
- Các bảng phiên, token đăng nhập, token đặt lại mật khẩu, cache và hàng đợi có đầy đủ cấu trúc và được để trống trong bản xuất. Đăng nhập lại sau khi cài đặt.
- Cài thư viện bằng `composer install` và `npm ci` với các lockfile trong repo.
- Repo chứa cấu hình thật được chủ dự án chủ động chia sẻ cho người được cấp quyền. Giữ repo private và thu hồi hoặc thay khóa sau khi chấm bài.
