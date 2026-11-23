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

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120, 300];

    /**
     * @param  string  $dapurId  dapur yang dicek
     * @param  string|null  $cctvId  batasi ke satu kamera (dipakai saat admin
     *                               menyimpan rtsp_url baru, biar status tak
     *                               menunggu jadwal berikutnya)
     */
    public function __construct(public string $dapurId, public ?string $cctvId = null) {}

    public function handle(RtspProbe $probe, MediaMtxClient $mediaMtx): void
    {
        $query = Cctv::where('dapur_id', $this->dapurId);

        if (filled($this->cctvId)) {
            $query->where('id', $this->cctvId);
        }

        $cctvs = $query->get();

        foreach ($cctvs as $cctv) {
            // Status maintenance di-set manual admin; jangan ditimpa hasil probe.
            if ($cctv->status === 'maintenance') {
                $cctv->forceFill(['last_checked_at' => now()])->saveQuietly();

                continue;
            }

            // Saat admin menyimpan URL baru, source on-demand butuh waktu
            // beberapa detik sebelum playlist HLS bisa dibaca. Tunggu dulu di
            // job (bukan di request) supaya status tak salah jadi offline.
            if (filled($this->cctvId) && $cctv->stream_key) {
                $mediaMtx->warmPath($cctv->stream_key);
            }

            // MediaMTX diutamakan: itulah yang benar-benar melayani video di browser.
            // isServing() uji playlist HLS (sekaligus memicu pull RTSP on-demand),
            // karena field `online` hanya berarti path punya sumber, bukan stream hidup.
            // ffprobe hanya jadi acuan bila MediaMTX sekaligus HLS tak bisa menjawab.
            $streamState = $cctv->stream_key
                ? ($mediaMtx->isServing($cctv->stream_key) ?? $mediaMtx->isReady($cctv->stream_key))
                : null;

            $online = $streamState ?? $probe->isReachable($cctv->rtsp_url);

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
