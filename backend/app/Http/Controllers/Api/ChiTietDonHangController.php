<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;

class ChiTietDonHangController extends Controller
{
    public function index()
    {
        $chitietdonhangs = ChiTietDonHang::with([
            'donhang',
            'sanpham'
        ])->get();

        return response()->json($chitietdonhangs);
    }
}