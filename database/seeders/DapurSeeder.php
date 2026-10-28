<?php

namespace Database\Seeders;

use App\Models\Dapur;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Controllers\DapurController;

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
                'alamat' => 'Jl. Raya Serang No. 1, Kecamatan Serang, Kota Serang, Banten',
                'kecamatan' => 'Serang',
                'kabupaten_kota' => 'Kota Serang',
                'provinsi' => DapurController::BANTEN_PROVINSI,
            ],
            [
                'nama' => 'Dapur Genta Cilegon',
                'alamat' => 'Jl. Merak No. 45, Kecamatan Citangkil, Kota Cilegon, Banten',
                'kecamatan' => 'Citangkil',
                'kabupaten_kota' => 'Kota Cilegon',
                'provinsi' => DapurController::BANTEN_PROVINSI,
            ],
            [
                'nama' => 'Dapur Genta Tangerang',
                'alamat' => 'Jl. Jenderal Sudirman No. 78, Kecamatan Tangerang, Kota Tangerang, Banten',
                'kecamatan' => 'Tangerang',
                'kabupaten_kota' => 'Kota Tangerang',
                'provinsi' => DapurController::BANTEN_PROVINSI,
            ],
            [
                'nama' => 'Dapur Genta Pandeglang',
                'alamat' => 'Jl. Raya Labuan No. 12, Kecamatan Labuan, Kabupaten Pandeglang, Banten',
                'kecamatan' => 'Labuan',
                'kabupaten_kota' => 'Kabupaten Pandeglang',
                'provinsi' => DapurController::BANTEN_PROVINSI,
            ],
            [
                'nama' => 'Dapur Genta Lebak',
                'alamat' => 'Jl. Raya Rangkasbitung No. 56, Kecamatan Rangkasbitung, Kabupaten Lebak, Banten',
                'kecamatan' => 'Rangkasbitung',
                'kabupaten_kota' => 'Kabupaten Lebak',
                'provinsi' => DapurController::BANTEN_PROVINSI,
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
            ['Nama Dapur', 'Kabupaten/Kota', 'Kecamatan', 'Kepala Dapur', 'Pengawas'],
            Dapur::with('kepalaDapur', 'pengawas')
                ->get()
                ->map(fn ($d) => [
                    $d->nama,
                    $d->kabupaten_kota,
                    $d->kecamatan,
                    $d->kepalaDapur?->name ?? 'Belum diassign',
                    $d->pengawas->pluck('name')->join(', ') ?: 'Belum diassign',
                ])
                ->toArray()
        );
    }
}