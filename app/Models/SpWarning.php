<?php

namespace App\Models;

use Database\Factories\SpWarningFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SpWarning extends Model
{
    /** @use HasFactory<SpWarningFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'admin_id', 'level', 'reason', 'status'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (self $warning) {
            if (empty($warning->id)) {
                $warning->id = Str::ulid();
            }
        });
    }

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
}
