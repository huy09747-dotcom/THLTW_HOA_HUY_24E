<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiViet;

class BaiVietController extends Controller
{
    public function index()
    {
        $baiviets = BaiViet::where('post_type', 'post')->where('status', 1)->get();

        return response()->json($baiviets);
    }
}