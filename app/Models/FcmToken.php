<?php

namespace App\Models;

use Database\Factories\FcmTokenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    /** @use HasFactory<FcmTokenFactory> */
    use HasFactory;

    #[Fillable(['user_id', 'token', 'platform', 'last_used_at'])]
    protected $fillable = ['user_id', 'token', 'platform', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
