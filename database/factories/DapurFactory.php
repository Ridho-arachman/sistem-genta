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
        return [
            'nama' => fake('id_ID')->company().' '.fake('id_ID')->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => fake('id_ID')->city(),
            'alamat' => fake('id_ID')->address(),
            'latitude' => fake('id_ID')->latitude(-6.5, -6.0),
            'longitude' => fake('id_ID')->longitude(106.5, 107.0),
            'geofence_radius' => fake('id_ID')->randomElement([50, 100, 150, 200]),
        ];
    }
}
