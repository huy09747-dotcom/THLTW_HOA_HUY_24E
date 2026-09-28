<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietGioHang extends Model
{
    protected $table = 'chitietgiohang';

    public $timestamps = false;

    public function giohang(): BelongsTo
    {
        return $this->belongsTo(GioHang::class, 'giohang_id');
    }

    public function sanpham(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sanpham_id');
    }
}