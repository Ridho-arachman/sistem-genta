<?php

namespace Database\Factories;

use App\Http\Controllers\DapurController;
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
        $kabupatenKota = fake('id_ID')->randomElement(DapurController::BANTEN_KABUPATEN_KOTA);
        $kecamatan = fake('id_ID')->randomElement((new DapurController)->getKnownKecamatan($kabupatenKota));

        return [
            'nama' => fake('id_ID')->company().' '.fake('id_ID')->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => $kabupatenKota.' · Kec. '.$kecamatan,
            'alamat' => fake('id_ID')->address(),
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupatenKota,
            'provinsi' => DapurController::BANTEN_PROVINSI,
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.12016280941!2d106.09306857440856!3d-6.114521559962048!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e418bb3a534efd3%3A0xe0bd65ad1c85f7c1!2sTaktakan%2C%20Kec.%20Taktakan%2C%20Kota%20Serang%2C%20Banten%2042162!5e0!3m2!1sid!2sid!4v1793725171124!5m2!1sid!2sid',
        ];
    }
}
