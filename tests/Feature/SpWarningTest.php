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

test('admin tidak bisa terbitkan SP ganda pengawas+level+alasan sama hari itu (idempoten)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pengawas = User::factory()->create(['role' => 'pengawas']);

    $payload = [
        'user_id' => $pengawas->id,
        'level' => 1,
        'reason' => 'Terlambat datang',
    ];

    $this->actingAs($admin)
        ->post(route('admin.sp-warnings.store'), $payload)
        ->assertRedirect(route('admin.sp-warnings.index'))
        ->assertSessionHas('status', 'SP berhasil diterbitkan.');

    // Double submit dengan data yang sama harus tidak membuat duplikat.
    $this->actingAs($admin)
        ->post(route('admin.sp-warnings.store'), $payload)
        ->assertRedirect(route('admin.sp-warnings.index'))
        ->assertSessionHas('status', 'SP untuk pengawas, level, dan alasan ini sudah terbit hari ini.');

    expect(SpWarning::where('user_id', $pengawas->id)
        ->where('admin_id', $admin->id)
        ->where('level', 1)
        ->where('reason', 'Terlambat datang')
        ->where('status', 'aktif')
        ->whereDate('created_at', today())
        ->count())->toBe(1);
});
