<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteSetting extends Model
{
    public $incrementing = false;
    protected $fillable = ['site_name', 'tagline', 'contact_email', 'contact_phone', 'address', 'opening_hours', 'footer_text', 'meta_description'];
    public const DEFAULTS = [
        'site_name' => 'HH Shop', 'tagline' => 'Công nghệ mỗi ngày',
        'contact_email' => '', 'contact_phone' => '', 'address' => '', 'opening_hours' => '',
        'footer_text' => 'Công nghệ gần hơn với bạn.',
        'meta_description' => 'Khám phá sản phẩm công nghệ tại HH Shop.',
    ];
    public static function publicData(): array
    {
        $settings = static::find(1);
        return $settings ? array_replace(self::DEFAULTS, $settings->only(array_keys(self::DEFAULTS))) : self::DEFAULTS;
    }
}
