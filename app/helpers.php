<?php

use App\Models\Settings;
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

            return Settings::get($key, $default);
        } catch (Exception $e) {
            return $default;
        }
    }
}
