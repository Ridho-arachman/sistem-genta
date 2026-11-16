<?php

namespace App\Console\Commands;

use App\Jobs\CctvHealthCheckJob;
use App\Models\Dapur;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cctv:health-check')]
#[Description('Dispatch pengecekan status kamera per dapur (RTSP/MediaMTX)')]
class CctvHealthCheck extends Command
{
    public function handle(): int
    {
        $count = 0;

        Dapur::whereHas('cctvs')->pluck('id')->chunk(50)->each(function ($ids) use (&$count) {
            foreach ($ids as $dapurId) {
                CctvHealthCheckJob::dispatch((string) $dapurId);
                $count++;
            }
        });

        $this->info("Dispatched {$count} CCTV health-check jobs.");

        return self::SUCCESS;
    }
}
