<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'sanpham';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'price_buy',
        'price_sale',
        'image',
        'qty',
        'detail',
        'description',
        'created_by',
        'updated_by',
        'status',
        'sku',
        'unit',
        'prep_minutes',
        'is_available',
        'is_featured',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}