<?php

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
});

test('admin can view dapur index', function () {
    Dapur::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.index'))
        ->assertOk()
        ->assertSee('Daftar Dapur');
});

test('pengawas cannot view dapur index', function () {
    Dapur::factory()->count(3)->create();

    $this->actingAs($this->pengawas)
        ->get(route('admin.dapurs.index'))
        ->assertForbidden();
});

test('admin can create dapur', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Dapur Utama',
            'lokasi' => 'Jakarta Pusat',
            'alamat' => 'Jl. Merdeka No. 1',
        ])
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur berhasil ditambahkan.');

    $this->assertDatabaseHas('dapurs', [
        'nama' => 'Dapur Utama',
        'lokasi' => 'Jakarta Pusat',
    ]);
});

test('pengawas cannot create dapur', function () {
    $this->actingAs($this->pengawas)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Dapur Utama',
        ])
        ->assertForbidden();
});

test('admin can update dapur', function () {
    $dapur = Dapur::factory()->create(['nama' => 'Dapur Lama']);

    $this->actingAs($this->admin)
        ->put(route('admin.dapurs.update', $dapur), [
            'nama' => 'Dapur Baru',
            'lokasi' => 'Bandung',
            'alamat' => 'Jl. Asia Afrika',
        ])
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur berhasil diperbarui.');

    $this->assertDatabaseHas('dapurs', [
        'id' => $dapur->id,
        'nama' => 'Dapur Baru',
    ]);
});

test('pengawas cannot update dapur', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->put(route('admin.dapurs.update', $dapur), [
            'nama' => 'Dapur Baru',
        ])
        ->assertForbidden();
});

test('admin can delete dapur', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.dapurs.destroy', $dapur))
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur berhasil dihapus.');

    $this->assertDatabaseMissing('dapurs', ['id' => $dapur->id]);
});

test('pengawas cannot delete dapur', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->delete(route('admin.dapurs.destroy', $dapur))
        ->assertForbidden();
});

test('dapur show loads cctvs relationship', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.show', $dapur))
        ->assertOk()
        ->assertSee($cctv->nama);
});