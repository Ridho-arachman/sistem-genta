<?php

namespace Database\Seeders;

use App\Models\Dapur;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DapurSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user yang sudah ada dari RoleSeeder
        $kepalaDapurUser = User::where('email', 'kepaladapur@test.com')->first();
        $pengawasUser = User::where('email', 'pengawas@test.com')->first();
        $adminUser = User::where('email', 'admin@test.com')->first();

        $dapurs = [
            [
                'nama' => 'Dapur Genta Pusat Serang',
                'lokasi' => 'Serang',
                'alamat' => 'Jl. Raya Serang No. 1, Kecamatan Serang, Kota Serang, Banten',
                'latitude' => -6.1148,
                'longitude' => 106.1503,
                'geofence_radius' => 150,
            ],
            [
                'nama' => 'Dapur Genta Cilegon',
                'lokasi' => 'Cilegon',
                'alamat' => 'Jl. Merak No. 45, Kecamatan Citangkil, Kota Cilegon, Banten',
                'latitude' => -5.9897,
                'longitude' => 106.0105,
                'geofence_radius' => 100,
            ],
            [
                'nama' => 'Dapur Genta Tangerang',
                'lokasi' => 'Tangerang',
                'alamat' => 'Jl. Jenderal Sudirman No. 78, Kecamatan Tangerang, Kota Tangerang, Banten',
                'latitude' => -6.1783,
                'longitude' => 106.6319,
                'geofence_radius' => 200,
            ],
            [
                'nama' => 'Dapur Genta Pandeglang',
                'lokasi' => 'Pandeglang',
                'alamat' => 'Jl. Raya Labuan No. 12, Kecamatan Labuan, Kabupaten Pandeglang, Banten',
                'latitude' => -6.3502,
                'longitude' => 105.9321,
                'geofence_radius' => 100,
            ],
            [
                'nama' => 'Dapur Genta Lebak',
                'lokasi' => 'Lebak',
                'alamat' => 'Jl. Raya Rangkasbitung No. 56, Kecamatan Rangkasbitung, Kabupaten Lebak, Banten',
                'latitude' => -6.3667,
                'longitude' => 106.2500,
                'geofence_radius' => 150,
            ],
        ];

        foreach ($dapurs as $index => $dapurData) {
            $dapur = Dapur::create($dapurData);

            // Assign Kepala Dapur ke dapur pertama (jika user ada)
            // Kepala Dapur menggunakan dapur_id pada tabel users (HasOne/HasMany)
            if ($index === 0 && $kepalaDapurUser) {
                $kepalaDapurUser->update(['dapur_id' => $dapur->id]);
            }

            // Assign Pengawas ke semua dapur (jika user ada)
            // Pengawas menggunakan pivot table dapur_user (BelongsToMany)
            if ($pengawasUser) {
                $dapur->pengawas()->attach($pengawasUser->id);
            }
        }

        // Buat dapur tambahan via factory (5 dapur acak)
        Dapur::factory()->count(5)->create();

        $this->command->info('Dapur seeded successfully!');
        $this->command->table(
            ['Nama Dapur', 'Lokasi', 'Kepala Dapur', 'Pengawas'],
            Dapur::with('kepalaDapur', 'pengawas')
                ->get()
                ->map(fn ($d) => [
                    $d->nama,
                    $d->lokasi,
                    $d->kepalaDapur?->name ?? 'Belum diassign',
                    $d->pengawas->pluck('name')->join(', ') ?: 'Belum diassign',
                ])
                ->toArray()
        );
    }
}