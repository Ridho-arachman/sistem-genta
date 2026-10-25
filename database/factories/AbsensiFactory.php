<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'dapur_id' => Dapur::factory(),
            'check_in_at' => fake()->dateTimeBetween('-2 hours', 'now'),
            'check_out_at' => fake()->optional(0.7)->dateTimeBetween('now', '+2 hours'),
            'lat' => fake()->latitude(-6.5, -6.0),
            'lng' => fake()->longitude(106.5, 107.0),
            'selfie_path' => fake()->optional(0.5)->imageUrl(640, 480, 'people'),
            'status' => fake()->randomElement(['hadir', 'izin', 'cuti', 'tidak_hadir']),
        ];
    }
}
