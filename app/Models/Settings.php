<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->castedValue;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (is_array($value) || is_object($value) ? json_encode($value) : (string) $value)]
        );
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
     */
    public static function allKeyed(): array
    {
        return static::all()->pluck('casted_value', 'key')->toArray();
    }
}
