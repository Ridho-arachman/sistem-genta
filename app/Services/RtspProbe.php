<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Cek apakah stream RTSP benar-benar bisa dibaca, pakai ffprobe.
 * Butuh binary ffprobe di server (atau path absolut via CCTV_FFPROBE).
 */
class RtspProbe
{
    /**
     * Null = tidak bisa diverifikasi (ffprobe tidak tersedia / URL kosong).
     */
    public function isReachable(?string $url): ?bool
    {
        if (blank($url)) {
            return null;
        }

        $binary = (string) config('cctv.ffprobe', 'ffprobe');
        $executable = file_exists($binary) ? $binary : (new ExecutableFinder)->find($binary);

        if (! $executable) {
            Log::warning('CCTV health check: ffprobe tidak ditemukan', ['binary' => $binary]);

            return null;
        }

        $process = new Process([
            $executable,
            '-v', 'error',
            '-rtsp_transport', 'tcp',
            '-rw_timeout', '10000000',
            '-i', $url,
            '-read_intervals', '%+1',
            '-show_entries', 'format=format_name',
            '-of', 'default=nw=1:nk=1',
        ]);
        $process->setTimeout((float) config('cctv.probe_timeout', 8));

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            return false;
        }

        return $process->isSuccessful();
    }
}
