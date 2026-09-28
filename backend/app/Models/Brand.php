<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'thuonghieu';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'description',
        'image',
        'created_by',
        'title',
        'content',
        'replay_id',
        'updated_by',
        'status',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}