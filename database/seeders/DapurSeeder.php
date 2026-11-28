<?php

namespace Database\Seeders;

use App\Models\Dapur;
use Illuminate\Database\Seeder;

class DapurSeeder extends Seeder
{
    /**
     * @return array<int, array<string, string>>
     */
    private function dapurs(): array
    {
        return [
            [
                'nama' => 'Dapur Umum Kota Serang',
                'alamat' => 'Jl. Raya Kasemen No. 12, Kasemen, Kota Serang',
                'kecamatan' => 'Kasemen',
                'kabupaten_kota' => 'Kota Serang',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.1352,106.1568&output=embed',
            ],
            [
                'nama' => 'Dapur Pangan Cikande',
                'alamat' => 'Jl. Raya Cikande No. 45, Cikande, Kabupaten Serang',
                'kecamatan' => 'Cikande',
                'kabupaten_kota' => 'Kabupaten Serang',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.1600,106.2800&output=embed',
            ],
            [
                'nama' => 'Dapur Bersama Cilegon',
                'alamat' => 'Jl. Raya Cibeber No. 8, Cibeber, Kota Cilegon',
                'kecamatan' => 'Cibeber',
                'kabupaten_kota' => 'Kota Cilegon',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.0190,106.0540&output=embed',
            ],
            [
                'nama' => 'Dapur Umum Tangerang Kota',
                'alamat' => 'Jl. Raya Karang Tengah No. 21, Karang Tengah, Kota Tangerang',
                'kecamatan' => 'Karang Tengah',
                'kabupaten_kota' => 'Kota Tangerang',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.1780,106.6300&output=embed',
            ],
            [
                'nama' => 'Dapur Pangan Serpong',
                'alamat' => 'Jl. Raya Serpong No. 33, Serpong, Kota Tangerang Selatan',
                'kecamatan' => 'Serpong',
                'kabupaten_kota' => 'Kota Tangerang Selatan',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.3020,106.7140&output=embed',
            ],
            [
                'nama' => 'Dapur Umum Pandeglang',
                'alamat' => 'Jl. Raya Pandeglang No. 17, Pandeglang, Kabupaten Pandeglang',
                'kecamatan' => 'Pandeglang',
                'kabupaten_kota' => 'Kabupaten Pandeglang',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.3400,105.8100&output=embed',
            ],
            [
                'nama' => 'Dapur Bersama Rangkasbitung',
                'alamat' => 'Jl. Raya Rangkasbitung No. 56, Rangkasbitung, Kabupaten Lebak',
                'kecamatan' => 'Rangkasbitung',
                'kabupaten_kota' => 'Kabupaten Lebak',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.3600,106.2500&output=embed',
            ],
            [
                'nama' => 'Dapur Pangan Tigaraksa',
                'alamat' => 'Jl. Raya Tigaraksa No. 9, Tigaraksa, Kabupaten Tangerang',
                'kecamatan' => 'Tigaraksa',
                'kabupaten_kota' => 'Kabupaten Tangerang',
                'map_embed_url' => 'https://maps.google.com/maps?q=-6.2300,106.4900&output=embed',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Dapur::query()->exists()) {
            return;
        }

        foreach ($this->dapurs() as $dapur) {
            Dapur::query()->create(array_merge($dapur, [
                'provinsi' => config('banten.provinsi'),
            ]));
        }
    }
}
