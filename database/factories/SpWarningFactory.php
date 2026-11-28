<?php

namespace Database\Factories;

use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpWarning>
 */
class SpWarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'pengawas']),
            'admin_id' => User::factory()->state(['role' => 'admin']),
            'level' => fake()->randomElement([1, 2, 3]),
            'reason' => fake()->randomElement([
                'Terlambat mengirim laporan inspeksi tiga hari kerja berturut-turut.',
                'Foto selfie inspeksi tidak sesuai dengan lokasi dapur yang dilaporkan.',
                'Gagal melaporkan temuan kritis pada area pengolahan makanan.',
            ]),
            'status' => 'aktif',
        ];
    }
}
