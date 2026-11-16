<?php

namespace App\Jobs;

use App\Models\Cctv;
use App\Services\MediaMtxClient;
use App\Services\RtspProbe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Cek status kamera satu dapur. Dipisah per dapur supaya 400 dapur
 * tidak jadi satu job raksasa; antrian bisa jalan paralel.
 */
class CctvHealthCheckJob implements ShouldQueue
{
    use Queueable;

    /** Probe 5 kamera × timeout 8 detik masih di bawah batas ini. */
    public int $timeout = 180;

    public function __construct(public string $dapurId) {}

    public function handle(RtspProbe $probe, MediaMtxClient $mediaMtx): void
    {
        $cctvs = Cctv::where('dapur_id', $this->dapurId)->get();

        foreach ($cctvs as $cctv) {
            // Status maintenance di-set manual admin; jangan ditimpa hasil probe.
            if ($cctv->status === 'maintenance') {
                $cctv->forceFill(['last_checked_at' => now()])->saveQuietly();

                continue;
            }

            $online = $probe->isReachable($cctv->rtsp_url)
                ?? ($cctv->stream_key ? $mediaMtx->isReady($cctv->stream_key) : null);

            if ($online === null) {
                continue; // tidak bisa diverifikasi, biarkan status lama
            }

            $newStatus = $online ? 'online' : 'offline';
            $wentOffline = $newStatus === 'offline' && $cctv->status !== 'offline';

            $cctv->forceFill([
                'status' => $newStatus,
                'last_checked_at' => now(),
            ])->saveQuietly();

            if ($wentOffline) {
                CctvAlertJob::dispatch($cctv->id, 'offline');
            }
        }
    }
}
