<?php
namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteContentSampleSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/site-content.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            $admin = User::where('roles', 'admin')->where('status', 1)->orderBy('id')->firstOrFail();
            foreach ($data['pages'] as $sample) {
                if (count($sample['sections']) !== 10) throw new \RuntimeException('Mỗi trang mẫu phải có 10 mục.');
                if (BaiViet::withTrashed()->where('slug', $sample['slug'])->exists()) {
                    $this->command?->info('Giữ trang đã có: '.$sample['slug']);
                    continue;
                }
                $sections = [];
                foreach ($sample['sections'] as $index => [$heading, $body]) $sections[] = ($index + 1).'. '.$heading."\n".$body;
                $page = new BaiViet([
                    'title' => $sample['title'], 'slug' => $sample['slug'],
                    'description' => $sample['description'], 'detail' => implode("\n\n", $sections),
                    'post_type' => 'page', 'status' => 1,
                ]);
                $page->created_by = $admin->id;
                $page->updated_by = $admin->id;
                $page->save();
                $this->command?->info('Đã xuất bản: '.$sample['slug'].' (10 mục)');
            }
            $settings = SiteSetting::orderBy('id')->first() ?? new SiteSetting();
            $origin = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/');
            $values = $data['settings'] + ['logo' => $origin.'/hh-shop-demo.svg', 'favicon' => $origin.'/default-favicon.ico'];
            foreach ($values as $column => $value) {
                if (trim((string) $settings->{$column}) === '') $settings->{$column} = $value;
            }
            $settings->save();
            $this->command?->info('Đã bổ sung các trường cấu hình còn trống; giữ dữ liệu có sẵn.');
        });
    }
}
