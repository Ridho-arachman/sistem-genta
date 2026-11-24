<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// Pengawas hanya boleh lewat webview mobile (User-Agent GentaCapacitor),
// seperti aturan RestrictPengawasFromWeb.

test('pengawas dapat ganti password', function () {
    $user = User::factory()->create([
        'role' => 'pengawas',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->withHeaders(['User-Agent' => 'GentaCapacitor'])
        ->actingAs($user)
        ->put(route('pengawas.settings.profile.update'), [
            'current_password' => 'password123',
            'password' => 'password456',
            'password_confirmation' => 'password456',
        ]);

    $response->assertRedirect();
    expect(Hash::check('password456', $user->fresh()->password))->toBeTrue();
});

test('password lebih pendek dari aturan Password::defaults() ditolak', function () {
    $user = User::factory()->create([
        'role' => 'pengawas',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->withHeaders(['User-Agent' => 'GentaCapacitor'])
        ->actingAs($user)
        ->put(route('pengawas.settings.profile.update'), [
            'current_password' => 'password123',
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ]);

    $response->assertSessionHasErrors('password');
    expect(Hash::check('password123', $user->fresh()->password))->toBeTrue();
});

test('pengawas gagal ganti password kalau current_password salah', function () {
    $user = User::factory()->create([
        'role' => 'pengawas',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->withHeaders(['User-Agent' => 'GentaCapacitor'])
        ->actingAs($user)
        ->put(route('pengawas.settings.profile.update'), [
            'current_password' => 'salah',
            'password' => 'password456',
            'password_confirmation' => 'password456',
        ]);

    $response->assertSessionHasErrors('current_password');
    expect(Hash::check('password123', $user->fresh()->password))->toBeTrue();
});
