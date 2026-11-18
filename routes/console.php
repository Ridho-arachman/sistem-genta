<?php

use App\Console\Commands\CctvHealthCheck;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Status kamera diverifikasi nyata (ffprobe/MediaMTX), bukan dari input manual.
Schedule::command(CctvHealthCheck::class)
    ->everyFiveMinutes()
    ->withoutOverlapping();
