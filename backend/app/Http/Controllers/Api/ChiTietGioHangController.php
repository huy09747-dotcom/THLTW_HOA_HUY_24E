<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietGioHang;

class ChiTietGioHangController extends Controller
{
    public function index()
    {
        $chitietgiohang = ChiTietGioHang::with([
            'giohang',
            'sanpham'
        ])->get();

        return response()->json($chitietgiohang);
    }
}