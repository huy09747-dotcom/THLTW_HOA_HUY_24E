<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChudeBaiViet extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'chudebaiviet';

    protected $fillable = [
        'name', 'slug', 'sort_order', 'description',
        'status',
    ];

    public function baiviets(): HasMany
    {
        return $this->hasMany(BaiViet::class, 'topic_id');
    }
}