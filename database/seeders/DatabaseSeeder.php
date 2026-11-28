<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            DapurSeeder::class,
            UserSeeder::class,
            CctvSeeder::class,
            LaporanSeeder::class,
            LaporanHasilSeeder::class,
            SpWarningSeeder::class,
            CctvAnomalyReportSeeder::class,
        ]);
    }
}
