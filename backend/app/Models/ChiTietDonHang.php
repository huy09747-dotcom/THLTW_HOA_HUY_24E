<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDonHang extends Model
{
    protected $table = 'chitietdonhang';

    public $timestamps = false;

    protected $fillable = [
        'donhang_id',
        'sanpham_id',
        'tensanpham',
        'dongia',
        'soluong',
        'thanhtien',
    ];

    protected $casts = [
        'dongia' => 'decimal:2',
        'soluong' => 'integer',
        'thanhtien' => 'decimal:2',
    ];

    public function donhang(): BelongsTo
    {
        return $this->belongsTo(Donhang::class, 'donhang_id');
    }

    public function sanpham(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sanpham_id');
    }
}