<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share settings with all views (skip if table doesn't exist, e.g., during testing)
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Settings::allKeyed();
                    $view->with('settings', $settings);
                } else {
                    $view->with('settings', []);
                }
            } catch (\Exception $e) {
                $view->with('settings', []);
            }
        });
    }
}
