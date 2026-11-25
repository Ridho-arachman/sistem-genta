<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Settings extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    protected static function booted(): void
    {
        static::creating(function (self $setting) {
            if (empty($setting->id)) {
                $setting->id = Str::ulid();
            }
        });
    }

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (is_array($value) || is_object($value) ? json_encode($value) : (string) $value)]
        );

        // Invalidate cache semua setting agar baca berikutnya ambil data terbaru.
        Cache::forget('settings:all');

        return $setting;
    }

    /**
     * Get the casted value based on type.
     */
    public function getCastedValueAttribute()
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get all settings as key-value array for a group.
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Get all settings as key-value array (with casted values).
     *
     * Di-cache selama 24 jam; di-invalidasi otomatis saat ada setting yang diubah.
     */
    public static function allKeyed(): array
    {
        return Cache::remember('settings:all', 86400, function () {
            return static::all()->pluck('casted_value', 'key')->toArray();
        });
    }
}
