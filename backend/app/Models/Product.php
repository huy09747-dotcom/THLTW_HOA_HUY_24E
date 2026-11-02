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
        'discount_percent',
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

    public function scopeWithReviewSummary($query)
    {
        $reviews = \Illuminate\Support\Facades\DB::table('product_reviews')
            ->whereColumn('product_id', 'sanpham.id')
            ->where('is_visible', true);

        return $query->select('sanpham.*')
            ->selectSub((clone $reviews)->selectRaw('COUNT(*)'), 'review_count')
            ->selectSub((clone $reviews)->selectRaw('AVG(rating)'), 'review_average');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function getFinalPriceAttribute(): ?int
    {
        if ($this->price_sale === null) {
            return null;
        }

        return (int) round((int) $this->price_sale * (100 - (int) $this->discount_percent) / 100);
    }
}