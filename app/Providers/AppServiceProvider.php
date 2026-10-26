<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Policies\AbsensiPolicy;
use App\Policies\CctvAnomalyReportPolicy;
use App\Policies\CctvPolicy;
use App\Policies\DapurPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Absensi::class => AbsensiPolicy::class,
        Cctv::class => CctvPolicy::class,
        CctvAnomalyReport::class => CctvAnomalyReportPolicy::class,
        Dapur::class => DapurPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
