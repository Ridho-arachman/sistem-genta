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
        // Banten kecamatan list per kabupaten/kota
        $bantenKecamatan = [
            'Kota Serang' => ['Curug', 'Kasemen', 'Walantaka', 'Taktakan', 'Serang', 'Cikeusal', 'Kramatwatu', 'Pabuaran', 'Cinangka', 'Carenang'],
            'Kota Cilegon' => ['Cilegon', 'Pulo Ampel', 'Jombang', 'Citangkil', 'Grogol', 'Ciwandan', 'Gerogol'],
            'Kota Tangerang' => ['Tangerang', 'Batuceper', 'Benda', 'Jatiuwung', 'Neglasari', 'Cipondoh', 'Karawaci', 'Larangan', 'Ciledug', 'Pinang', 'Binuang', 'Pakuhaji', 'Sepatan', 'Sukadiri', 'Mekar Baru'],
            'Kota Tangerang Selatan' => ['Serpong', 'Serpong Utara', 'Pamulang', 'Ciputat', 'Ciputat Timur', 'Setu', 'Pondok Aren'],
            'Kabupaten Pandeglang' => ['Pandeglang', 'Munjul', 'Cibaliung', 'Cimanggu', 'Angsana', 'Karangsak', 'Sobang', 'Cibitung', 'Mandalawangi', 'Cikeudel', 'Panimbang', 'Sumur', 'Ujung Jaya', 'Pulosari', 'Cigeulis', 'Pagelaran', 'Majasari', 'Kaduhejo'],
            'Kabupaten Lebak' => ['Rangkasbitung', 'Cibadak', 'Muncang', 'Sobang', 'Cihara', 'Malenggang', 'Bayah', 'Cilangkap', 'Cileles', 'Cijaku', 'Cigemblong', 'Wanasalam', 'Gunung Kencana', 'Mekar Jaya', 'Panggarangan', 'Sajira', 'Cibengkung', 'Leuwidamar', 'Bojongmanik', 'Sukaresmi', 'Kencana', 'Gunung Kencana', 'Cibadak', 'Warunggunung'],
            'Kabupaten Tangerang' => ['Tigaraksa', 'Kosambi', 'Sepatan', 'Rajeg', 'Cikupa', 'Kelapa Dua', 'Curug', 'Solear', 'Kresek', 'Kaduhejo', 'Pagedangan', 'Cadasari', 'Jayanti', 'Balaraja', 'Bojongmanik', 'Kronjo', 'Mauk', 'Sukamulya', 'Gunung Kaler', 'Jasinga', 'Caringin', 'Cigemblong', 'Sindang Jaya', 'Jambe', 'Kramatwatu', 'Carenang', 'Sindang', 'Pasar Kemis'],
        ];

        $kabupatenKota = fake('id_ID')->randomElement(array_keys($bantenKecamatan));
        $kecamatan = fake('id_ID')->randomElement($bantenKecamatan[$kabupatenKota]);

        return [
            'nama' => fake('id_ID')->company().' '.fake('id_ID')->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => $kabupatenKota . ' · Kec. ' . $kecamatan,
            'alamat' => fake('id_ID')->address(),
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupatenKota,
            'provinsi' => 'Banten',
        ];
    }
}