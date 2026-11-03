<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPhoto extends Model
{
    protected $fillable = ['laporan_id', 'photo_path', 'indikator', 'created_by', 'updated_by'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }
}
