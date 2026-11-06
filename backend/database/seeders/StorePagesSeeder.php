<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StorePagesSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/site-content.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            foreach ($data['pages'] as $page) {
                $record = BaiViet::where('slug', $page['slug'])->where('post_type', 'page')->firstOrFail();
                $sections = [];
                foreach ($page['sections'] as $i => [$heading, $text]) {
                    $sections[] = ($i + 1).'. '.$heading."\n".$text;
                }
                $record->update([
                    'title' => $page['title'], 'description' => $page['description'],
                    'detail' => implode("\n\n", $sections),
                ]);
            }
            DB::table('cauhinh')->where('seo_description', 'like', '%Đồ án THLTW%')
                ->update(['seo_description' => $data['settings']['seo_description']]);
        });
    }
}
