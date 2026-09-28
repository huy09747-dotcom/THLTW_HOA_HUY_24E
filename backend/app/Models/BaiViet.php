<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaiViet extends Model
{
    protected $table = 'baiviet';

    protected $fillable = [
        'chude_id',
        'tieude',
        'noidung',
        'hinhanh',
        'trangthai',
    ];

    public function chude(): BelongsTo
    {
        return $this->belongsTo(ChudeBaiViet::class, 'chude_id');
    }
}