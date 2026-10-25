<?php

namespace App\Models;

use Database\Factories\CctvFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cctv extends Model
{
    /** @use HasFactory<CctvFactory> */
    use HasFactory;

    #[Fillable(['dapur_id', 'nama', 'ip_address', 'rtsp_url', 'hls_url', 'status', 'section', 'camera_number'])]
    protected $fillable = ['dapur_id', 'nama', 'ip_address', 'rtsp_url', 'hls_url', 'status', 'section', 'camera_number'];

    protected $casts = [
        'camera_number' => 'integer',
    ];

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
