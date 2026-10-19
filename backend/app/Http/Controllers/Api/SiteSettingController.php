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
            'tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'address' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $settings = SiteSetting::firstOrNew(['id' => 1]);
        $settings->fill($data)->save();
        return response()->json(['data' => SiteSetting::publicData(), 'message' => 'Đã lưu cấu hình website.']);
    }
}
