<?php

namespace Tests\Concerns;

use App\Models\Dapur;
use App\Models\User;

trait CreatesTestUsers
{
    protected function createTestUsers(): void
    {
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->pengawas = User::factory()->create(['role' => 'pengawas']);
    }

    protected function createTestDapur(array $attributes = []): Dapur
    {
        return Dapur::factory()->create(array_merge([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'geofence_radius' => 100,
        ], $attributes));
    }
}
