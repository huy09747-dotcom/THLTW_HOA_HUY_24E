<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'sanpham';

    public $timestamps = false;

    protected $fillable = [
        'danhmuc_id',
        'thuonghieu_id',
        'tensanpham',
        'hinhanh',
        'gia',
        'giakhuyenmai',
        'mota',
        'soluongton',
        'trangthai',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'thuonghieu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }
}