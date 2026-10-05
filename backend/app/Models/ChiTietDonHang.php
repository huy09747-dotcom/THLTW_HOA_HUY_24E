<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'chitietdonhang';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'item_name', 'price', 'qty', 'amount',
    ];

    public function donhang()
    {
        return $this->belongsTo(Donhang::class, 'order_id');
    }

    public function sanpham()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
