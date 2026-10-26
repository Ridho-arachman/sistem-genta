<?php

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
    $this->dapur = Dapur::factory()->create();
});

test('authenticated user can view cctv index', function () {
    Cctv::factory()->count(3)->create(['dapur_id' => $this->dapur->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.index'))
        ->assertOk()
        ->assertSee('Daftar CCTV');
});

test('admin can create cctv', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Depan',
            'ip_address' => '192.168.1.100',
            'rtsp_url' => 'rtsp://admin:pass@192.168.1.100:554/stream',
            'hls_url' => 'https://example.com/stream.m3u8',
            'status' => 'online',
        ])
        ->assertRedirect(route('admin.cctvs.index'))
        ->assertSessionHas('status', 'CCTV berhasil ditambahkan.');

    $this->assertDatabaseHas('cctvs', [
        'dapur_id' => $this->dapur->id,
        'nama' => 'Kamera Depan',
        'status' => 'online',
    ]);
});

test('pengawas cannot create cctv', function () {
    $this->actingAs($this->pengawas)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Belakang',
            'status' => 'offline',
        ])
        ->assertForbidden();
});

test('user can update cctv', function () {
    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id, 'status' => 'offline']);

    $this->actingAs($this->admin)
        ->put(route('admin.cctvs.update', $cctv), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Utama Updated',
            'ip_address' => '192.168.1.101',
            'rtsp_url' => 'rtsp://admin:pass@192.168.1.101:554/stream',
            'hls_url' => 'https://example.com/new-stream.m3u8',
            'status' => 'online',
        ])
        ->assertRedirect(route('admin.cctvs.index'))
        ->assertSessionHas('status', 'CCTV berhasil diperbarui.');

    $this->assertDatabaseHas('cctvs', [
        'id' => $cctv->id,
        'nama' => 'Kamera Utama Updated',
        'status' => 'online',
    ]);
});

test('user can delete cctv', function () {
    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.cctvs.destroy', $cctv))
        ->assertRedirect(route('admin.cctvs.index'))
        ->assertSessionHas('status', 'CCTV berhasil dihapus.');

    $this->assertDatabaseMissing('cctvs', ['id' => $cctv->id]);
});

test('cctv show page loads', function () {
    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
        'rtsp_url' => 'rtsp://example.com/stream',
        'hls_url' => 'https://example.com/stream.m3u8',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.show', $cctv))
        ->assertOk()
        ->assertSee($cctv->nama)
        ->assertSee($cctv->rtsp_url)
        ->assertSee($cctv->hls_url);
});

test('monitoring page loads for dapur', function () {
    $cctv1 = Cctv::factory()->create(['dapur_id' => $this->dapur->id, 'status' => 'online']);
    $cctv2 = Cctv::factory()->create(['dapur_id' => $this->dapur->id, 'status' => 'offline']);

    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.monitoring', $this->dapur))
        ->assertOk()
        ->assertSee($this->dapur->nama)
        ->assertSee($cctv1->nama)
        ->assertSee($cctv2->nama);
});

test('cctv status enum validation', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Test Camera',
            'status' => 'invalid_status',
        ])
        ->assertSessionHasErrors('status');
});

test('cctv requires dapur_id', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'nama' => 'Test Camera',
        ])
        ->assertSessionHasErrors('dapur_id');
});
