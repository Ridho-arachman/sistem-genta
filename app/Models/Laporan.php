<?php

namespace App\Models;

use Database\Factories\LaporanFactory;
use App\Models\LaporanHasil;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @use HasFactory<LaporanFactory> */
class Laporan extends Model
{
    use Factories\HasFactory;

    #[Fillable(['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'address', 'created_by', 'updated_by'])]
    protected $fillable = ['dapur_id', 'status', 'catatan', 'selfie_photo', 'latitude', 'longitude', 'address', 'created_by', 'updated_by'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'address' => 'string',
    ];

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
