<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Client tipis untuk MediaMTX Control API: daftarkan/hapus path stream.
 * Kosongkan config('cctv.mediamtx.api') untuk menonaktifkan (mis. di test/lokal).
 */
class MediaMtxClient
{
    public function isConfigured(): bool
    {
        return filled(config('cctv.mediamtx.api'));
    }

    /**
     * Daftarkan stream_key sebagai path di MediaMTX. Tanpa rtsp_url, path
     * menunggu publisher (mode push dari sisi dapur).
     *
     * Pakai replace, bukan add: idempoten, jadi edit RTSP URL kamera ikut
     * ter-update dan path yang hilang setelah MediaMTX restart terpasang ulang.
     */
    public function addPath(string $streamKey, ?string $rtspUrl = null): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $source = 'publisher';

        if (filled($rtspUrl)) {
            // Satu URL RTSP = satu koneksi upstream. Path kamera lain yang
            // memakai URL yang sama menarik dari path sumber ini lewat RTSP
            // lokal, bukan membuka koneksi baru (rtsplink membatasi 2 koneksi).
            $sourcePath = 'src-'.substr(hash('sha256', $rtspUrl), 0, 20);
            $registered = $this->request('post', "/v3/config/paths/replace/{$sourcePath}", [
                'source' => $rtspUrl,
                'sourceOnDemand' => true,
                'sourceOnDemandCloseAfter' => '30s',
            ])?->successful() ?? false;

            $source = $registered
                ? rtrim((string) config('cctv.mediamtx.rtsp', 'rtsp://127.0.0.1:8554'), '/').'/'.$sourcePath
                : $rtspUrl;
        }

        $response = $this->request('post', "/v3/config/paths/replace/{$streamKey}", [
            'source' => $source,
            'sourceOnDemand' => $source !== 'publisher',
            'sourceOnDemandCloseAfter' => '30s',
        ]);

        return $response?->successful() ?? false;
    }

    public function removePath(string $streamKey): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $response = $this->request('delete', "/v3/config/paths/delete/{$streamKey}");

        return $response?->successful() ?? false;
    }

    /**
     * Status path di MediaMTX. Null = tidak bisa diverifikasi (server mati /
     * path belum terdaftar), false = path ada tapi sumbernya offline.
     *
     * Pakai field `online`, bukan `ready`: dengan sourceOnDemand, `ready`
     * hanya true saat ada penonton dan bisa diputar — idle tetap false.
     */
    public function isReady(string $streamKey): ?bool
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->request('get', "/v3/paths/get/{$streamKey}");

        if ($response === null) {
            return null;
        }

        if ($response->status() === 404) {
            return null;
        }

        return $response->successful() ? (bool) $response->json('online') : null;
    }

    /**
     * Uji apakah stream benar-benar bisa diputar: minta playlist HLS dari
     * MediaMTX. Request ini sekaligus memicu pull RTSP on-demand, jadi sumber
     * yang mati (mis. token kedaluwarsa) akan gagal dan playlist tak muncul.
     *
     * `?cookieCheck=1` menghindari redirect cookie Secure yang tak jalan di
     * browser non-localhost. Null = HLS base belum diatur / server tak bisa dihubungi.
     */
    public function isServing(string $streamKey): ?bool
    {
        $base = rtrim((string) config('cctv.hls_base'), '/');

        if (! filled($base)) {
            return null;
        }

        try {
            $response = Http::timeout(12)->get("{$base}/{$streamKey}/index.m3u8?cookieCheck=1");
        } catch (Throwable $e) {
            Log::warning('MediaMTX HLS tidak bisa dihubungi', [
                'stream_key' => $streamKey,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        return $response->successful() && str_contains($response->body(), '#EXTM3U');
    }

    /**
     * Tunggu sampai sourceOnDemand selesai menarik stream saat URL baru
     * disimpan. Pull RTSP bisa butuh ~15 detik, jadi jangan beri status
     * offline hanya karena playlist belum sempat terbentuk.
     */
    public function warmPath(string $streamKey): bool
    {
        if (! filled(config('cctv.hls_base'))) {
            return false;
        }

        for ($attempt = 0; $attempt < 20; $attempt++) {
            if ($this->isServing($streamKey) === true) {
                return true;
            }

            sleep(1);
        }

        return false;
    }

    private function request(string $method, string $path, array $body = []): ?Response
    {
        $base = rtrim((string) config('cctv.mediamtx.api'), '/');
        $request = Http::timeout(5)->acceptJson();

        if (filled(config('cctv.mediamtx.username'))) {
            $request = $request->withBasicAuth(
                config('cctv.mediamtx.username'),
                config('cctv.mediamtx.password')
            );
        }

        try {
            return $body === []
                ? $request->{$method}($base.$path)
                : $request->{$method}($base.$path, $body);
        } catch (Throwable $e) {
            Log::warning('MediaMTX tidak bisa dihubungi', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
