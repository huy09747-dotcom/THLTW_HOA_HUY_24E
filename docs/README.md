# TÀI LIỆU HƯỚNG DẪN CÀI ĐẶT, VẬN HÀNH VÀ BÀN GIAO

| Thông tin | Chi tiết |
| :--- | :--- |
| **Dự án** | **HH Shop** · Website bán điện thoại, laptop và phụ kiện |
| **Mã đề tài** | `THLTW_HOA_HUY_24E` |
| **Kiến trúc** | Backend Laravel 12 API · Frontend Next.js 16 · Cơ sở dữ liệu MySQL |
| **Đối tượng sử dụng** | Giảng viên, người tiếp nhận mã nguồn và quản trị viên |

---

## MỤC LỤC

1. [Yêu cầu hệ thống và môi trường](#1-yêu-cầu-hệ-thống-và-môi-trường)
2. [Cấu trúc mã nguồn bàn giao](#2-cấu-trúc-mã-nguồn-bàn-giao)
3. [Cài đặt Backend Laravel 12](#3-cài-đặt-backend-laravel-12)
4. [Cài đặt Frontend Next.js 16](#4-cài-đặt-frontend-nextjs-16)
5. [Cấu hình Gmail gửi liên kết đặt lại mật khẩu](#5-cấu-hình-gmail-gửi-liên-kết-đặt-lại-mật-khẩu)
6. [Thanh toán chuyển khoản ngân hàng qua payOS](#6-thanh-toán-chuyển-khoản-ngân-hàng-qua-payos)
7. [Tài khoản và kịch bản chấm bài](#7-tài-khoản-và-kịch-bản-chấm-bài)
8. [Lưu ý vận hành và xử lý lỗi](#8-lưu-ý-vận-hành-và-xử-lý-lỗi)

> [!NOTE]
> Cài lần đầu theo mục 1–4. Khi cửa hàng đã mở, dùng tài khoản tại mục 7. Mục 5 hướng dẫn thử email và mục 6 hướng dẫn thử chuyển khoản.

---

## 1. YÊU CẦU HỆ THỐNG VÀ MÔI TRƯỜNG

| Công cụ | Yêu cầu | Dùng để |
| :--- | :--- | :--- |
| **Git** | Tài khoản GitHub có quyền truy cập repo private | Clone nhánh `main` |
| **PHP** | 8.2 trở lên. Bật `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` và các extension Composer yêu cầu | Chạy Laravel và kết nối MySQL |
| **Composer** | Composer 2 | Cài thư viện PHP từ `composer.lock` |
| **Node.js và npm** | Node.js 20.9 trở lên, kèm npm | Cài và chạy Next.js |
| **MySQL** | MySQL 8. Bản SQL được xuất bằng MySQL 8.4 | Lưu dữ liệu HH Shop |
| **Laragon và phpMyAdmin** | Chạy MySQL trên Laragon và mở được phpMyAdmin | Import file SQL |

Nếu PowerShell không nhận lệnh `php` hoặc `composer`, mở **Terminal** trong Laragon rồi chạy lại.

---

## 2. CẤU TRÚC MÃ NGUỒN BÀN GIAO

```text
THLTW_HOA_HUY_24E/
├── backend/
│   ├── app/                           Logic nghiệp vụ, model và controller
│   ├── routes/api.php                 Các endpoint Laravel
│   ├── database/migrations/           Các bản cập nhật cấu trúc database
│   ├── storage/app/public/            Ảnh đã tải lên
│   ├── scripts/                       Proxy và script đăng ký webhook payOS
│   ├── .env                           Cấu hình MySQL, email và payOS
│   └── composer.lock                  Phiên bản thư viện PHP
├── frontend/
│   ├── src/app/                       Các trang cửa hàng và quản trị
│   ├── src/components/                Thành phần giao diện
│   ├── .env                           Địa chỉ backend
│   └── package-lock.json              Phiên bản thư viện frontend
└── docs/
    └── THLTW_DB_HOA_HUY_24E.sql        Bản SQL dùng để cài đặt
```

Mở terminal tại thư mục muốn lưu bài và chạy các lệnh sau.

```powershell
git clone --branch main https://github.com/huy09747-dotcom/THLTW_HOA_HUY_24E.git
cd THLTW_HOA_HUY_24E
```

---

## 3. CÀI ĐẶT BACKEND LARAVEL 12

### Bước 3.1. Cài thư viện PHP

Chạy các lệnh sau từ thư mục gốc dự án.

```powershell
cd backend
composer install
```

Chờ Composer cài xong rồi tiếp tục với các lệnh `php artisan` ở các bước sau.

### Bước 3.2. Import cơ sở dữ liệu bằng phpMyAdmin

1. Mở **Laragon** và bật **MySQL**.
2. Mở **phpMyAdmin**. Tại **trang chính**, chọn tab **Import**.
3. Chọn file **`docs/THLTW_DB_HOA_HUY_24E.sql`** rồi bấm **Import** hoặc **Go**.
4. Sau khi có thông báo thành công, làm mới danh sách bên trái. Mở **`thltw_huy_hoa_24e`** và kiểm tra danh sách bảng.

> [!IMPORTANT]
> File SQL tự tạo và chọn database `thltw_huy_hoa_24e` khi import. Tài khoản MySQL cần quyền tạo database. Khi import lại, các bảng trùng tên sẽ được tạo lại từ bản nộp.

### Bước 3.3. Cấu hình file `backend/.env`

Mở file `backend/.env` được bàn giao và chỉnh các dòng sau theo MySQL trên máy đang chạy.

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thltw_huy_hoa_24e
DB_USERNAME=root
DB_PASSWORD=
FRONTEND_URL=http://127.0.0.1:3000
FRONTEND_ALLOWED_ORIGINS=http://127.0.0.1:3000,http://localhost:3000
QUEUE_CONNECTION=database
```

| Biến | Giá trị cần kiểm tra |
| :--- | :--- |
| `DB_HOST`, `DB_PORT` | Địa chỉ và cổng MySQL của Laragon |
| `DB_DATABASE` | Tên database vừa import, `thltw_huy_hoa_24e` |
| `DB_USERNAME`, `DB_PASSWORD` | Tài khoản MySQL trên máy. Để trống `DB_PASSWORD` nếu tài khoản MySQL không đặt mật khẩu |
| `APP_KEY` | Giữ khóa được bàn giao. Nếu rỗng, chạy `php artisan key:generate` trong `backend` |
| `FRONTEND_URL` | Địa chỉ mở cửa hàng và địa chỉ quay về từ email hoặc payOS |

Nếu file `.env` chưa có, lấy bản cấu hình từ nhóm. Trên Windows, lưu đúng tên `.env`.

### Bước 3.4. Cập nhật bảng và liên kết thư mục ảnh

Chạy tại `backend`.

```powershell
php artisan config:clear
php artisan migrate
php artisan storage:link
```

`migrate` áp dụng các thay đổi còn thiếu nếu bản SQL cũ hơn mã nguồn. `storage:link` giúp trình duyệt tải được ảnh sản phẩm và ảnh đại diện đã lưu trong storage.

### Bước 3.5. Khởi động Backend

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Giữ terminal này chạy. Mở [địa chỉ kiểm tra Backend](http://127.0.0.1:8000/up). Nếu trang phản hồi, chuyển sang cài Frontend.

---

## 4. CÀI ĐẶT FRONTEND NEXT.JS 16

### Bước 4.1. Mở terminal thứ hai và cài thư viện

Chạy các lệnh sau từ thư mục gốc dự án.

```powershell
cd frontend
npm ci
```

### Bước 4.2. Cấu hình file `frontend/.env`

```dotenv
LARAVEL_API_URL=http://127.0.0.1:8000/api
NEXT_PUBLIC_LARAVEL_ORIGIN=http://127.0.0.1:8000
```

`LARAVEL_API_URL` có đuôi `/api` để gọi API. `NEXT_PUBLIC_LARAVEL_ORIGIN` dùng địa chỉ gốc của Laravel để tải ảnh. Nếu máy đã có `.env.local` hoặc `.env.development.local`, kiểm tra các biến trùng tên trong đó vì chúng được ưu tiên hơn `.env`.

### Bước 4.3. Khởi động Frontend

```powershell
npm run dev
```

Mở cửa hàng bằng địa chỉ [http://127.0.0.1:3000](http://127.0.0.1:3000). Dùng địa chỉ này trong suốt buổi demo để phiên đăng nhập được giữ khi quay về từ payOS.

---

## 5. CẤU HÌNH GMAIL GỬI LIÊN KẾT ĐẶT LẠI MẬT KHẨU

### Bước 5.1. Kiểm tra cấu hình SMTP

Nếu nhóm đã cấp cấu hình Gmail đang hoạt động, giữ nguyên các giá trị đó. Khi thay Gmail gửi thư, điền địa chỉ mới và mật khẩu ứng dụng của tài khoản đó trong `backend/.env`.

```dotenv
#API GMAIL
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=huy09747@gmail.com
MAIL_PASSWORD="jwfuxzmxhhqjkxee"
MAIL_FROM_ADDRESS=huy09747@gmail.com
MAIL_FROM_NAME="HH Shop"
```

### Bước 5.2. Chạy tiến trình gửi thư

Mở terminal riêng tại `backend` và giữ tiến trình chạy khi thử email.

```powershell
php artisan config:clear
php artisan queue:work database --tries=1
```

### Bước 5.3. Kiểm tra trên giao diện

1. Tại màn hình đăng nhập cửa hàng, chọn **Quên mật khẩu**.
2. Nhập email của tài khoản đã đăng ký và gửi yêu cầu.
3. Mở thư đến hoặc thư mục Spam rồi bấm **liên kết đặt lại mật khẩu**.
4. Điền mật khẩu mới, xác nhận và đăng nhập lại.

---

## 6. THANH TOÁN CHUYỂN KHOẢN NGÂN HÀNG QUA PAYOS

Bản nộp đã có cấu hình payOS trong `backend/.env`. Sau khi clone, import SQL và chạy ứng dụng theo mục 3–4, có thể dùng chuyển khoản nếu máy có Internet và ba khóa payOS còn hiệu lực. Giữ nguyên các giá trị được bàn giao.

### Cách thử thanh toán

1. Đăng nhập tài khoản khách hàng và tạo đơn mới bằng phương thức **Chuyển khoản ngân hàng**.
2. Mở chi tiết đơn và bấm **Thanh toán ngay**. Cửa hàng chuyển sang trang payOS hiển thị mã QR.
3. Quét QR bằng ứng dụng ngân hàng. Đối chiếu tài khoản nhận, số tiền và nội dung chuyển khoản. Nếu buổi chấm chỉ cần xem QR, dừng tại đây.
4. Nếu thử giao dịch tiền thật, xác nhận trong ứng dụng ngân hàng. Sau đó quay về chi tiết đơn và bấm **Kiểm tra thanh toán** để HH Shop đối chiếu trực tiếp với payOS.
5. Nếu lỡ thực hiện chuyển thật vui lòng liên hệ zalo: 0843212278 cung cấp bill để được xử lí hoàn tiền.

### Webhook dùng khi nào?

Webhook là thông báo payOS gửi về máy chủ để tự cập nhật đơn ngay cả khi khách đã đóng trang. Bước tạo QR và nút **Kiểm tra thanh toán** ở trên hoạt động qua API payOS, không cần cài webhook trên máy giảng viên.

---

## 7. TÀI KHOẢN

### 7.1. Tài khoản bàn giao

| Vai trò | Đường dẫn đăng nhập | Email | Mật khẩu |
| :--- | :--- | :--- | :--- |
| **Khách hàng** | [Đăng nhập cửa hàng](http://127.0.0.1:3000/login) | nmhuy101@gmail.com | 12345678 | Có thể bấm đăng ký để tạo tài khoản mới
| **Quản trị viên** | [Đăng nhập quản trị](http://127.0.0.1:3000/admin/login) | admin@gmail.com | 12345678 |

### 7.2. Luồng kiểm tra sau cài đặt

| Bước | Thao tác | Kết quả cần thấy |
| :--- | :--- | :--- |
| **1. Xem cửa hàng** | Mở trang chủ, chọn danh mục và một sản phẩm | Có dữ liệu sản phẩm, giá và hình ảnh |
| **2. Đăng nhập khách hàng** | Mở hồ sơ và thử cập nhật ảnh đại diện | Hồ sơ lưu được và ảnh hiển thị |
| **3. Tạo mã khuyến mãi** | Đăng nhập quản trị, mở `/admin/coupons`. Tạo mã `GIAMGIA` nếu chưa có, chọn giảm 10%, đơn tối thiểu 0, để trống thời hạn và bật hoạt động | Mã có trong danh sách quản trị |
| **4. Áp mã và đặt COD** | Thêm hàng vào giỏ, nhập mã, bấm **Áp dụng**, kiểm tra tổng tiền rồi đặt COD | Hiện số tiền giảm và đơn mới xuất hiện trong `/orders` |
| **5. Kiểm tra quản trị** | Mở `/admin/orders` và tìm đơn vừa tạo | Thấy thông tin đơn và trạng thái |
| **6. Kiểm tra chuyển khoản** | Tạo đơn mới theo mục 6 | Mở được trang payOS và QR đúng tổng tiền |

Nếu thay đổi sản phẩm hoặc số lượng trong giỏ, áp dụng lại mã trước khi đặt hàng.

---

## 8. LƯU Ý VẬN HÀNH VÀ XỬ LÝ LỖI

| Hiện tượng | Cách xử lý |
| :--- | :--- |
| Thiếu `vendor/autoload.php` | Chạy `composer install` trong `backend` |
| Laravel báo lỗi kết nối database | Kiểm tra MySQL trong Laragon và các biến `DB_*`. Sau khi sửa, chạy `php artisan config:clear` |
| Cửa hàng báo chưa kết nối được | Kiểm tra Laravel ở cổng 8000 và địa chỉ API trong `frontend/.env`. Khởi động lại Frontend sau khi sửa |
| Thiếu cột `avatar` | Kiểm tra đúng database rồi chạy `php artisan migrate` tại `backend` |
| Ảnh upload không hiển thị | Kiểm tra `php artisan storage:link` và `NEXT_PUBLIC_LARAVEL_ORIGIN` |
| Không nhận được email | Kiểm tra SMTP, terminal queue worker và thư mục Spam |
| payOS chưa cập nhật đơn | Bấm **Kiểm tra thanh toán** ở chi tiết đơn. Nếu vẫn chưa xác nhận, kiểm tra trạng thái giao dịch trong ứng dụng ngân hàng |

Khi mở lại sau buổi cài đặt, bật MySQL, chạy lại Laravel và Next.js. Chạy thêm queue worker khi cần thử gửi email.
