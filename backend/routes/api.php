<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BaiVietController;
use App\Http\Controllers\Api\ChudeBaiVietController;
use App\Http\Controllers\Api\DonhangController;
use App\Http\Controllers\Api\ChiTietDonHangController;
use App\Http\Controllers\Api\ChiTietGioHangController;
use App\Http\Controllers\Api\GioHangController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;



// ==================== PRODUCT ====================

// Danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);

// Thêm sản phẩm
Route::post('/products', [ProductController::class, 'store'])->middleware(['auth:sanctum', 'admin']);

// Sửa sản phẩm
Route::put('/products/{id}', [ProductController::class, 'update'])->middleware(['auth:sanctum', 'admin']);

// Xóa sản phẩm
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware(['auth:sanctum', 'admin']);


// ==================== BRAND ====================

// Danh sách thương hiệu
Route::get('/brands', [BrandController::class, 'index']);

// Thêm thương hiệu
Route::post('/brands', [BrandController::class, 'store'])->middleware(['auth:sanctum', 'admin']);

// Sửa thương hiệu
Route::put('/brands/{id}', [BrandController::class, 'update'])->middleware(['auth:sanctum', 'admin']);

// Xóa thương hiệu
Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->middleware(['auth:sanctum', 'admin']);


// ==================== TEST ====================

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong'
    ]);
});


// ==================== CATEGORY ====================

// Danh sách danh mục
Route::get('/categories', [CategoryController::class, 'index']);

// Thêm danh mục
Route::post('/categories', [CategoryController::class, 'store'])->middleware(['auth:sanctum', 'admin']);

// Sửa danh mục
Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware(['auth:sanctum', 'admin']);

// Xóa tạm danh mục
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware(['auth:sanctum', 'admin']);

// Danh sách danh mục trong thùng rác
Route::get('/categories-trash', [CategoryController::class, 'trash']);

// Khôi phục danh mục
Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore'])->middleware(['auth:sanctum', 'admin']);

// Xóa vĩnh viễn danh mục
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete'])->middleware(['auth:sanctum', 'admin']);


// ==================== BANNER ====================

Route::get('/banners', [BannerController::class, 'index']);


// ==================== BÀI VIẾT ====================

Route::get('/baiviets', [BaiVietController::class, 'index']);


// ==================== CHỦ ĐỀ BÀI VIẾT ====================

Route::get('/chudebaiviets', [ChudeBaiVietController::class, 'index']);


// ==================== ĐƠN HÀNG ====================

Route::get('/donhangs', [DonhangController::class, 'index'])->middleware(['auth:sanctum', 'admin']);


// ==================== CHI TIẾT ĐƠN HÀNG ====================

Route::get('/chitietdonhangs', [ChiTietDonHangController::class, 'index'])->middleware(['auth:sanctum', 'admin']);


// ==================== CHI TIẾT GIỎ HÀNG ====================

Route::get('/chitietgiohangs', [ChiTietGioHangController::class, 'index'])->middleware(['auth:sanctum', 'admin']);


// ==================== GIỎ HÀNG ====================

Route::get('/giohangs', [GioHangController::class, 'index'])->middleware(['auth:sanctum', 'admin']);


// ==================== XÁC THỰC USER / ADMIN ====================

// Đăng ký thành viên (quyền user)
Route::post('/register', [AuthController::class, 'register']);

// Đăng nhập user / admin và lấy token
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Thông tin tài khoản đang đăng nhập
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Đăng xuất user / admin và thu hồi token hiện tại
Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/profile/avatar', [AuthController::class, 'updateProfileAvatar'])->middleware('auth:sanctum');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ==================== CHI TIẾT SẢN PHẨM USER ====================
Route::get('/products/{slug}', [ProductController::class, 'show']);

// ==================== GIỎ HÀNG CỦA TÀI KHOẢN ====================
// Xem giỏ hàng của người đang đăng nhập
Route::get('/cart', [CartController::class, 'index'])->middleware('auth:sanctum');
// Thêm sản phẩm vào giỏ
Route::post('/cart/items', [CartController::class, 'store'])->middleware('auth:sanctum');
// Đổi số lượng một dòng trong giỏ
Route::put('/cart/items/{id}', [CartController::class, 'update'])->middleware('auth:sanctum')->whereNumber('id');
// Xóa một dòng khỏi giỏ
Route::delete('/cart/items/{id}', [CartController::class, 'destroy'])->middleware('auth:sanctum')->whereNumber('id');

