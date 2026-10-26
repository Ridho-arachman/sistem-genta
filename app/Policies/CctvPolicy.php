<?php

namespace App\Policies;

use App\Models\Cctv;
use App\Models\User;

class CctvPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function view(User $user, Cctv $cctv): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    public function update(User $user, Cctv $cctv): bool
    {
        return $user->hasRole(['admin']);
    }

    public function delete(User $user, Cctv $cctv): bool
    {
        return $user->hasRole(['admin']);
    }

    public function monitor(User $user, Cctv $cctv): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }
}
