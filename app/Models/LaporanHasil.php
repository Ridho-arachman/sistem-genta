<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanHasil extends Model
{
    protected $table = 'laporan_hasil';
    protected $fillable = ['laporan_id', 'indikator', 'hasil'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }
}
