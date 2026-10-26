<?php
namespace Database\Seeders;
use App\Models\BaiViet;
use App\Models\ChudeBaiViet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NewsSampleSeeder extends Seeder {
 public function run(): void {
  DB::transaction(function(){
   $admin=User::where('roles','admin')->where('status',1)->firstOrFail();
   foreach([
    ['tin-tuc','Tin tức','hh-shop-cap-nhat-trai-nghiem-mua-sam','HH Shop cập nhật trải nghiệm mua sắm','Khám phá đánh giá sản phẩm và theo dõi đơn hàng.','HH Shop bổ sung phần chấm điểm và đánh giá tại trang sản phẩm. Đăng nhập để chia sẻ trải nghiệm, cập nhật hoặc xóa đánh giá của mình. Bạn cũng có thể mở Đơn hàng của tôi để theo dõi trạng thái và thông tin nhận hàng. Đây là bài viết mẫu phục vụ đồ án.'],
    ['su-kien','Sự kiện','trai-nghiem-thanh-toan-sandbox','Trải nghiệm thanh toán sandbox tại HH Shop','Hoạt động thực hành quy trình đặt hàng và thanh toán thử nghiệm.','Trong buổi thực hành, nhóm sẽ trình diễn luồng giỏ hàng, đặt hàng và lựa chọn COD, MoMo, PayPal hoặc thẻ nội địa qua VNPAY. Các phương thức online chỉ khả dụng khi quản trị viên đã cấu hình tài khoản sandbox. Không sử dụng thông tin thẻ thật. Lịch tổ chức sẽ được nhóm cập nhật sau. Đây là nội dung sự kiện mẫu.']
   ] as [$slug,$name,$postSlug,$title,$description,$detail]){
    $topic=ChudeBaiViet::withTrashed()->where('slug',$slug)->first();
    if(!$topic)$topic=ChudeBaiViet::create(['slug'=>$slug,'name'=>$name,'status'=>1,'sort_order'=>0,'description'=>$name.' của HH Shop']);
    if($topic->trashed())continue;
    if(!BaiViet::withTrashed()->where('slug',$postSlug)->exists()){$post=new BaiViet(['topic_id'=>$topic->id,'slug'=>$postSlug,'title'=>$title,'description'=>$description,'detail'=>$detail,'post_type'=>'post','status'=>1]);$post->created_by=$admin->id;$post->save();}
   }
  });
 }
}
