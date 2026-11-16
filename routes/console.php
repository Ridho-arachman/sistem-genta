<?php

use App\Console\Commands\CctvHealthCheck;
use App\Console\Commands\DispatchAbsensiReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled commands
Schedule::command(DispatchAbsensiReminders::class)
    ->dailyAt('06:50');

// Status kamera diverifikasi nyata (ffprobe/MediaMTX), bukan dari input manual.
Schedule::command(CctvHealthCheck::class)
    ->everyFiveMinutes()
    ->withoutOverlapping();
