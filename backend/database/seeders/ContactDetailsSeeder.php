<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ContactDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::orderBy('id')->firstOrFail();
        $settings->email = 'khanhhoatrinh4@gmail.com';
        $settings->sodienthoai = '0398716527';
        $settings->facebook = 'https://web.facebook.com/NMHuy10105';
        $settings->save();
    }
}
