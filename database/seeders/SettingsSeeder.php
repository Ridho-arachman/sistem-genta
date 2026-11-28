<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<string, string> $values */
        $values = [
            'app_name_desktop' => 'GENTA Pangan Banten',
            'app_name_web' => 'GENTA Pangan Banten',
            'app_tagline' => 'Gerakan Nasional Tani untuk Kemandirian Pangan Banten.',
            'office_lat' => '-6.1202',
            'office_lng' => '106.1495',
            'office_address' => 'Gedung Pemprov Banten, Jl. Raya Serang–Jakarta, Serang, Banten',
        ];

        foreach ($values as $key => $value) {
            Settings::set($key, $value);
        }
    }
}
