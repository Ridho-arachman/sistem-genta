<?php

namespace Database\Factories;

use App\Models\Cctv;
use App\Models\Dapur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cctv>
 */
class CctvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dapur_id' => Dapur::factory(),
            'nama' => 'Kamera '.fake()->randomElement(['Depan', 'Belakang', 'Samping', 'Atap', 'Gudang', 'Parkir']),
            'ip_address' => fake()->ipv4(),
            'rtsp_url' => 'rtsp://'.fake()->userName().':'.fake()->password().'@'.fake()->ipv4().':554/stream',
            'hls_url' => fake()->optional()->url().'/stream.m3u8',
            'status' => fake()->randomElement(['online', 'offline', 'maintenance']),
        ];
    }
}
