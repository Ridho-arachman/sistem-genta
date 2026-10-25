<?php

namespace App\Models;

use Database\Factories\SpWarningFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpWarning extends Model
{
    /** @use HasFactory<SpWarningFactory> */
    use HasFactory;

    #[Fillable(['user_id', 'admin_id', 'level', 'reason', 'status'])]
    protected $fillable = ['user_id', 'admin_id', 'level', 'reason', 'status'];

    protected $casts = [
        'level' => 'integer',
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
