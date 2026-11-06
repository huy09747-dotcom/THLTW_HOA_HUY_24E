<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
class BannerController extends Controller {
 public function index(){return response()->json(Banner::visible()->where('position','slideshow')->orderBy('sort_order')->orderBy('id')->get());}
 public function adminIndex(Request $r){return response()->json(\App\Support\AdminFilters::apply(Banner::query(),$r,['name'])->orderBy('sort_order')->orderBy('id')->paginate(10));}
 private function validated(Request $request):array {
  $safeUrl=function($attribute,$value,$fail){
   if(preg_match('/[\\\\\x00-\x20]/',$value) || !(preg_match('~^/(?!/)~',$value) || (filter_var($value,FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($value,PHP_URL_SCHEME)),['http','https'])))) $fail('Đường dẫn phải bắt đầu bằng / hoặc http(s)://.');
  };
  return $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string|max:2000','image'=>['required','string','max:255',$safeUrl],'link'=>['nullable','string','max:2048',$safeUrl],'sort_order'=>'required|integer|min:0|max:9999','position'=>'required|in:slideshow,advertise','status'=>'required|integer|in:0,1']);
 }
 public function store(Request $request){$data=$this->validated($request);$data['created_by']=$request->user()->id;return response()->json(['data'=>Banner::create($data)],201);}
 public function update(Request $request,int $id){$banner=Banner::findOrFail($id);$data=$this->validated($request);$data['updated_by']=$request->user()->id;$banner->update($data);return response()->json(['data'=>$banner]);}
 public function destroy(int $id){Banner::findOrFail($id)->delete();return response()->json(['message'=>'Đã xóa banner.']);}
}
