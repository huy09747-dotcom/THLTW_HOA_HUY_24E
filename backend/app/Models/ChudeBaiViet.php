<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChudeBaiViet extends Model
{
    protected $table = 'chudebaiviet';

    protected $fillable = [
        'tenchude',
        'trangthai',
    ];

    public function baiviets(): HasMany
    {
        return $this->hasMany(BaiViet::class, 'chude_id');
    }
}