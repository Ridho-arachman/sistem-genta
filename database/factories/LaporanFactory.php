<?php

namespace Database\Factories;

use App\Models\Dapur;
use App\Models\Laporan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class LaporanFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = Laporan::class;

    /** @var class-string<Model>|null */
    protected $modelFactory = DapurFactory::class;

    public function definition(): array
    {
        $statuses = ['diperiksa', 'temuan', 'tindak lanjut', 'selesai'];

        return [
            'dapur_id' => Dapur::first()->id ?? Dapur::factory(),
            'status' => fake()->randomElement($statuses),
            'catatan' => fake()->optional()->paragraph(),
            'selfie_photo' => fake()->optional()->imageUrl(640, 480),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['status' => 'selesai']);
    }

    public function temuan(): static
    {
        return $this->state(fn () => ['status' => 'temuan']);
    }
}
