<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteSetting extends Model
{
    protected $table = 'cauhinh';
    // API names are mapped to the existing database columns.
    public const FIELDS = [
        'site_name'=>'tenwebsite', 'contact_email'=>'email', 'contact_phone'=>'sodienthoai',
        'address'=>'diachi', 'meta_description'=>'seo_description', 'seo_title'=>'seo_title',
        'seo_keywords'=>'seo_keywords', 'logo'=>'logo', 'favicon'=>'favicon',
        'facebook'=>'facebook', 'instagram'=>'instagram', 'youtube'=>'youtube',
    ];
    public const DEFAULTS = [
        'site_name'=>'HH Shop', 'contact_email'=>'', 'contact_phone'=>'', 'address'=>'',
        'meta_description'=>'Khám phá sản phẩm công nghệ tại HH Shop.',
        'seo_title'=>'', 'seo_keywords'=>'', 'logo'=>'', 'favicon'=>'',
        'facebook'=>'', 'instagram'=>'', 'youtube'=>'',
        'tagline'=>'Công nghệ mỗi ngày', 'footer_text'=>'Công nghệ gần hơn với bạn.',
    ];
    public static function publicData(): array
    {
        $settings = static::orderBy('id')->first();
        $data = self::DEFAULTS;
        if ($settings) {
            foreach (self::FIELDS as $api => $column) $data[$api] = $settings->{$column} ?? '';
        }
        return $data;
    }
}
