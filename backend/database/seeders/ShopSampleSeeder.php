<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $image = fn (string $path) => 'https://cdn.dummyjson.com/product-images/'.$path.'/thumbnail.webp';

            $brand = Brand::withTrashed()->firstOrCreate(
                ['name' => 'HH Shop'],
                [
                    'slug' => 'hh-shop',
                    'image' => $image('laptops/apple-macbook-pro-14-inch-space-grey'),
                    'description' => 'Sản phẩm công nghệ HH Shop.',
                    'content' => 'Điện thoại, máy tính và phụ kiện.',
                    'status' => 1,
                ]
            );

            if ($brand->trashed()) {
                throw new \RuntimeException('Thuong hieu HH Shop dang trong thung rac. Can kiem tra truoc khi seed.');
            }

            $groups = [
                ['Điện thoại', 'dien-thoai', 'smartphones/iphone-13-pro'],
                ['Laptop', 'laptop', 'laptops/apple-macbook-pro-14-inch-space-grey'],
                ['Tai nghe', 'tai-nghe', 'mobile-accessories/apple-airpods'],
                ['Sạc điện thoại', 'sac-dien-thoai', 'mobile-accessories/apple-iphone-charger'],
                ['Phụ kiện', 'phu-kien', 'mobile-accessories/apple-magsafe-battery-pack'],
            ];

            $categories = [];
            foreach ($groups as $index => [$name, $slug, $picture]) {
                $category = Category::withTrashed()->firstOrCreate(
                    ['slug' => 'hh-sample-'.$slug],
                    [
                        'name' => $name,
                        'image' => $image($picture),
                        'parent_id' => 0,
                        'sort_order' => $index + 1,
                        'description' => $name.' tại HH Shop.',
                        'status' => 1,
                    ]
                );
                if ($category->trashed()) {
                    throw new \RuntimeException('Danh muc mau dang trong thung rac: '.$slug);
                }
                $categories[] = $category;
            }

            $items = [
                ['iPhone 13 Pro', 0, 15990000, 'smartphones/iphone-13-pro'],
                ['Oppo A57', 0, 3990000, 'smartphones/oppo-a57'],
                ['Oppo F19 Pro Plus', 0, 6990000, 'smartphones/oppo-f19-pro-plus'],
                ['MacBook Pro 14', 1, 32990000, 'laptops/apple-macbook-pro-14-inch-space-grey'],
                ['Huawei Matebook X Pro', 1, 22990000, 'laptops/huawei-matebook-x-pro'],
                ['Asus Zenbook Pro', 1, 28990000, 'laptops/asus-zenbook-pro-dual-screen-laptop'],
                ['Apple AirPods', 2, 2990000, 'mobile-accessories/apple-airpods'],
                ['AirPods Max Silver', 2, 10990000, 'mobile-accessories/apple-airpods-max-silver'],
                ['Beats Flex', 2, 1290000, 'mobile-accessories/beats-flex-wireless-earphones'],
                ['Sạc iPhone', 3, 490000, 'mobile-accessories/apple-iphone-charger'],
                ['Sạc không dây AirPower', 3, 990000, 'mobile-accessories/apple-airpower-wireless-charger'],
                ['Pin MagSafe', 3, 1490000, 'mobile-accessories/apple-magsafe-battery-pack'],
                ['Ốp lưng iPhone 12', 4, 390000, 'mobile-accessories/iphone-12-silicone-case-with-magsafe-plum'],
                ['Gậy selfie', 4, 190000, 'mobile-accessories/selfie-stick-monopod'],
                ['Đèn selfie', 4, 290000, 'mobile-accessories/selfie-lamp-with-iphone'],
            ];

            foreach ($items as $index => [$name, $group, $price, $picture]) {
                Product::withTrashed()->firstOrCreate(
                    ['sku' => sprintf('HH-SAMPLE-%03d', $index + 1)],
                    [
                        'name' => $name,
                        'slug' => 'hh-sample-product-'.($index + 1),
                        'category_id' => $categories[$group]->id,
                        'brand_id' => $brand->id,
                        'price_buy' => (int) ($price * 0.7),
                        'price_sale' => $price,
                        'image' => $image($picture),
                        'qty' => 10 + $index,
                        'detail' => $name.'. Thiết kế tiện dụng cho công việc, học tập và giải trí.',
                        'description' => $name.' tại HH Shop.',
                        'status' => 1,
                        'unit' => 'cái',
                        'prep_minutes' => 0,
                        'is_available' => 1,
                        'is_featured' => $index < 4,
                    ]
                );
            }
        });
    }
}