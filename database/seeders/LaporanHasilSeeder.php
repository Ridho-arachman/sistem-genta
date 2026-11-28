<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\LaporanHasil;
use Illuminate\Database\Seeder;

class LaporanHasilSeeder extends Seeder
{
    /** @var array<int, string> */
    private const INDIKATORS = [
        'Kebersihan dan sanitasi',
        'Penyimpanan bahan',
        'Proses pengolahan',
        'Distribusi makanan',
        'Peralatan dan fasilitas',
    ];

    /** @var array<string, array<int, string>> */
    private const HASIL_BY_STATUS = [
        'diperiksa' => ['diperiksa', 'diperiksa', 'temuan_sedang', 'diperiksa', 'diperiksa'],
        'temuan' => ['diperiksa', 'temuan_kritis', 'diperiksa', 'diperiksa', 'diperiksa'],
        'tindak lanjut' => ['diperiksa', 'diperiksa', 'temuan_sedang', 'temuan_sedang', 'diperiksa'],
        'selesai' => ['diperiksa', 'diperiksa', 'diperiksa', 'diperiksa', 'diperiksa'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (LaporanHasil::query()->exists()) {
            return;
        }

        // Indikator mengikuti daftar tetap di LaporanController.
        foreach (Laporan::query()->get() as $laporan) {
            foreach (self::INDIKATORS as $index => $indikator) {
                LaporanHasil::factory()->for($laporan)->create([
                    'indikator' => $indikator,
                    'hasil' => self::HASIL_BY_STATUS[$laporan->status][$index],
                    'created_by' => $laporan->created_by,
                    'updated_by' => $laporan->updated_by,
                ]);
            }
        }
    }
}
