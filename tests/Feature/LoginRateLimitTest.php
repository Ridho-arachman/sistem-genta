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

test('forgot-password dibatasi lima percobaan per menit per IP', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);
    }

    $this->post(route('password.email'), [
        'email' => 'test@example.com',
    ])->assertStatus(429);
});

test('reset-password dibatasi lima percobaan per menit per IP', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('password.update'), [
            'token' => 'salah',
            'email' => 'test@example.com',
            'password' => 'baru123',
            'password_confirmation' => 'baru123',
        ]);
    }

    $this->post(route('password.update'), [
        'token' => 'salah',
        'email' => 'test@example.com',
        'password' => 'baru123',
        'password_confirmation' => 'baru123',
    ])->assertStatus(429);
});

test('ganti password dibatasi lima percobaan per menit per user', function () {
    $pengawas = User::factory()->pengawas()->create([
        'password' => bcrypt('password123'),
    ]);

    $this->actingAs($pengawas)->withCookie('capacitor_app', '1');

    for ($i = 0; $i < 5; $i++) {
        $this->put(route('pengawas.settings.profile.update'), [
            'current_password' => 'salah',
            'password' => 'baru123',
            'password_confirmation' => 'baru123',
        ]);
    }

    $this->put(route('pengawas.settings.profile.update'), [
        'current_password' => 'salah',
        'password' => 'baru123',
        'password_confirmation' => 'baru123',
    ])->assertStatus(429);
});
