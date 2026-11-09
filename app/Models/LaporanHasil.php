<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LaporanHasil extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'laporan_hasil';

    protected $fillable = ['laporan_id', 'indikator', 'hasil', 'created_by', 'updated_by'];

    protected static function booted(): void
    {
        static::creating(function (self $hasil) {
            if (empty($hasil->id)) {
                $hasil->id = Str::ulid();
            }
        });
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }
}
