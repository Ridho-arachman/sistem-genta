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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sections = ['Lobby', 'Dapur', 'Gudang', 'Parkir', 'Pintu Masuk', 'Area Masak', 'Ruang Makan', 'Kantin'];

        return [
            'dapur_id' => Dapur::factory(),
            'nama' => fake()->randomElement($sections).' '.fake()->randomElement(['Kiri', 'Kanan', 'Tengah']),
            'rtsp_url' => 'rtsp://admin:genta123@192.168.1.'.fake()->numberBetween(10, 250).':554/Streaming/Channels/101',
            'status' => 'online',
            'section' => fake()->randomElement($sections),
            'camera_number' => fake()->numberBetween(1, 5),
        ];
    }
}
