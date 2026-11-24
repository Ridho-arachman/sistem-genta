<?php

use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createSpWarning(): SpWarning
{
    return SpWarning::factory()->create([
        'level' => 1,
        'reason' => 'Terlambat datang',
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

test('policy SP: hanya admin yang boleh terbitkan, ubah, dan hapus', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $manajerial = User::factory()->create(['role' => 'manajerial']);
    $warning = createSpWarning();

    // Kelola SP = admin saja; lihat = admin + manajerial.
    expect($admin->can('viewAny', SpWarning::class))->toBeTrue()
        ->and($admin->can('create', SpWarning::class))->toBeTrue()
        ->and($admin->can('update', $warning))->toBeTrue()
        ->and($admin->can('delete', $warning))->toBeTrue();

    expect($manajerial->can('viewAny', SpWarning::class))->toBeTrue()
        ->and($manajerial->can('view', $warning))->toBeTrue()
        ->and($manajerial->can('create', SpWarning::class))->toBeFalse()
        ->and($manajerial->can('update', $warning))->toBeFalse()
        ->and($manajerial->can('delete', $warning))->toBeFalse();
});
