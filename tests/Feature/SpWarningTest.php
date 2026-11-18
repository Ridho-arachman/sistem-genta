<?php

use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createSpWarning(): SpWarning
{
    $pengawas = User::factory()->create(['role' => 'pengawas']);
    $admin = User::factory()->create(['role' => 'admin']);

    return SpWarning::create([
        'user_id' => $pengawas->id,
        'admin_id' => $admin->id,
        'level' => 1,
        'reason' => 'Terlambat datang',
        'status' => 'aktif',
    ]);
}

test('manajerial dapat melihat daftar SP', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial']);
    createSpWarning();

    $this->actingAs($manajerial)
        ->get(route('manajerial.sp-warnings.index'))
        ->assertOk()
        ->assertSee('Terlambat datang');
});

test('manajerial dapat melihat detail SP', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial']);
    $warning = createSpWarning();

    $this->actingAs($manajerial)
        ->get(route('manajerial.sp-warnings.show', $warning))
        ->assertOk()
        ->assertSee('Terlambat datang');
});

test('manajerial tidak melihat tombol kelola SP', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial']);
    createSpWarning();

    $this->actingAs($manajerial)
        ->get(route('manajerial.sp-warnings.index'))
        ->assertOk()
        ->assertDontSee('Buat SP Baru');
});

test('admin melihat tombol kelola SP', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    createSpWarning();

    $this->actingAs($admin)
        ->get(route('admin.sp-warnings.index'))
        ->assertOk()
        ->assertSee('Buat SP Baru');
});

test('kepala_dapur tidak boleh akses halaman SP manajerial', function () {
    $kepalaDapur = User::factory()->create(['role' => 'kepala_dapur']);

    $this->actingAs($kepalaDapur)
        ->get(route('manajerial.sp-warnings.index'))
        ->assertForbidden();
});
