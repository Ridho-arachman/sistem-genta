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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Indonesian provinces and cities for realistic data
        $bantenCities = [
            ['kota' => 'Kota Serang', 'kecamatan' => ['Curug', 'Kasemen', 'Walantaka', 'Taktakan', 'Serang']],
            ['kota' => 'Kota Cilegon', 'kecamatan' => ['Cilegon', 'Pulo Ampel', 'Jombang', 'Citangkil', 'Grogol']],
            ['kota' => 'Kota Tangerang', 'kecamatan' => ['Tangerang', 'Batuceper', 'Benda', 'Jatiuwung', 'Neglasari']],
            ['kota' => 'Kota Tangerang Selatan', 'kecamatan' => ['Serpong', 'Serpong Utara', 'Pamulang', 'Ciputat', 'Setu']],
            ['kabupaten' => 'Kabupaten Pandeglang', 'kecamatan' => ['Pandeglang', 'Munjul', 'Cibaliung', 'Cimanggu', 'Angsana']],
            ['kabupaten' => 'Kabupaten Lebak', 'kecamatan' => ['Rangkasbitung', 'Cibadak', 'Muncang', 'Sobang', 'Cihara']],
            ['kabupaten' => 'Kabupaten Tangerang', 'kecamatan' => ['Tigaraksa', 'Kosambi', 'Sepatan', 'Rajeg', 'Cikupa']],
        ];
        
        $city = fake('id_ID')->randomElement($bantenCities);
        $kabupatenKota = $city['kota'] ?? $city['kabupaten'];
        $kecamatan = fake('id_ID')->randomElement($city['kecamatan']);
        
        return [
            'nama' => fake('id_ID')->company().' '.fake('id_ID')->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => $kabupatenKota . ' · Kec. ' . $kecamatan,
            'alamat' => fake('id_ID')->address(),
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupatenKota,
            'provinsi' => 'Banten',
            'latitude' => fake('id_ID')->latitude(-6.5, -6.0),
            'longitude' => fake('id_ID')->longitude(106.5, 107.0),
            'geofence_radius' => fake('id_ID')->randomElement([50, 100, 150, 200]),
        ];
    }
}