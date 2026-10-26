<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Banner extends Model {
 use SoftDeletes;
 protected $table='banner';
 protected $fillable=['name','description','image','link','sort_order','position','status','created_by','updated_by'];
 protected $casts=['status'=>'integer','sort_order'=>'integer'];
 public function scopeVisible($query){return $query->where('status',1);}
}
