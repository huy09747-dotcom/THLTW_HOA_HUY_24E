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

Route::get('/donhangs', [DonhangController::class, 'index']);


// ==================== CHI TIẾT ĐƠN HÀNG ====================

Route::get('/chitietdonhangs', [ChiTietDonHangController::class, 'index']);


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
