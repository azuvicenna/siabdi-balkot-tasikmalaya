<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        Setting::create([
            'key' => 'status_absensi',
            'value' => 'buka'
        ]);

        Setting::create([
            'key' => 'limit_voucher',
            'value' => '1'
        ]);
        
        Setting::create([
            'key' => 'geofencing',
            'value' => json_encode([
                'center_lat'      => -7.3817108,
                'center_lng'      => 108.6547367,
                'radius_meters'   => 1000000,
                // 'center_lat'      => -7.3164413671413895, // Lokasi real balai kota tasikmalaya
                // 'center_lng'      => 108.19693890463489,  // Lokasi real balai kota tasikmalaya
                // 'radius_meters'   => 150, // Radius 150 meter dari titik lokasi
                'max_upload_size' => 10240
            ])
        ]);
    }
}
