<?php

namespace Database\Factories;

use App\Models\Dapur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dapur>
 */
class DapurFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kabupatenKota = fake()->randomElement(config('banten.kabupaten_kota'));
        $kecamatan = fake()->randomElement(config('banten.kecamatan')[$kabupatenKota]);

        return [
            'nama' => fake('id_ID')->company().' '.fake()->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => $kabupatenKota.' · Kec. '.$kecamatan,
            'alamat' => fake('id_ID')->address(),
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupatenKota,
            'provinsi' => config('banten.provinsi'),
        ];
    }
}
