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

        $response = $this->request('post', "/v3/config/paths/replace/{$streamKey}", [
            'source' => filled($rtspUrl) ? $rtspUrl : 'publisher',
            'sourceOnDemand' => filled($rtspUrl),
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
     * Status path di MediaMTX. Null = tidak bisa diverifikasi (server mati/kosong).
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
            return false;
        }

        return $response->successful() ? (bool) $response->json('ready') : null;
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
