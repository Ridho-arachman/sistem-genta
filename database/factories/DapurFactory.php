<?php

namespace Database\Factories;

use App\Models\Dapur;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Http\Controllers\DapurController;

/**
 * @extends Factory<Dapur>
 */
class DapurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kabupatenKota = fake('id_ID')->randomElement(DapurController::BANTEN_KABUPATEN_KOTA);
        $kecamatan = fake('id_ID')->randomElement((new DapurController())->getKnownKecamatan($kabupatenKota));

        return [
            'nama' => fake('id_ID')->company().' '.fake('id_ID')->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => $kabupatenKota . ' · Kec. ' . $kecamatan,
            'alamat' => fake('id_ID')->address(),
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupatenKota,
            'provinsi' => DapurController::BANTEN_PROVINSI,
            'latitude' => -6.2088 + (fake()->randomFloat(6, -0.5, 0.5)),
            'longitude' => 106.8456 + (fake()->randomFloat(6, -0.5, 0.5)),
            'geofence_radius' => fake()->randomElement([100, 150, 200, 500, 1000]),
        ];
    }
}