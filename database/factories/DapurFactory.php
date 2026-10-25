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
            'nama' => fake()->company().' '.fake()->randomElement(['Pusat', 'Utama', 'Cabang', 'Satuan']),
            'lokasi' => fake()->city(),
            'alamat' => fake()->address(),
            'latitude' => fake()->latitude(-6.5, -6.0),
            'longitude' => fake()->longitude(106.5, 107.0),
            'geofence_radius' => fake()->randomElement([50, 100, 150, 200]),
        ];
    }
}
