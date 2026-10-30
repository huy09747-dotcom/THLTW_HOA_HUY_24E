<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReviewController extends Controller {
 private function product(int $id): void {Product::where('status',1)->findOrFail($id);}
 public function index(Request $request,int $id) {
  $this->product($id);$request->validate(['page'=>'sometimes|integer|min:1|max:100000']);
  $query=DB::table('product_reviews')->where('product_id',$id)->where('is_visible',true);
  $summary=['count'=>(clone $query)->count(),'average'=>round((float)(clone $query)->avg('rating'),1)];
  $rows=$query->leftJoin('nguoidung','nguoidung.id','=','product_reviews.user_id')->select('product_reviews.id','product_reviews.rating','product_reviews.comment','product_reviews.created_at','nguoidung.name')->orderByDesc('product_reviews.id')->paginate(10);
  return response()->json(['data'=>$rows->items(),'summary'=>$summary,'meta'=>['current_page'=>$rows->currentPage(),'last_page'=>$rows->lastPage()]]);
 }
 public function save(Request $request,int $id) {
  $this->product($id);$data=$request->validate(['rating'=>'required|integer|between:1,5','comment'=>'required|string|max:2000']);
  DB::table('product_reviews')->upsert([array_merge($data,['product_id'=>$id,'user_id'=>$request->user()->id,'is_visible'=>true,'created_at'=>now(),'updated_at'=>now()])],['product_id','user_id'],['rating','comment','is_visible','updated_at']);
  return response()->json(['message'=>'Đã lưu đánh giá của bạn.']);
 }
 public function mine(Request $request,int $id){$this->product($id);return response()->json(['data'=>DB::table('product_reviews')->where('product_id',$id)->where('user_id',$request->user()->id)->where('is_visible',true)->first(['rating','comment'])]);}
 public function destroy(Request $request,int $id){$this->product($id);DB::table('product_reviews')->where('product_id',$id)->where('user_id',$request->user()->id)->delete();return response()->json(['message'=>'Đã xóa đánh giá của bạn.']);}
}
