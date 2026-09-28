<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donhang extends Model
{
    protected $table = 'donhang';

    protected $fillable = [
        'nguoidung_id',
        'khuyenmai_id',
        'hoten_nguoinhan',
        'sodienthoai',
        'diachi_giaohang',
        'tongtien',
        'tiengiam',
        'phuongthuc_thanhtoan',
        'trangthai',
        'ghichu',
    ];

    protected $casts = [
        'tongtien' => 'decimal:2',
        'tiengiam' => 'decimal:2',
    ];

    public function nguoidung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoidung_id');
    }
}