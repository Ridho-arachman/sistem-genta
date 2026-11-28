<?php

namespace Database\Seeders;

use App\Models\Cctv;
use App\Models\Dapur;
use Illuminate\Database\Seeder;

class CctvSeeder extends Seeder
{
    /**
     * @return array<int, array{nama: string, section: string, status: string}>
     */
    private function cameras(int $dapurIndex): array
    {
        return [
            ['nama' => 'Area Masak', 'section' => 'Dapur', 'status' => 'online'],
            ['nama' => 'Gudang Bahan', 'section' => 'Gudang', 'status' => 'online'],
            ['nama' => 'Pintu Masuk', 'section' => 'Pintu Masuk', 'status' => $dapurIndex % 4 === 0 ? 'maintenance' : 'offline'],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Cctv::query()->exists()) {
            return;
        }

        foreach (Dapur::query()->orderBy('nama')->get()->values() as $dapurIndex => $dapur) {
            foreach ($this->cameras($dapurIndex) as $cameraNumber => $camera) {
                Cctv::query()->create([
                    'dapur_id' => $dapur->id,
                    'nama' => $camera['nama'].' Cam '.($cameraNumber + 1),
                    'rtsp_url' => sprintf(
                        'rtsp://admin:genta123@192.168.%d.%d:554/Streaming/Channels/101',
                        10 + $dapurIndex,
                        20 + $cameraNumber,
                    ),
                    'status' => $camera['status'],
                    'section' => $camera['section'],
                    'camera_number' => $cameraNumber + 1,
                    'last_checked_at' => $camera['status'] === 'online'
                        ? now()->subMinutes(5 + $cameraNumber)
                        : null,
                ]);
            }
        }
    }
}
