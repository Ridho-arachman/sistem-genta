<?php

namespace App\Models;

use Database\Factories\DapurFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dapur extends Model
{
    /** @use HasFactory<DapurFactory> */
    use HasFactory;

    #[Fillable(['nama', 'lokasi', 'alamat', 'kecamatan', 'kabupaten_kota', 'provinsi', 'latitude', 'longitude', 'geofence_radius'])]
    protected $fillable = ['nama', 'lokasi', 'alamat', 'kecamatan', 'kabupaten_kota', 'provinsi', 'latitude', 'longitude', 'geofence_radius'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'geofence_radius' => 'integer',
    ];

    public function cctvs(): HasMany
    {
        return $this->hasMany(Cctv::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pengawas(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dapur_user')
            ->where('role', 'pengawas')
            ->withTimestamps();
    }

    public function kepalaDapur(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'kepala_dapur');
    }

    // Scope for user-based dapur filtering
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin() || $user->isManajerial()) {
            return $query;
        }

        $dapurIds = $user->getAccessibleDapurIds();

        return $query->whereIn('id', $dapurIds);
    }
}
