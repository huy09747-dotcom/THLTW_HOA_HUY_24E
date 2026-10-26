<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Banner;
class BannerSeeder extends Seeder {
 public function run():void {
  $items=[['Công nghệ mới. Cảm hứng mỗi ngày.','Khám phá những chiếc điện thoại đồng hành cùng phong cách của bạn.','phone'],['Bật ý tưởng. Mở tương lai.','Laptop cho học tập, làm việc và những ý tưởng không giới hạn.','laptop'],['Kết nối trọn vẹn. Tận hưởng mỗi ngày.','Tai nghe và phụ kiện tinh tế cho mọi khoảnh khắc của bạn.','accessories']];
  foreach($items as $index=>$item){Banner::withTrashed()->firstOrCreate(['image'=>'/images/banners/hh-'.$item[2].'.png'],['name'=>$item[0],'description'=>$item[1],'link'=>'/products','sort_order'=>$index+1,'position'=>'slideshow','status'=>1,'created_by'=>1]);}
 }
}
