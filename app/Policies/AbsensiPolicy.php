<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;

class AbsensiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'pengawas', 'manajerial']);
    }

    public function view(User $user, Absensi $absensi): bool
    {
        return $user->hasRole(['admin', 'pengawas', 'manajerial']) ||
               $user->id === $absensi->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'pengawas']);
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return $user->hasRole(['admin', 'pengawas']) ||
               ($user->id === $absensi->user_id && $user->hasRole(['pengawas']));
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return $user->hasRole(['admin']);
    }

    public function checkOut(User $user, Absensi $absensi): bool
    {
        return $user->id === $absensi->user_id || $user->hasRole(['admin']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial']);
    }
}
