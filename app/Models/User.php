<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    #[Fillable(['name', 'email', 'password', 'role', 'registered_by'])]
    #[Hidden(['password', 'remember_token'])]
    protected $fillable = ['name', 'email', 'password', 'role', 'registered_by'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function registeredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'registered_by');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function spWarnings(): HasMany
    {
        return $this->hasMany(SpWarning::class, 'user_id');
    }

    public function spWarningsIssued(): HasMany
    {
        return $this->hasMany(SpWarning::class, 'admin_id');
    }

    public function cctvAnomalyReports(): HasMany
    {
        return $this->hasMany(CctvAnomalyReport::class, 'admin_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengawas(): bool
    {
        return $this->role === 'pengawas';
    }

    public function isManajerial(): bool
    {
        return $this->role === 'manajerial';
    }

    public function isKepalaDapur(): bool
    {
        return $this->role === 'kepala_dapur';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles);
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canViewCctvDashboard(): bool
    {
        return $this->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function canViewAbsensi(): bool
    {
        return $this->hasRole(['admin', 'pengawas', 'manajerial']);
    }

    public function canManageAbsensi(): bool
    {
        return $this->hasRole(['admin', 'pengawas']);
    }

    public function canExportReports(): bool
    {
        return $this->hasRole(['admin', 'manajerial']);
    }
}
