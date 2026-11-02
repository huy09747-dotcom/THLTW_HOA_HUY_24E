<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogImagesSeeder extends Seeder
{
    public function run(): void
    {
        $items = json_decode(file_get_contents(database_path('seeders/data/catalog-images.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                if (!in_array($item['table'], ['sanpham', 'danhmuc', 'thuonghieu'], true)) {
                    throw new \RuntimeException('Invalid table');
                }
                if (!is_file(public_path($item['image']))) {
                    throw new \RuntimeException('Missing image: '.$item['image']);
                }
                DB::table($item['table'])->where('name', $item['name'])
                    ->update(['image' => $item['image']]);
            }
        });
    }
}
