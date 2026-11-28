<?php

namespace Database\Seeders;

use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpWarningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (SpWarning::query()->exists()) {
            return;
        }

        $admin = User::query()->where('role', 'admin')->first();
        $pengawas = User::query()->where('role', 'pengawas')->orderBy('nama')->get();

        if (! $admin || $pengawas->isEmpty()) {
            return;
        }

        /** @var array<int, array{reason: string, level: int, status: string}> $warnings */
        $warnings = [
            ['reason' => 'Terlambat mengirim laporan inspeksi tiga hari kerja berturut-turut.', 'level' => 1, 'status' => 'selesai'],
            ['reason' => 'Foto selfie inspeksi tidak sesuai dengan lokasi dapur yang dilaporkan.', 'level' => 2, 'status' => 'aktif'],
            ['reason' => 'Gagal melaporkan temuan kritis pada area pengolahan makanan.', 'level' => 3, 'status' => 'dicabut'],
        ];

        foreach ($warnings as $index => $warning) {
            SpWarning::query()->create(array_merge($warning, [
                'user_id' => $pengawas[$index % $pengawas->count()]->id,
                'admin_id' => $admin->id,
            ]));
        }
    }
}
