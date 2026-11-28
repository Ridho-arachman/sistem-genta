<?php

namespace Database\Factories;

use App\Models\Laporan;
use App\Models\LaporanHasil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaporanHasil>
 */
class LaporanHasilFactory extends Factory
{
    protected $model = LaporanHasil::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'laporan_id' => Laporan::factory(),
            'indikator' => 'Kebersihan dan sanitasi',
            'hasil' => 'diperiksa',
        ];
    }
}
