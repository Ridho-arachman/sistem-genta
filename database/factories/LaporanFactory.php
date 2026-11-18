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
        return [
            'dapur_id' => Dapur::factory(),
            'status' => 'selesai',
            'catatan' => fake()->sentence(),
            'selfie_photo' => 'laporans/1/photos/photo.jpg',
            'latitude' => -6.208800,
            'longitude' => 106.845600,
            'address' => fake()->address(),
            'created_by' => User::factory(),
        ];
    }
}
