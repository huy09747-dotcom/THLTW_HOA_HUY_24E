<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tendanhmuc',
    'noibat',
    'trangthai',
])]
class Category extends Model
{
    protected $table = 'danhmuc';
    public $timestamps = false;
    public function products(): HasMany
    {
        return $this->hasMany(
            Product::class,
            'danhmuc_id',
            'id'
        );
    }
    public function scopeActive(Builder $query): void
    {
        $query->where('trangthai', 1);
    }
}