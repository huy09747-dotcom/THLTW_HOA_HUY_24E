<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminReviewController extends Controller {
 public function index(Request $r){
  $data=$r->validate(['page'=>'sometimes|integer|min:1|max:100000','search'=>'nullable|string|max:255','rating'=>'nullable|integer|between:1,5','visibility'=>'nullable|in:visible,hidden']);
  $q=DB::table('product_reviews as r')->leftJoin('sanpham as p','p.id','=','r.product_id')->leftJoin('nguoidung as u','u.id','=','r.user_id');
  if(!empty($data['search'])){$search='%'.trim($data['search']).'%';$q->where(function($q)use($search){$q->where('p.name','like',$search)->orWhere('u.name','like',$search)->orWhere('r.comment','like',$search);});}
  if(!empty($data['rating']))$q->where('r.rating',$data['rating']);
  if(!empty($data['visibility']))$q->where('r.is_visible',$data['visibility']==='visible');
  $summary=['total'=>(clone $q)->count(),'visible'=>(clone $q)->where('r.is_visible',true)->count(),'hidden'=>(clone $q)->where('r.is_visible',false)->count(),'average'=>round((float)(clone $q)->where('r.is_visible',true)->avg('r.rating'),1)];
  $rows=$q->select('r.*','p.name as product_name','u.name as user_name')->orderByDesc('r.id')->paginate(10);
  return response()->json(['data'=>$rows->items(),'summary'=>$summary,'meta'=>['current_page'=>$rows->currentPage(),'last_page'=>$rows->lastPage(),'total'=>$rows->total()]]);
 }
 public function update(Request $r,int $id){$data=$r->validate(['is_visible'=>'required|boolean']);abort_unless(DB::table('product_reviews')->where('id',$id)->exists(),404);DB::table('product_reviews')->where('id',$id)->update(['is_visible'=>$data['is_visible'],'updated_at'=>now()]);return response()->json(['message'=>'Đã cập nhật trạng thái đánh giá.']);}
 public function destroy(int $id){abort_unless(DB::table('product_reviews')->where('id',$id)->delete(),404);return response()->json(['message'=>'Đã xóa đánh giá.']);}
}
