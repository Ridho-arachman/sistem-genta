<?php

namespace App\Providers;

use App\Models\CctvAnomalyReport;
use App\Models\Laporan;
use App\Policies\CctvAnomalyReportPolicy;
use App\Policies\LaporanPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        CctvAnomalyReport::class => CctvAnomalyReportPolicy::class,
        Laporan::class => LaporanPolicy::class,
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
        // Komponen reusable khusus halaman admin: <x-admin-panel>, <x-admin-page-heading>, dst.
        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'admin');

        // Komponen reusable khusus halaman pengawas: <x-pengawas::page-heading>, dst.
        Blade::anonymousComponentPath(resource_path('views/pengawas/components'), 'pengawas');

        $this->defineRateLimiters();
    }

    /**
     * Batas permintaan (rate limit) memakai cache store "database" — tanpa Redis.
     *
     * Guest dibatasi per IP dan per email (menahan penyerangan terdistribusi ke satu akun);
     * pengguna login dibatasi per user ID. Kunci menyertakan URI rute agar satu rute
     * tidak menghabiskan kuota rute lain.
     */
    private function defineRateLimiters(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            $scope = $request->route()?->uri() ?? $request->path();

            if ($user = $request->user()) {
                return Limit::perMinute(5)->by('user:'.$user->getAuthIdentifier().':'.$scope);
            }

            return [
                Limit::perMinute(5)->by('ip:'.$request->ip().':'.$scope),
                Limit::perMinute(30)->by('email:'.Str::lower(trim((string) $request->input('email'))).':'.$scope),
            ];
        });

        RateLimiter::for('mobile', fn (Request $request) => Limit::perMinute(30)
            ->by('user:'.$request->user()?->getAuthIdentifier()));

        RateLimiter::for('web', fn (Request $request) => Limit::perMinute(60)
            ->by('user:'.($request->user()?->getAuthIdentifier() ?? $request->ip()).':'.($request->route()?->uri() ?? $request->path())));
    }
}
