<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiViet;

class BaiVietController extends Controller
{
    public function index()
    {
        $baiviets = BaiViet::all();

        return response()->json($baiviets);
    }
}