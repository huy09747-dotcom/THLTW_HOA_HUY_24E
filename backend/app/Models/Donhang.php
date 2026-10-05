<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donhang extends Model
{
    use SoftDeletes;

    protected $table = 'donhang';

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'address', 'note', 'status',
        'code', 'order_type', 'subtotal', 'discount', 'total',
        'payment_status', 'payment_method',
    ];

    public function nguoidung()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(ChiTietDonHang::class, 'order_id');
    }
}