// ==================== TẢI HÌNH ẢNH ADMIN ====================
// Tải ảnh cho danh mục, sản phẩm và thương hiệu
Route::post('/images', [\App\Http\Controllers\Api\ImageUploadController::class, 'store'])
    ->middleware(['auth:sanctum', 'admin']);

// ==================== THÙNG RÁC SẢN PHẨM ====================
Route::get('/products-trash', [ProductController::class, 'trash'])->middleware(['auth:sanctum', 'admin']);
Route::patch('/products/{id}/restore', [ProductController::class, 'restore'])->middleware(['auth:sanctum', 'admin']);
Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->middleware(['auth:sanctum', 'admin']);

// ==================== THÙNG RÁC THƯƠNG HIỆU ====================
Route::get('/brands-trash', [BrandController::class, 'trash'])->middleware(['auth:sanctum', 'admin']);
Route::patch('/brands/{id}/restore', [BrandController::class, 'restore'])->middleware(['auth:sanctum', 'admin']);
Route::delete('/brands/{id}/force', [BrandController::class, 'forceDelete'])->middleware(['auth:sanctum', 'admin']);

// ==================== DAT HANG ====================
// Dat hang tu gio hang cua tai khoan dang dang nhap
Route::post('/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'store'])
    ->middleware('auth:sanctum');
// ĐƠN HÀNG - ADMIN
Route::get('/donhangs', [DonhangController::class, 'index'])
    ->middleware(['auth:sanctum', 'admin']);

Route::get('/donhangs/{id}', [DonhangController::class, 'show'])
    ->middleware(['auth:sanctum', 'admin'])
    ->whereNumber('id');

Route::patch('/donhangs/{id}/status', [DonhangController::class, 'updateStatus'])
    ->middleware(['auth:sanctum', 'admin'])
    ->whereNumber('id');

// ĐƠN HÀNG - NGƯỜI DÙNG
Route::get('/my-orders', [DonhangController::class, 'myOrders'])
    ->middleware('auth:sanctum');

// ==================== NGƯỜI DÙNG - ADMIN ====================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/users')->group(function () {
    // Danh sách người dùng
    Route::get('/', [UserController::class, 'index']);

    // Chi tiết người dùng
    Route::get('/{id}', [UserController::class, 'show'])->whereNumber('id');

    // Khóa hoặc mở khóa tài khoản
    Route::patch('/{id}/status', [UserController::class, 'updateStatus'])->whereNumber('id');

    // Danh sách đơn hàng của người dùng
    Route::get('/{id}/orders', [UserController::class, 'orders'])->whereNumber('id');
});


// ==================== POST - ADMIN ====================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/posts')->group(function () {
    // Danh sách
    Route::get('/', [\App\Http\Controllers\Api\PostController::class, 'index']);

    // Dữ liệu cho danh sách chọn
    Route::get('/options', [\App\Http\Controllers\Api\PostController::class, 'options']);

    // Thùng rác
    Route::get('/trash', [\App\Http\Controllers\Api\PostController::class, 'trash']);

    // Thêm mới
    Route::post('/', [\App\Http\Controllers\Api\PostController::class, 'store']);

    // Chỉnh sửa
    Route::put('/{id}', [\App\Http\Controllers\Api\PostController::class, 'update'])->whereNumber('id');

    // Xóa mềm
    Route::delete('/{id}', [\App\Http\Controllers\Api\PostController::class, 'destroy'])->whereNumber('id');

    // Khôi phục
    Route::patch('/{id}/restore', [\App\Http\Controllers\Api\PostController::class, 'restore'])->whereNumber('id');

    // Xóa vĩnh viễn
    Route::delete('/{id}/force', [\App\Http\Controllers\Api\PostController::class, 'forceDelete'])->whereNumber('id');

});


// ==================== MENU - ADMIN ====================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/menus')->group(function () {
    // Danh sách
    Route::get('/', [\App\Http\Controllers\Api\MenuController::class, 'index']);

    // Dữ liệu cho danh sách chọn
    Route::get('/options', [\App\Http\Controllers\Api\MenuController::class, 'options']);

    // Thùng rác
    Route::get('/trash', [\App\Http\Controllers\Api\MenuController::class, 'trash']);

    // Thêm mới
    Route::post('/', [\App\Http\Controllers\Api\MenuController::class, 'store']);

    // Chỉnh sửa
    Route::put('/{id}', [\App\Http\Controllers\Api\MenuController::class, 'update'])->whereNumber('id');

    // Xóa mềm
    Route::delete('/{id}', [\App\Http\Controllers\Api\MenuController::class, 'destroy'])->whereNumber('id');

    // Khôi phục
    Route::patch('/{id}/restore', [\App\Http\Controllers\Api\MenuController::class, 'restore'])->whereNumber('id');

    // Xóa vĩnh viễn
    Route::delete('/{id}/force', [\App\Http\Controllers\Api\MenuController::class, 'forceDelete'])->whereNumber('id');

});

