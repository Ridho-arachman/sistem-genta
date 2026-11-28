<?php

namespace Database\Factories;

use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Laporan>
 */
class LaporanFactory extends Factory
{
    protected $model = Laporan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = fake()->randomElement([
            ['lat' => -6.1352, 'lng' => 106.1568],
            ['lat' => -6.3020, 'lng' => 106.7140],
            ['lat' => -6.0190, 'lng' => 106.0540],
        ]);

        return [
            'dapur_id' => Dapur::factory(),
            'status' => 'selesai',
            'catatan' => fake()->randomElement([
                'Area masak bersih dan sanitasi sesuai standar.',
                'Ditemukan tumpahan minyak di dekat kompor, sudah dibersihkan saat inspeksi.',
                'Beberapa wadah bahan belum diberi tanggal kadaluarsa.',
                'Peralatan masak perlu diganti karena kondisinya sudah aus.',
            ]),
            'selfie_photo' => null,
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'address' => fake('id_ID')->address(),
            'created_by' => User::factory(),
        ];
    }
}
