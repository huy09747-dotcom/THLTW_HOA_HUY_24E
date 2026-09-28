<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donhang;

class DonhangController extends Controller
{
    public function index()
    {
        $donhangs = Donhang::with('nguoidung')->get();

        return response()->json($donhangs);
    }
}