<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login dibatasi lima percobaan per menit', function () {
    User::factory()->admin()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Kredensial salah: penyerang tidak punya sesi, jadi hitungan per IP.
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'salah',
        ]);
    }

    $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'salah',
    ])->assertStatus(429);
});
