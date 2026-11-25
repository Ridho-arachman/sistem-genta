<?php

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (! function_exists('settings')) {
    /**
     * Get a setting value by key.
     *
     * @param  mixed  $default
     * @return mixed
     */
    function settings(string $key, $default = null)
    {
        try {
            if (app()->runningInConsole() && ! Schema::hasTable('settings')) {
                return $default;
            }

            // Baca dari cache 'settings:all' (di-cache 24 jam oleh
            // Settings::allKeyed(), di-invalidasi saat Settings::set()).
            // Hindari query DB per kunci pada path panas.
            $all = Cache::remember('settings:all', 86400, function () {
                return Settings::allKeyed();
            });

            return $all[$key] ?? $default;
        } catch (Exception $e) {
            return $default;
        }
    }
}