// Trang đơn và cấu hình website
Route::get('/pages/{slug}', [\App\Http\Controllers\Api\PageController::class, 'show']);
Route::get('/site-settings', [\App\Http\Controllers\Api\SiteSettingController::class, 'show']);
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/pages', [\App\Http\Controllers\Api\PageController::class, 'index']);
    Route::put('/pages/by-slug/{slug}', [\App\Http\Controllers\Api\PageController::class, 'update']);
    Route::get('/site-settings', [\App\Http\Controllers\Api\SiteSettingController::class, 'show']);
    Route::put('/site-settings', [\App\Http\Controllers\Api\SiteSettingController::class, 'update']);
});

Route::get('/my-orders/{id}', [DonhangController::class, 'myOrder'])->middleware('auth:sanctum')->whereNumber('id');

// Đánh giá và nội dung công khai
Route::get('/products/{id}/reviews', [\App\Http\Controllers\Api\ReviewController::class,'index'])->whereNumber('id');
Route::middleware(['auth:sanctum'])->group(function(){
 Route::get('/products/{id}/my-review',[\App\Http\Controllers\Api\ReviewController::class,'mine'])->whereNumber('id');
 Route::put('/products/{id}/my-review',[\App\Http\Controllers\Api\ReviewController::class,'save'])->whereNumber('id');
 Route::delete('/products/{id}/my-review',[\App\Http\Controllers\Api\ReviewController::class,'destroy'])->whereNumber('id');
 Route::post('/my-orders/{id}/payment',[\App\Http\Controllers\Api\PaymentController::class,'start'])->whereNumber('id')->middleware('throttle:10,1');
 Route::post('/my-orders/{id}/payment/verify',[\App\Http\Controllers\Api\PaymentController::class,'verify'])->whereNumber('id')->middleware('throttle:20,1');
});
Route::get('/payment-methods',[\App\Http\Controllers\Api\PaymentController::class,'methods']);
Route::post('/payments/momo/ipn',[\App\Http\Controllers\Api\PaymentController::class,'momo']);
Route::get('/payments/vnpay/ipn',[\App\Http\Controllers\Api\PaymentController::class,'vnpay']);
Route::get('/news-topics',[\App\Http\Controllers\Api\NewsController::class,'topics']);
Route::get('/news',[\App\Http\Controllers\Api\NewsController::class,'index']);
Route::get('/news/{slug}',[\App\Http\Controllers\Api\NewsController::class,'show']);

Route::middleware(['auth:sanctum','admin'])->prefix('admin/reviews')->group(function(){
 Route::get('/',[\App\Http\Controllers\Api\AdminReviewController::class,'index']);
 Route::patch('/{id}',[\App\Http\Controllers\Api\AdminReviewController::class,'update'])->whereNumber('id');
 Route::delete('/{id}',[\App\Http\Controllers\Api\AdminReviewController::class,'destroy'])->whereNumber('id');
});

Route::middleware(['auth:sanctum','admin'])->prefix('admin/banners')->group(function(){
 Route::get('/',[BannerController::class,'adminIndex']);
 Route::post('/',[BannerController::class,'store']);
 Route::put('/{id}',[BannerController::class,'update'])->whereNumber('id');
 Route::delete('/{id}',[BannerController::class,'destroy'])->whereNumber('id');
});

Route::get('/menus', [\App\Http\Controllers\Api\MenuController::class, 'publicIndex']);

// Thanh toán payOS: nhận kết quả và tự động xác nhận đơn hàng
Route::post('/payments/payos/webhook', [\App\Http\Controllers\Api\PaymentController::class, 'payos']);

// Khach hang huy don dang cho xac nhan
Route::post('/my-orders/{id}/cancel', [DonhangController::class, 'cancelMyOrder'])
    ->middleware('auth:sanctum')
    ->whereNumber('id');

