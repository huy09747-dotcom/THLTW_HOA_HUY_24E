<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.required' => 'Vui long chon hinh anh.',
            'image.image' => 'Tep phai la hinh anh.',
            'image.mimes' => 'Chi chap nhan JPG, PNG hoac WebP.',
            'image.max' => 'Hinh anh toi da 5 MB.',
        ]);

        $path = $request->file('image')->store('images', 'public');
        abort_unless($path, 500, 'Khong the luu hinh anh.');

        return response()->json(['image' => '/storage/'.$path], 201);
    }
}