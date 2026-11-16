<?php

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

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
        ->assertSee('Daftar Monitor Kamera');
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

test('pengawas cannot create cctv via web (mobile only)', function () {
    $this->actingAs($this->pengawas)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Belakang',
            'status' => 'offline',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
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

test('monitoring page redirects to cctv index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.monitoring'))
        ->assertRedirect(route('admin.cctvs.index'));
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

test('stream key dibuat otomatis dan unik', function () {
    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id]);

    expect($cctv->stream_key)->not->toBeNull()
        ->and($cctv->stream_key)->not->toBe($cctv->id);
});

test('stream url diturunkan dari base media server', function () {
    config(['cctv.hls_base' => 'http://mediamtx.local:8888/']);

    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
        'hls_url' => 'https://example.com/stream.m3u8',
    ]);

    expect($cctv->stream_url)->toBe('http://mediamtx.local:8888/'.$cctv->stream_key.'/index.m3u8');
});

test('stream url memakai hls_url manual saat media server belum dikonfigurasi', function () {
    config(['cctv.hls_base' => null]);

    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
        'hls_url' => 'https://example.com/stream.m3u8',
    ]);

    expect($cctv->stream_url)->toBe('https://example.com/stream.m3u8');
});

test('menambah cctv mendaftarkan path ke media server', function () {
    config(['cctv.mediamtx.api' => 'http://mediamtx.test:9997']);
    Http::fake(['mediamtx.test:9997/*' => Http::response([], 200)]);

    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Area Masak',
            'rtsp_url' => 'rtsp://admin:pass@192.168.0.1:554/Streaming/Channels/101',
            'status' => 'online',
        ])
        ->assertRedirect(route('admin.cctvs.index'));

    Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/config/paths/replace/')
        && $request['source'] === 'rtsp://admin:pass@192.168.0.1:554/Streaming/Channels/101');
});

test('rtsp url harus dimulai dengan rtsp scheme', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Salah',
            'rtsp_url' => 'http://192.168.0.1/stream',
            'status' => 'online',
        ])
        ->assertSessionHasErrors('rtsp_url');
});
