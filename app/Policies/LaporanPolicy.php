<?php

namespace App\Policies;

use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'pengawas', 'kepala_dapur']);
    }

    public function view(User $user, Laporan $laporan): bool
    {
        if ($user->isAdmin() || $user->isManajerial() || $user->isKepalaDapur()) {
            return true;
        }

        return $user->isPengawas() && $laporan->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'pengawas']);
    }

    public function update(User $user, Laporan $laporan): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Laporan $laporan): bool
    {
        return $user->isAdmin();
    }
}
