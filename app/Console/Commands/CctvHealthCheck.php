<?php

namespace App\Console\Commands;

use App\Jobs\CctvHealthCheckJob;
use App\Models\Dapur;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('cctv:health-check')]
#[Description('Dispatch pengecekan status kamera per dapur (RTSP/MediaMTX)')]
class CctvHealthCheck extends Command
{
    public function handle(): int
    {
        $startedAt = microtime(true);
        $count = 0;

        Dapur::whereHas('cctvs')->pluck('id')->chunk(50)->each(function ($ids) use (&$count) {
            $chunkStartedAt = microtime(true);

            foreach ($ids as $dapurId) {
                CctvHealthCheckJob::dispatch(
                    (string) $dapurId,
                    null,
                    Log::sharedContext()['correlation_id'] ?? null
                );
                $count++;
            }

            // Durasi per batch, bukan hanya total: dispatch yang melambat tanda
            // worker/antrean sudah jenuh, walau keseluruhannya kelihatan biasa.
            Log::info('cctv:health-check batch', [
                'dapurs' => $ids->count(),
                'dispatched' => $count,
                'seconds' => round(microtime(true) - $chunkStartedAt, 2),
            ]);
        });

        $this->info("Dispatched {$count} CCTV health-check jobs.");
        $this->info(sprintf('Total %.2f seconds.', microtime(true) - $startedAt));

        return self::SUCCESS;
    }
}
