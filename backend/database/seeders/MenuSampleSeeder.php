<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Menu;
class MenuSampleSeeder extends Seeder {
 public function run():void {foreach([['Trang chủ','/'],['Sản phẩm','/products'],['Danh mục','/#danh-muc'],['Bài viết','/news'],['Giới thiệu','/about'],['Liên hệ','/contact']] as $i=>[$name,$link])Menu::withTrashed()->firstOrCreate(['link'=>$link,'type'=>'custom'],['name'=>$name,'status'=>1,'sort_order'=>$i+1,'created_by'=>1]);}
}