// Quen mat khau va dat lai mat khau qua email
Route::post('/forgot-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'forgot'])
    ->middleware('throttle:5,1');
Route::post('/reset-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'reset'])
    ->middleware('throttle:5,1');

// Ma giam gia va thong ke danh cho admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('/admin/coupons', [\App\Http\Controllers\Api\CouponController::class, 'index']);
    Route::post('/admin/coupons', [\App\Http\Controllers\Api\CouponController::class, 'save']);
    Route::put('/admin/coupons/{id}', [\App\Http\Controllers\Api\CouponController::class, 'save'])->whereNumber('id');
});
// Khach hang ap dung ma va luu san pham yeu thich
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/coupons/preview', [\App\Http\Controllers\Api\CouponController::class, 'preview'])->middleware('throttle:20,1');
    Route::get('/favorites/ids', [\App\Http\Controllers\Api\FavoriteController::class, 'ids']);
    Route::get('/favorites', [\App\Http\Controllers\Api\FavoriteController::class, 'index']);
    Route::put('/favorites/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'store'])->whereNumber('id');
    Route::delete('/favorites/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'destroy'])->whereNumber('id');
});

// Bo sung chuc nang theo bang yeu cau mon hoc
Route::post('/contacts', [\App\Http\Controllers\Api\ContactController::class,'store'])->middleware('throttle:5,1');
Route::put('/profile/password', [AuthController::class,'changePassword'])->middleware('auth:sanctum');
Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function () {
    Route::get('/topics',[\App\Http\Controllers\Api\TopicController::class,'index']);
    Route::post('/topics',[\App\Http\Controllers\Api\TopicController::class,'save']);
    Route::put('/topics/{id}',[\App\Http\Controllers\Api\TopicController::class,'save'])->whereNumber('id');
    Route::delete('/topics/{id}',[\App\Http\Controllers\Api\TopicController::class,'destroy'])->whereNumber('id');
    Route::post('/pages',[\App\Http\Controllers\Api\PageController::class,'save']);
    Route::put('/pages/{id}',[\App\Http\Controllers\Api\PageController::class,'save'])->whereNumber('id');
    Route::delete('/pages/{id}',[\App\Http\Controllers\Api\PageController::class,'destroy'])->whereNumber('id');
    Route::delete('/users/{id}',[UserController::class,'destroy'])->whereNumber('id');
    Route::delete('/coupons/{id}',[\App\Http\Controllers\Api\CouponController::class,'destroy'])->whereNumber('id');
    Route::get('/contacts',[\App\Http\Controllers\Api\ContactController::class,'index']);
    Route::put('/contacts/{id}',[\App\Http\Controllers\Api\ContactController::class,'update'])->whereNumber('id');
    Route::delete('/contacts/{id}',[\App\Http\Controllers\Api\ContactController::class,'destroy'])->whereNumber('id');
    Route::get('/stock/products',[\App\Http\Controllers\Api\StockController::class,'products']);
    Route::get('/stock',[\App\Http\Controllers\Api\StockController::class,'index']);
    Route::post('/stock',[\App\Http\Controllers\Api\StockController::class,'store']);
});

// Gio hang va dat hang cho khach chua dang nhap
Route::post('/guest/session',[\App\Http\Controllers\Api\GuestCartController::class,'session'])->middleware('throttle:20,1');
Route::prefix('guest')->middleware(\App\Http\Middleware\GuestSession::class)->group(function () {
    Route::get('/cart',[\App\Http\Controllers\Api\GuestCartController::class,'index']);
    Route::post('/cart/items',[\App\Http\Controllers\Api\GuestCartController::class,'change']);
    Route::put('/cart/items/{id}',[\App\Http\Controllers\Api\GuestCartController::class,'change'])->whereNumber('id');
    Route::delete('/cart/items/{id}',[\App\Http\Controllers\Api\GuestCartController::class,'change'])->whereNumber('id');
    Route::post('/checkout',[\App\Http\Controllers\Api\CheckoutController::class,'store'])->middleware('throttle:10,1');
    Route::post('/coupons/preview',[\App\Http\Controllers\Api\CouponController::class,'preview']);
    Route::get('/orders/{id}',[\App\Http\Controllers\Api\GuestCartController::class,'show'])->whereNumber('id');
    Route::post('/orders/{id}/payment',[\App\Http\Controllers\Api\GuestCartController::class,'payment'])->whereNumber('id')->middleware('throttle:10,1');
    Route::post('/orders/{id}/verify',[\App\Http\Controllers\Api\GuestCartController::class,'verify'])->whereNumber('id')->middleware('throttle:20,1');
});
