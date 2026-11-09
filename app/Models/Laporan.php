<?php

namespace App\Models;

use Database\Factories\LaporanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/** @use HasFactory<LaporanFactory> */
class Laporan extends Model
{
    use Factories\HasFactory;

    #[Fillable(['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'address', 'created_by', 'updated_by'])]
    protected $fillable = ['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'address', 'created_by', 'updated_by'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'address' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $laporan) {
            if (empty($laporan->id)) {
                $laporan->id = Str::ulid();
            }
        });
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hasil(): HasMany
    {
        return $this->hasMany(LaporanHasil::class);
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
