<?php

use App\Jobs\CctvAlertJob;
use App\Jobs\CctvHealthCheckJob;
use App\Models\Cctv;
use App\Models\Dapur;
use App\Services\MediaMtxClient;
use App\Services\RtspProbe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('perintah cctv:health-check mengirim satu job per dapur bersangkutan', function () {
    Queue::fake();
    Log::spy();

    $dapur = Dapur::factory()->create();

    Cctv::factory()->create();
    Cctv::factory()->create(['dapur_id' => $dapur->id]);
    Dapur::factory()->create();

    $this->artisan('cctv:health-check')
        ->expectsOutput('Dispatched 2 CCTV health-check jobs.')
        ->assertOk();

    Queue::assertPushed(CctvHealthCheckJob::class, 2);
    Log::shouldHaveReceived('info')
        ->withArgs(fn (string $message, array $context) => $message === 'cctv:health-check batch'
            && $context['dapurs'] === 2
            && $context['dispatched'] === 2
            && is_float($context['seconds']))
        ->once();
});

test('health check menandai kamera online saat rtsp terbaca', function () {
    $cctv = Cctv::factory()->create(['status' => 'offline']);

    $this->mock(RtspProbe::class)
        ->shouldReceive('isReachable')
        ->once()
        ->andReturn(true);

    (new CctvHealthCheckJob($cctv->dapur_id))->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    $cctv->refresh();

    expect($cctv->status)->toBe('online')
        ->and($cctv->last_checked_at)->not->toBeNull();
});

test('health check memakai mediamtx saat ffprobe gagal', function () {
    Queue::fake();

    $cctv = Cctv::factory()->create(['status' => 'offline']);

    $mediaMtx = $this->mock(MediaMtxClient::class);
    $mediaMtx->shouldReceive('isServing')->andReturnNull();
    $mediaMtx->shouldReceive('isReady')->andReturn(true);

    $this->mock(RtspProbe::class)
        ->shouldReceive('isReachable')
        ->andReturn(false);

    (new CctvHealthCheckJob($cctv->dapur_id))->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($cctv->fresh()->status)->toBe('online');

    Queue::assertNotPushed(CctvAlertJob::class);
});

test('health check menandai offline saat playlist hls tidak keluar walau path masih terdaftar', function () {
    Queue::fake();

    $cctv = Cctv::factory()->create(['status' => 'online']);

    $this->mock(MediaMtxClient::class)
        ->shouldReceive('isServing')
        ->andReturn(false);

    $this->mock(RtspProbe::class)
        ->shouldReceive('isReachable')
        ->andReturnNull();

    (new CctvHealthCheckJob($cctv->dapur_id))->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($cctv->fresh()->status)->toBe('offline');

    Queue::assertPushed(CctvAlertJob::class, 1);
});

test('health check dengan cctv_id hanya memeriksa kamera itu', function () {
    Queue::fake();

    $target = Cctv::factory()->create(['status' => 'offline']);
    $other = Cctv::factory()->create(['dapur_id' => $target->dapur_id, 'status' => 'offline']);

    $this->mock(MediaMtxClient::class)
        ->shouldReceive('warmPath')
        ->andReturn(true)
        ->shouldReceive('isServing')
        ->andReturn(true);

    $this->mock(RtspProbe::class)
        ->shouldNotReceive('isReachable');

    (new CctvHealthCheckJob($target->dapur_id, $target->id))
        ->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($target->fresh()->status)->toBe('online')
        ->and($other->fresh()->status)->toBe('offline')
        ->and($other->fresh()->last_checked_at)->toBeNull();
});

test('health check menandai offline dan mengirim alert sekali saja', function () {
    Queue::fake();

    $cctv = Cctv::factory()->create(['status' => 'online']);

    $this->mock(RtspProbe::class)
        ->shouldReceive('isReachable')
        ->andReturn(false);

    $job = new CctvHealthCheckJob($cctv->dapur_id);
    $job->handle(app(RtspProbe::class), app(MediaMtxClient::class));
    $job->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($cctv->fresh()->status)->toBe('offline');
    Queue::assertPushed(CctvAlertJob::class, 1);
});

test('alert job tidak di-antre ganda untuk cctv dan status yang sama (idempoten)', function () {
    Queue::fake();

    $cctv = Cctv::factory()->create(['status' => 'offline']);

    CctvAlertJob::dispatch($cctv->id, 'offline');
    CctvAlertJob::dispatch($cctv->id, 'offline');

    Queue::assertPushed(CctvAlertJob::class, 1);
});

test('health check tidak menimpa status maintenance', function () {
    $cctv = Cctv::factory()->create(['status' => 'maintenance']);

    $this->mock(RtspProbe::class)
        ->shouldNotReceive('isReachable');

    (new CctvHealthCheckJob($cctv->dapur_id))->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    $cctv->refresh();

    expect($cctv->status)->toBe('maintenance')
        ->and($cctv->last_checked_at)->not->toBeNull();
});

test('health check membiarkan status saat hasil tidak bisa diverifikasi', function () {
    $cctv = Cctv::factory()->create(['status' => 'online']);

    $this->mock(RtspProbe::class)
        ->shouldReceive('isReachable')
        ->andReturn(null);

    (new CctvHealthCheckJob($cctv->dapur_id))->handle(app(RtspProbe::class), app(MediaMtxClient::class));

    expect($cctv->fresh()->status)->toBe('online')
        ->and($cctv->fresh()->last_checked_at)->toBeNull();
});
