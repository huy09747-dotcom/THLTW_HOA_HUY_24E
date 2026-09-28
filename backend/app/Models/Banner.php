<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'tieude',
    'hinhanh',
    'duongdan',
    'thutu',
    'trangthai',
])]
class Banner extends Model
{
    protected $table = 'banner';

    public function scopeVisible($query)
    {
        return $query->where('trangthai', 'visible');
    }
}