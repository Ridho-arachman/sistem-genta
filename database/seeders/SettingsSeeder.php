<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // Branding
            [
                'key' => 'app_name_desktop',
                'value' => 'GENTA Pangan Banten',
                'type' => 'string',
                'group' => 'branding',
                'label' => 'Nama Aplikasi (Desktop/App)',
                'description' => 'Nama aplikasi untuk desktop dan mobile app',
            ],
            [
                'key' => 'app_name_web',
                'value' => 'GENTA Banten',
                'type' => 'string',
                'group' => 'branding',
                'label' => 'Nama Instansi (Web)',
                'description' => 'Nama instansi untuk tampilan web publik',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Monitoring CCTV & Absensi Geotag Dapur',
                'type' => 'string',
                'group' => 'branding',
                'label' => 'Tagline',
                'description' => 'Tagline aplikasi',
            ],

            // Map / Office Location
            [
                'key' => 'office_lat',
                'value' => '-6.2088',
                'type' => 'float',
                'group' => 'map',
                'label' => 'Latitude Kantor',
                'description' => 'Koordinat latitude kantor pusat untuk Google Maps',
            ],
            [
                'key' => 'office_lng',
                'value' => '106.8456',
                'type' => 'float',
                'group' => 'map',
                'label' => 'Longitude Kantor',
                'description' => 'Koordinat longitude kantor pusat untuk Google Maps',
            ],
            [
                'key' => 'office_address',
                'value' => 'Jl. Pendidikan No. 1, Kec. Ciputat, Kota Tangerang Selatan, Banten',
                'type' => 'string',
                'group' => 'map',
                'label' => 'Alamat Kantor',
                'description' => 'Alamat lengkap kantor pusat',
            ],

            // General
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Zona Waktu',
                'description' => 'Zona waktu aplikasi',
            ],
        ];

        foreach ($defaults as $setting) {
            Settings::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
