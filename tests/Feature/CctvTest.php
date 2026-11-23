<?php

use App\Jobs\CctvHealthCheckJob;
use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use App\Services\MediaMtxClient;
use App\Services\RtspProbe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

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
    Queue::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.cctvs.store'), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Depan',
            'rtsp_url' => 'rtsp://admin:pass@192.168.1.100:554/stream',
            'status' => 'online',
        ])
        ->assertRedirect(route('admin.cctvs.index'))
        ->assertSessionHas('status', 'CCTV berhasil ditambahkan.');

    $this->assertDatabaseHas('cctvs', [
        'dapur_id' => $this->dapur->id,
        'nama' => 'Kamera Depan',
        'status' => 'online',
    ]);

    Queue::assertPushed(CctvHealthCheckJob::class, function ($job) {
        return $job->cctvId !== null;
    });
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
    Queue::fake();

    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id, 'status' => 'offline']);

    $this->actingAs($this->admin)
        ->put(route('admin.cctvs.update', $cctv), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Utama Updated',
            'rtsp_url' => 'rtsp://admin:pass@192.168.1.101:554/stream',
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

test('updating cctv schedules an immediate health check', function () {
    Queue::fake();

    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id, 'status' => 'offline']);

    $this->actingAs($this->admin)
        ->put(route('admin.cctvs.update', $cctv), [
            'dapur_id' => $this->dapur->id,
            'nama' => 'Kamera Utama Updated',
            'rtsp_url' => 'rtsp://admin:pass@192.168.1.101:554/stream',
            'status' => 'offline',
        ])
        ->assertRedirect(route('admin.cctvs.index'));

    // Badge tak perlu menunggu jadwal lima menit berikutnya.
    Queue::assertPushed(CctvHealthCheckJob::class, function ($job) use ($cctv) {
        return $job->dapurId === (string) $this->dapur->id
            && $job->cctvId === (string) $cctv->id;
    });
});

test('health check setelah simpan url baru menunggu playlist hls sebelum menandai online', function () {
    config([
        'cctv.mediamtx.api' => 'http://mediamtx.test:9997',
        'cctv.hls_base' => 'http://mediamtx.test:8888',
    ]);
    Http::fake([
        'mediamtx.test:9997/*' => Http::response([], 200),
        'mediamtx.test:8888/*' => Http::sequence()
            ->push('', 500)
            ->push('#EXTM3U', 200)
            ->push('#EXTM3U', 200),
    ]);

    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
        'rtsp_url' => 'rtsp://cam/live',
        'status' => 'offline',
    ]);

    (new CctvHealthCheckJob($cctv->dapur_id, $cctv->id))
        ->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($cctv->fresh()->status)->toBe('online')
        ->and($cctv->fresh()->last_checked_at)->not->toBeNull();
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
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.show', $cctv))
        ->assertOk()
        ->assertSee($cctv->nama)
        ->assertSee($cctv->rtsp_url);
});

test('token rtsp url dimask di halaman detail', function () {
    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
        'rtsp_url' => 'rtsp://example.com/stream?token=rahasia123',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.cctvs.show', $cctv))
        ->assertOk()
        ->assertSee('token=••••')
        ->assertDontSee('rahasia123');
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
    ]);

    expect($cctv->stream_url)->toBe('http://mediamtx.local:8888/'.$cctv->stream_key.'/index.m3u8');
});

test('stream url kosong saat media server belum dikonfigurasi', function () {
    config(['cctv.hls_base' => null]);

    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id]);

    expect($cctv->stream_url)->toBeNull();
});

test('url webrtc diturunkan dari base media server', function () {
    config(['cctv.webrtc_base' => 'http://mediamtx.local:8889']);

    $cctv = Cctv::factory()->create([
        'dapur_id' => $this->dapur->id,
    ]);

    expect($cctv->web_rtc_url)->toBe('http://mediamtx.local:8889/'.$cctv->stream_key.'/whep');
});

test('url webrtc kosong saat base webrtc belum dikonfigurasi', function () {
    config(['cctv.webrtc_base' => null]);

    $cctv = Cctv::factory()->create(['dapur_id' => $this->dapur->id]);

    expect($cctv->web_rtc_url)->toBeNull();
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

    $sourcePath = 'src-'.substr(hash('sha256', 'rtsp://admin:pass@192.168.0.1:554/Streaming/Channels/101'), 0, 20);

    Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/config/paths/replace/')
        && $request['source'] === 'rtsp://admin:pass@192.168.0.1:554/Streaming/Channels/101');
    Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/config/paths/replace/')
        && $request['source'] === 'rtsp://127.0.0.1:8554/'.$sourcePath);
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
