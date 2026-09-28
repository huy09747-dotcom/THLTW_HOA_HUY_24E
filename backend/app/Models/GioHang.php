<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GioHang extends Model
{
    protected $table = 'giohang';

    public function nguoidung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoidung_id');
    }

    public function chitietgiohang(): HasMany
    {
        return $this->hasMany(ChiTietGioHang::class, 'giohang_id');
    }
}