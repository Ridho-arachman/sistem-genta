<?php

namespace App\Models;

use Database\Factories\CctvAnomalyReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CctvAnomalyReport extends Model
{
    /** @use HasFactory<CctvAnomalyReportFactory> */
    use HasFactory;

    #[Fillable(['cctv_id', 'admin_id', 'description', 'severity', 'status'])]
    protected $fillable = ['cctv_id', 'admin_id', 'description', 'severity', 'status'];

    public function cctv(): BelongsTo
    {
        return $this->belongsTo(Cctv::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '!=', 'selesai');
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }
}
