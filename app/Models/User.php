<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    #[Fillable(['name', 'email', 'password', 'role', 'registered_by', 'dapur_id', 'created_by', 'updated_by', 'deleted_by'])]
    #[Hidden(['password', 'remember_token'])]
    protected $fillable = ['name', 'email', 'password', 'role', 'registered_by', 'dapur_id', 'created_by', 'updated_by', 'deleted_by'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
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

    // Dapur relationships
    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function dapurs(): BelongsToMany
    {
        return $this->belongsToMany(Dapur::class, 'dapur_user');
    }

    // Helper to get all dapur IDs user can access
    public function getAccessibleDapurIds(): array
    {
        if ($this->isAdmin() || $this->isManajerial()) {
            return Dapur::pluck('id')->toArray();
        }

        if ($this->isKepalaDapur()) {
            return $this->dapur_id ? [$this->dapur_id] : [];
        }

        if ($this->isPengawas()) {
            return $this->dapurs->pluck('id')->toArray();
        }

        return [];
    }

    // Check if user can access specific dapur
    public function canAccessDapur(int $dapurId): bool
    {
        if ($this->isAdmin() || $this->isManajerial()) {
            return true;
        }

        return in_array($dapurId, $this->getAccessibleDapurIds());
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

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Model Events
     */
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            $user->created_by = auth()->id();
            $user->updated_by = auth()->id();
            // registered_by is set in controller for consistency
            if (! $user->registered_by && auth()->check()) {
                $user->registered_by = auth()->id();
            }
        });

        static::updating(function (self $user) {
            $user->updated_by = auth()->id();
        });

        static::deleting(function (self $user) {
            // Prevent hard delete of admin users via force delete
            if ($user->isForceDeleting() && $user->isAdmin()) {
                return false;
            }
            // Set deleted_by on soft delete
            if (! $user->isForceDeleting() && auth()->check()) {
                $user->deleted_by = auth()->id();
                $user->saveQuietly();
            }
            // Detach dapur_user pivot on soft delete
            if (! $user->isForceDeleting()) {
                $user->dapurs()->detach();
            }
        });
    }

    public function getStatusAttribute(): string
    {
        return $this->status_label;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->trashed()) {
            return 'Dihapus';
        }
        if (! $this->email_verified_at) {
            return 'Belum Verifikasi';
        }

        return 'Aktif';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_label) {
            'Aktif' => 'bg-[#22C55E15] text-[#166534]',
            'Belum Verifikasi' => 'bg-[#F59E0B15] text-[#854D0E]',
            'Dihapus' => 'bg-[#FEF2F2] text-[#EF4444]',
            default => 'bg-[var(--color-surface)] text-[var(--color-text-muted)]',
        };
    }

    public function getAccessDescriptionAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Semua data',
            'pengawas' => $this->dapurs->count() > 0 ? 'Dapur yang diamati' : '-',
            'kepala_dapur' => $this->dapur_id ? 'Dapur sendiri' : '-',
            'manajerial' => 'Dashboard & laporan',
            default => '-',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'pengawas' => 'Pengawas',
            'kepala_dapur' => 'Kepala Dapur',
            'manajerial' => 'Pimpinan',
            default => ucfirst($this->role),
        };
    }
}
