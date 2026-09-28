<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
<<<<<<< Updated upstream

Route::get('/products', [ProductController::class, 'index']);

<<<<<<< Updated upstream
=======
// Thêm sản phẩm
Route::post('/products', [ProductController::class, 'store']);

// Sửa sản phẩm
Route::put('/products/{id}', [ProductController::class, 'update']);

// Xóa sản phẩm
Route::delete('/products/{id}', [ProductController::class, 'destroy']);


// ==================== BRAND ====================

// Danh sách thương hiệu
=======
//Api Product
Route::get('/products', [ProductController::class, 'index']);
//Api Brand
>>>>>>> Stashed changes
>>>>>>> Stashed changes
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong'
    ]);
});
<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream


// ==================== CATEGORY ====================

// Danh sách danh mục
>>>>>>> Stashed changes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::get('/categories-trash', [CategoryController::class, 'trash']);
Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']);
<<<<<<< Updated upstream
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']);
=======

// Xóa vĩnh viễn danh mục
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']);


// ==================== BANNER ====================

=======
>>>>>>> Stashed changes
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

Route::get('/chitietgiohangs', [ChiTietGioHangController::class, 'index']);


// ==================== GIỎ HÀNG ====================

Route::get('/giohangs', [GioHangController::class, 'index']);
<<<<<<< Updated upstream
=======
//Api Category
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::get('/categories-trash', [CategoryController::class, 'trash']);
Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']);
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']);
>>>>>>> Stashed changes
>>>>>>> Stashed changes
