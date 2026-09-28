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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ==================== PRODUCT ====================

// Danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);

// Thêm sản phẩm
Route::post('/products', [ProductController::class, 'store']);

// Sửa sản phẩm
Route::put('/products/{id}', [ProductController::class, 'update']);

// Xóa sản phẩm
Route::delete('/products/{id}', [ProductController::class, 'destroy']);


// ==================== BRAND ====================

// Danh sách thương hiệu
Route::get('/brands', [BrandController::class, 'index']);

// Thêm thương hiệu
Route::post('/brands', [BrandController::class, 'store']);

// Sửa thương hiệu
Route::put('/brands/{id}', [BrandController::class, 'update']);

// Xóa thương hiệu
Route::delete('/brands/{id}', [BrandController::class, 'destroy']);


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
Route::post('/categories', [CategoryController::class, 'store']);

// Sửa danh mục
Route::put('/categories/{id}', [CategoryController::class, 'update']);

// Xóa tạm danh mục
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Danh sách danh mục trong thùng rác
Route::get('/categories-trash', [CategoryController::class, 'trash']);

// Khôi phục danh mục
Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']);

// Xóa vĩnh viễn danh mục
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']);


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

Route::get('/chitietgiohangs', [ChiTietGioHangController::class, 'index']);


// ==================== GIỎ HÀNG ====================

Route::get('/giohangs', [GioHangController::class, 'index']);
