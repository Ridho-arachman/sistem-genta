<?php

namespace App\Models;

use Database\Factories\CctvFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cctv extends Model
{
    /** @use HasFactory<CctvFactory> */
    use HasFactory;

    #[Fillable(['dapur_id', 'nama', 'rtsp_url', 'status', 'section', 'camera_number', 'stream_key', 'last_checked_at'])]
    protected $fillable = ['dapur_id', 'nama', 'rtsp_url', 'status', 'section', 'camera_number', 'stream_key', 'last_checked_at'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'camera_number' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $cctv) {
            if (empty($cctv->id)) {
                $cctv->id = Str::ulid();
            }
            if (empty($cctv->stream_key)) {
                $cctv->stream_key = Str::ulid();
            }
        });
    }

    /**
     * URL HLS yang diputar browser: diturunkan dari media server kalau
     * dikonfigurasi, kalau tidak pakai hls_url manual.
     */
    public function getStreamUrlAttribute(): ?string
    {
        $base = config('cctv.hls_base');

        if ($base && $this->stream_key) {
            return rtrim($base, '/').'/'.$this->stream_key.'/index.m3u8';
        }

        return $this->hls_url;
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function anomalyReports(): HasMany
    {
        return $this->hasMany(CctvAnomalyReport::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->nama];
        if ($this->section) {
            $parts[] = "({$this->section})";
        }
        if ($this->camera_number) {
            $parts[] = "Cam {$this->camera_number}";
        }

        return implode(' ', $parts);
    }
}
