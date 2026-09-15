<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name' => 'JMCL JEANS LTD',
            'store_email' => 'info@jmcljeans.test',
            'store_phone' => '+8801XXXXXXXXX',
            'store_address' => 'Dhaka, Bangladesh',
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'facebook_url' => '',
            'instagram_url' => '',
            'homepage_hero_title' => 'Premium Denim. Modern Fit.',
            'homepage_hero_subtitle' => 'Discover the JMCL JEANS LTD collection — crafted for comfort, built for style.',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
