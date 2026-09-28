<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GioHang;

class GioHangController extends Controller
{
    public function index()
    {
        $giohangs = GioHang::with([
            'nguoidung',
            'chitietgiohang'
        ])->get();

        return response()->json($giohangs);
    }
}