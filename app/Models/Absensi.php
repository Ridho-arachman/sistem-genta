<?php

namespace App\Models;

use Database\Factories\AbsensiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    /** @use HasFactory<AbsensiFactory> */
    use HasFactory;

    #[Fillable(['user_id', 'dapur_id', 'check_in_at', 'check_out_at', 'lat', 'lng', 'selfie_path', 'status'])]
    protected $fillable = ['user_id', 'dapur_id', 'check_in_at', 'check_out_at', 'lat', 'lng', 'selfie_path', 'status'];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class);
    }
}
