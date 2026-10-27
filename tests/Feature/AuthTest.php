<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('login fails with wrong password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});

test('guest cannot access dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('admin can access admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password123')]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('pengawas can access pengawas dashboard', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'password' => bcrypt('password123')]);

    $this->actingAs($pengawas)
        ->get(route('pengawas.dashboard'))
        ->assertOk();
});

test('manajerial can access manajerial dashboard', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial', 'password' => bcrypt('password123')]);

    $this->actingAs($manajerial)
        ->get(route('manajerial.dashboard'))
        ->assertOk();
});

test('kepala_dapur can access kepala-dapur dashboard', function () {
    $kepalaDapur = User::factory()->create(['role' => 'kepala_dapur', 'password' => bcrypt('password123')]);

    $this->actingAs($kepalaDapur)
        ->get(route('kepala-dapur.dashboard'))
        ->assertOk();
});

test('role middleware blocks wrong dashboard', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'password' => bcrypt('password123')]);

    $this->actingAs($pengawas)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
