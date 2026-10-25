<?php

namespace App\Models;

use Database\Factories\DapurFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dapur extends Model
{
    /** @use HasFactory<DapurFactory> */
    use HasFactory;

    #[Fillable(['nama', 'lokasi', 'alamat', 'latitude', 'longitude', 'geofence_radius'])]
    protected $fillable = ['nama', 'lokasi', 'alamat', 'latitude', 'longitude', 'geofence_radius'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'geofence_radius' => 'integer',
    ];

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
