<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\BaiViet;
use App\Models\ChudeBaiViet;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
class NewsController extends Controller {
 private function query(){return BaiViet::where('post_type','post')->where('status',1)->whereHas('chude',fn($q)=>$q->where('status',1))->with('chude');}
 public function index(Request $r){$data=$r->validate(['page'=>'sometimes|integer|min:1|max:100000','topic'=>'nullable|string|max:255']);$q=$this->query();if(!empty($data['topic']))$q->whereHas('chude',fn($q)=>$q->where('slug',$data['topic']));return PostResource::collection($q->latest('id')->paginate(9));}
 public function show(string $slug){return new PostResource($this->query()->where('slug',$slug)->firstOrFail());}
 public function topics(){return response()->json(['data'=>ChudeBaiViet::where('status',1)->orderBy('name')->get(['id','name','slug'])]);}
}
