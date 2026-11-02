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
    ['su-kien','Sự kiện','trai-nghiem-thanh-toan-sandbox','Trải nghiệm thanh toán sandbox tại HH Shop','Hoạt động thực hành quy trình đặt hàng và thanh toán thử nghiệm.','Trong buổi thực hành, nhóm sẽ trình diễn luồng giỏ hàng, đặt hàng và lựa chọn COD, MoMo, PayPal hoặc thẻ nội địa qua VNPAY. Các phương thức online chỉ khả dụng khi quản trị viên đã cấu hình tài khoản sandbox. Không sử dụng thông tin thẻ thật. Lịch tổ chức sẽ được nhóm cập nhật sau. Đây là nội dung sự kiện mẫu.'],
    ['su-kien','Sự kiện','11-11-con-so-dep-gia-cung-dep','11/11: Sale tới nóc, giá giảm tới 30%','Ngày 11/11, nhiều sản phẩm công nghệ tại HH Shop giảm đến 30%. Canh deal gọn lẹ, ví tiền thở phào!','Ngày đôi 11/11 năm nay, HH Shop mở hội săn deal: nhiều sản phẩm công nghệ được giảm đến 30%. Điện thoại, laptop và phụ kiện đang chờ chủ mới; bạn chỉ cần chọn món hợp gu, phần trăm giảm để HH Shop lo. Ưu đãi áp dụng cho các sản phẩm được đánh dấu khuyến mãi trong ngày 11/11/2026, số lượng có hạn. Nhớ ghé lại đúng ngày nhé — vì deal ngon thường chạy nhanh hơn lúc máy tính khởi động!','/images/news/hh-1111-sale.svg'],
    ['tin-tuc','Tin tức','gia-ram-tang-hh-shop-giu-gia','Giá RAM tăng, nhưng độ hào phóng của HH Shop thì không','Giá RAM nghe nói đang leo dốc, còn HH Shop vẫn giữ tinh thần giá dễ chịu.','Dạo này nghe tin giá RAM nhích lên, chắc mấy thanh RAM cũng đang tập cardio. Nhưng bạn cứ bình tĩnh: HH Shop vẫn cố giữ giá thật dễ chịu để chiếc máy tính không phải chạy bằng… niềm tin. Ghé xem các sản phẩm RAM và máy tính đang có trên cửa hàng; giá và khuyến mãi hiện tại được cập nhật ngay trên từng sản phẩm. RAM có thể tăng tốc, còn tụi mình thì không tăng độ khó cho chiếc ví của bạn!','/images/news/hh-ram-price.svg']
   ] as $postData){
    [$slug,$name,$postSlug,$title,$description,$detail]=$postData;$image=$postData[6]??null;
    $topic=ChudeBaiViet::withTrashed()->where('slug',$slug)->first();
    if(!$topic)$topic=ChudeBaiViet::create(['slug'=>$slug,'name'=>$name,'status'=>1,'sort_order'=>0,'description'=>$name.' của HH Shop']);
    if($topic->trashed())continue;
    if(BaiViet::withTrashed()->where('slug',$postSlug)->exists())continue;
    $post=new BaiViet(['slug'=>$postSlug,'post_type'=>'post']);
    $post->topic_id=$topic->id;$post->title=$title;$post->description=$description;$post->detail=$detail;$post->image=$image??null;$post->status=1;$post->created_by=$post->created_by?:$admin->id;
    $post->save();
   }
  });
 }
}
