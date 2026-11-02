<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['logitech-pro-x.jpg', 'logitech-g502.jpg'] as $file) {
                if (!is_file(public_path('images/hierarchy/'.$file))) throw new \RuntimeException('Missing image: '.$file);
            }
            $brand = Brand::withTrashed()->firstOrCreate(['slug'=>'logitech'], [
                'name'=>'Logitech','status'=>1,
                'image'=>'/images/catalog-real/thuonghieu-4.jpg',
            ]);
            if ($brand->trashed()) throw new \RuntimeException('Thuong hieu Logitech dang trong thung rac.');
            $root = Category::withTrashed()->firstOrCreate(['slug'=>'gaming'], [
                'name'=>'Gaming','image'=>'/images/hierarchy/logitech-pro-x.jpg',
                'parent_id'=>0,'sort_order'=>20,'status'=>1,
                'description'=>'Thiết bị gaming dành cho góc chơi game và giải trí.',
            ]);
            if ($root->trashed()) throw new \RuntimeException('Danh muc Gaming dang trong thung rac.');
            foreach ([
                ['ban-phim-gaming','Bàn phím gaming','logitech-pro-x.jpg','Bàn phím Logitech G Pro X',2490000,1800000],
                ['chuot-gaming','Chuột gaming','logitech-g502.jpg','Chuột Logitech G502 HERO',1090000,700000],
            ] as $index=>[$slug,$name,$image,$productName,$sale,$buy]) {
                $category = Category::withTrashed()->firstOrCreate(['slug'=>$slug], [
                    'name'=>$name,'image'=>'/images/hierarchy/'.$image,'parent_id'=>$root->id,
                    'sort_order'=>$index+1,'status'=>1,'description'=>$name.' thuộc nhóm Gaming.',
                ]);
                if ($category->trashed()) throw new \RuntimeException('Danh muc con dang trong thung rac.');
                Product::withTrashed()->firstOrCreate(['sku'=>'HH-GAMING-'.($index+1)], [
                    'name'=>$productName,'slug'=>\Illuminate\Support\Str::slug($productName),
                    'category_id'=>$category->id,'brand_id'=>$brand->id,'price_buy'=>$buy,
                    'price_sale'=>$sale,'discount_percent'=>0,'qty'=>15,'image'=>'/images/hierarchy/'.$image,
                    'description'=>$productName.' dành cho góc gaming của bạn.',
                    'detail'=>$index===0 ? 'Bàn phím cơ dạng tenkeyless, đèn RGB và kết nối USB, phù hợp chơi game và làm việc.' : 'Chuột gaming có dây, thiết kế công thái học, nhiều nút lập trình và cảm biến HERO.',
                    'status'=>1,'unit'=>'cái','is_available'=>1,'is_featured'=>0,'prep_minutes'=>0,
                ]);
            }
        });
    }
}
