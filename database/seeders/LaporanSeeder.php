<?php

namespace Database\Seeders;

use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    /**
     * @return array<int, string>
     */
    private function catatan(): array
    {
        return [
            'Area masak bersih dan sanitasi sesuai standar.',
            'Ditemukan tumpahan minyak di dekat kompor, sudah dibersihkan saat inspeksi.',
            'Bahan mentah dan matang sudah disimpan terpisah pada wadah berlabel.',
            'Beberapa wadah bahan belum diberi tanggal kadaluarsa.',
            'Proses pengolahan makanan berjalan sesuai SOP dan suhu terjaga.',
            'Distribusi makanan tertunda karena kendala kendaraan operasional.',
            'Peralatan masak perlu diganti karena kondisinya sudah aus.',
        ];
    }

    /**
     * @return array<int, array{lat: float, lng: float}>
     */
    private function koordinat(): array
    {
        return [
            ['lat' => -6.1352, 'lng' => 106.1568],
            ['lat' => -6.1600, 'lng' => 106.2800],
            ['lat' => -6.0190, 'lng' => 106.0540],
            ['lat' => -6.1780, 'lng' => 106.6300],
            ['lat' => -6.3020, 'lng' => 106.7140],
            ['lat' => -6.3400, 'lng' => 105.8100],
            ['lat' => -6.3600, 'lng' => 106.2500],
            ['lat' => -6.2300, 'lng' => 106.4900],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Laporan::query()->exists()) {
            return;
        }

        $dapurs = Dapur::query()->orderBy('nama')->get();
        $pengawas = User::query()->where('role', 'pengawas')->orderBy('nama')->get();

        if ($dapurs->isEmpty() || $pengawas->isEmpty()) {
            return;
        }

        $statuses = ['selesai', 'diperiksa', 'temuan', 'tindak lanjut'];
        $catatan = $this->catatan();
        $koordinat = $this->koordinat();

        for ($index = 0; $index < 12; $index++) {
            $dapur = $dapurs[$index % $dapurs->count()];
            $creator = $pengawas[$index % $pengawas->count()];
            $point = $koordinat[$index % count($koordinat)];
            $createdAt = now()->subDays($index * 3);

            Laporan::query()->create([
                'dapur_id' => $dapur->id,
                'status' => $statuses[$index % count($statuses)],
                'catatan' => $catatan[$index % count($catatan)],
                'selfie_photo' => null,
                'latitude' => $point['lat'],
                'longitude' => $point['lng'],
                'address' => $dapur->alamat,
                'created_by' => $creator->id,
                'updated_by' => $creator->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
