<?php

namespace App\Models;

use Database\Factories\DapurFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dapur extends Model
{
    /** @use HasFactory<DapurFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
        'deleted_by' => 'string',
    ];

    protected $fillable = ['nama', 'alamat', 'map_embed_url', 'kecamatan', 'kabupaten_kota', 'provinsi'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (self $dapur) {
            if (empty($dapur->id)) {
                $dapur->id = Str::ulid();
            }
        });

        static::deleting(function (self $dapur) {
            if (! $dapur->isForceDeleting() && auth()->check()) {
                $dapur->deleted_by = auth()->id();
                $dapur->saveQuietly();
            }
        });
    }

    public function cctvs(): HasMany
    {
        return $this->hasMany(Cctv::class);
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

    /**
     * Wilayah turunan dari kecamatan/kabupaten_kota, menggantikan kolom `lokasi`.
     */
    public function getWilayahAttribute(): ?string
    {
        $parts = array_filter([
            $this->kabupaten_kota,
            $this->kecamatan ? 'Kec. '.$this->kecamatan : null,
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }

    /**
     * Persentase kamera online (0-100). Pakai hasil withCount kalau ada,
     * supaya halaman daftar dan dashboard tidak menghitung ulang sendiri.
     */
    public function getProgressAttribute(): int
    {
        $total = $this->cctvs_count ?? $this->cctvs->count();

        if ($total === 0) {
            return 0;
        }

        $online = $this->cctvs_online_count ?? $this->cctvs->where('status', 'online')->count();

        return (int) min(100, round(($online / $total) * 100));
    }

    /**
     * Status kesehatan kamera: operasional | perlu_perhatian | kamera_bermasalah.
     */
    public function getStatusAttribute(): string
    {
        $total = $this->cctvs_count ?? $this->cctvs->count();
        $online = $this->cctvs_online_count ?? $this->cctvs->where('status', 'online')->count();

        if ($total === 0 || $online === 0) {
            return 'kamera_bermasalah';
        }

        return $online < $total ? 'perlu_perhatian' : 'operasional';
    }
}
