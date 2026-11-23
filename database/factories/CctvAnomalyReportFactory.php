<?php

namespace Database\Factories;

use App\Models\Cctv;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CctvAnomalyReport>
 */
class CctvAnomalyReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cctv_id' => Cctv::factory(),
            'admin_id' => User::factory(),
            'description' => fake('id_ID')->sentence(),
            'severity' => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'status' => 'baru',
        ];
    }
}
