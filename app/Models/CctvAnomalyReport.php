<?php

namespace App\Models;

use Database\Factories\CctvAnomalyReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CctvAnomalyReport extends Model
{
    /** @use HasFactory<CctvAnomalyReportFactory> */
    use HasFactory;

    protected $fillable = ['cctv_id', 'admin_id', 'description', 'severity', 'status'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (self $report) {
            if (empty($report->id)) {
                $report->id = Str::ulid();
            }
        });
    }

    public function cctv(): BelongsTo
    {
        return $this->belongsTo(Cctv::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
