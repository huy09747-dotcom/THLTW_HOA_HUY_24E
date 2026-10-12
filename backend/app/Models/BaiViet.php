<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaiViet extends Model
{
    use SoftDeletes;

    protected $table = 'baiviet';

    protected $fillable = [
        'topic_id', 'title', 'slug', 'detail', 'image',
        'post_type', 'description', 'status',
    ];

    public function chude(): BelongsTo
    {
        return $this->belongsTo(ChudeBaiViet::class, 'topic_id');
    }
}
