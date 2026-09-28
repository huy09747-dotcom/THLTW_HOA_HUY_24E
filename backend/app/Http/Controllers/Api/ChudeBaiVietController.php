<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChudeBaiViet;

class ChudeBaiVietController extends Controller
{
    public function index()
    {
        $chudes = ChudeBaiViet::all();

        return response()->json($chudes);
    }
}