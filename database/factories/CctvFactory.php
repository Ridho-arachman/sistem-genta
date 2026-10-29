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
    protected static int $cameraCounter = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$cameraCounter++;

        $sections = ['Lobby', 'Dapur', 'Gudang', 'Parkir', 'Pintu Masuk', 'Area Masak', 'Ruang Makan', 'Kantin'];
        $positions = ['Kiri', 'Kanan', 'Tengah', 'Atas', 'Bawah'];

        return [
            'dapur_id' => Dapur::factory(),
            'nama' => fake()->randomElement($sections).' '.fake()->randomElement($positions),
            'ip_address' => fake()->ipv4(),
            'rtsp_url' => 'rtsp://'.fake()->userName().':'.fake()->password().'@'.fake()->ipv4().':554/stream',
            'hls_url' => fake()->optional()->url().'/stream.m3u8',
            'status' => fake()->randomElement(['online', 'offline', 'maintenance']),
            'section' => fake()->randomElement($sections),
            'camera_number' => self::$cameraCounter,
        ];
    }

    /**
     * Set the dapur for this CCTV.
     */
    public function forDapur(Dapur $dapur): static
    {
        return $this->state(fn () => [
            'dapur_id' => $dapur->id,
        ]);
    }

    /**
     * Realistic distribution: 70% online, 20% offline, 10% maintenance.
     */
    public function realistic(): static
    {
        return $this->state(fn () => [
            'status' => fake()->randomElement(array_merge(
                array_fill(0, 7, 'online'),
                array_fill(0, 2, 'offline'),
                array_fill(0, 1, 'maintenance')
            )),
        ]);
    }
}
