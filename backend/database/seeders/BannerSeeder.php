<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Banner;
class BannerSeeder extends Seeder {
 public function run():void {
  $items=[['Công nghệ mới. Cảm hứng mỗi ngày.','Khám phá những chiếc điện thoại đồng hành cùng phong cách của bạn.','phone'],['Bật ý tưởng. Mở tương lai.','Laptop cho học tập, làm việc và những ý tưởng không giới hạn.','laptop'],['Kết nối trọn vẹn. Tận hưởng mỗi ngày.','Tai nghe và phụ kiện tinh tế cho mọi khoảnh khắc của bạn.','accessories'],['11/11: Sale tới nóc, giảm đến 30%!','Nhiều sản phẩm công nghệ giảm đến 30% ngày 11/11. Deal ngon chờ bạn, ví tiền xin phép vui sau!','11-11-sale','/images/banners/hh-1111-sale.svg','/news?topic=su-kien']];
  foreach($items as $index=>$item){$image=$item[3]??('/images/banners/hh-'.$item[2].'.png');Banner::withTrashed()->firstOrCreate(['image'=>$image],['name'=>$item[0],'description'=>$item[1],'link'=>$item[4]??'/products','sort_order'=>$index+1,'position'=>'slideshow','status'=>1,'created_by'=>1]);}
 }
}
