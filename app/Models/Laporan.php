<?php

namespace App\Models;

use Database\Factories\LaporanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @use HasFactory<LaporanFactory> */
class Laporan extends Model
{
    use Factories\HasFactory;

    #[Fillable(['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'hasil', 'address'])]
    protected $fillable = ['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'hasil', 'address'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'hasil' => 'array',
        'address' => 'string',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LaporanPhoto::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '!=', 'selesai');
    }
}
