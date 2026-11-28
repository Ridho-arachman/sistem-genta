<?php

namespace Database\Seeders;

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class CctvAnomalyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (CctvAnomalyReport::query()->exists()) {
            return;
        }

        $admin = User::query()->where('role', 'admin')->first();
        $cctvs = Cctv::query()->orderBy('nama')->get();

        if (! $admin || $cctvs->isEmpty()) {
            return;
        }

        /** @var array<int, array{description: string, severity: string, status: string}> $reports */
        $reports = [
            ['description' => 'Gambar kamera area masak berkedip dan sinyal sering terputus.', 'severity' => 'tinggi', 'status' => 'baru'],
            ['description' => 'Area gudang tertutup keranjang sehingga tidak terpantau.', 'severity' => 'sedang', 'status' => 'diproses'],
            ['description' => 'Video kamera pintu masuk buram saat malam hari.', 'severity' => 'sedang', 'status' => 'selesai'],
            ['description' => 'Kamera terputus setelah pemeliharaan jaringan.', 'severity' => 'rendah', 'status' => 'baru'],
        ];

        foreach ($reports as $index => $report) {
            CctvAnomalyReport::query()->create(array_merge($report, [
                'cctv_id' => $cctvs[$index % $cctvs->count()]->id,
                'admin_id' => $admin->id,
            ]));
        }
    }
}
