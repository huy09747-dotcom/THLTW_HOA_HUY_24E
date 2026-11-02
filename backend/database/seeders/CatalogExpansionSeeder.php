<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Category,Brand,Product};
class CatalogExpansionSeeder extends Seeder {
 public function run():void {DB::transaction(function(){
 $groups=[['Bàn phím','ban-phim','Logitech'],['Màn hình','man-hinh','Dell'],['Đồng hồ thông minh','dong-ho-thong-minh','Xiaomi'],['Thiết bị lưu trữ','thiet-bi-luu-tru','Kingston'],['Loa Bluetooth','loa-bluetooth','JBL']];
 $names=[['Bàn phím Logitech K120','Bàn phím Logitech K380','Bàn phím Logitech MX Keys'],['Màn hình Dell P2422H','Màn hình Dell S2721HN','Màn hình Dell U2723QE'],['Đồng hồ Xiaomi Smart Band 8','Đồng hồ Xiaomi Redmi Watch 3','Đồng hồ Xiaomi Watch S1'],['USB Kingston DataTraveler 64GB','Thẻ nhớ Kingston Canvas 128GB','SSD Kingston NV2 1TB'],['Loa JBL Go 3','Loa JBL Flip 6','Loa JBL Charge 5']];
 $prices=[[199000,790000,2390000],[3290000,4190000,11990000],[790000,1490000,2990000],[149000,299000,1590000],[890000,2490000,3490000]];
 foreach($groups as $i=>[$name,$slug,$brandName]){
  $image='/images/catalog/'.$slug.'.svg';
  $category=Category::withTrashed()->firstOrCreate(['slug'=>'hh-expand-'.$slug],['name'=>$name,'image'=>$image,'description'=>$name.' dành cho học tập, làm việc và giải trí.','parent_id'=>0,'sort_order'=>10+$i,'status'=>1]);
  $brand=Brand::withTrashed()->firstOrCreate(['slug'=>strtolower($brandName)],['name'=>$brandName,'description'=>'Danh mục sản phẩm '.$brandName.' tại HH Shop.','content'=>'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.','status'=>1]);
  if($category->trashed()||$brand->trashed())throw new \RuntimeException('Danh mục hoặc thương hiệu đang trong thùng rác.');
  foreach($names[$i] as $j=>$productName){$price=$prices[$i][$j];Product::withTrashed()->firstOrCreate(['sku'=>sprintf('HH-EXPAND-%03d',$i*3+$j+1)],['name'=>$productName,'slug'=>'hh-expand-'.\Illuminate\Support\Str::slug($productName),'category_id'=>$category->id,'brand_id'=>$brand->id,'price_buy'=>(int)($price*.7),'price_sale'=>$price,'discount_percent'=>$j===0?10:0,'qty'=>20+$i*3+$j,'image'=>$image,'description'=>$productName.' — lựa chọn cho không gian công nghệ của bạn.','detail'=>$productName."\nSản phẩm mẫu thuộc nhóm ".$name.".\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.",'status'=>1,'unit'=>'cái','prep_minutes'=>0,'is_available'=>1,'is_featured'=>$j===1]);}
 }
 });}
}
