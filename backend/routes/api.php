<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong'
    ]);
});
Route::get('/categories', [CategoryController::class, 'index']);
