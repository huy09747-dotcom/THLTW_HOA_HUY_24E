<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
class SiteSettingController extends Controller
{
    public function show() { return response()->json(['data' => SiteSetting::publicData()]); }
    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:150',
            'contact_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+().\s-]+$/'],
            'address' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'logo' => 'nullable|url:http,https|max:255',
            'favicon' => 'nullable|url:http,https|max:255',
            'facebook' => 'nullable|url:http,https|max:255',
            'instagram' => 'nullable|url:http,https|max:255',
            'youtube' => 'nullable|url:http,https|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $settings = SiteSetting::orderBy('id')->first() ?? new SiteSetting();
        foreach ($data as $api => $value) $settings->{SiteSetting::FIELDS[$api]} = $value;
        $settings->save();
        return response()->json(['data' => SiteSetting::publicData(), 'message' => 'Đã lưu cấu hình website.']);
    }
}
