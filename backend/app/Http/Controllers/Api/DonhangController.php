<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonhangResource;
use App\Models\Donhang;
use Illuminate\Http\Request;

class DonhangController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1|max:2147483647',
        ]);

        return DonhangResource::collection(
            Donhang::orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }
}
