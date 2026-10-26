<?php

namespace App\Policies;

use App\Models\Dapur;
use App\Models\User;

class DapurPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function view(User $user, Dapur $dapur): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    public function update(User $user, Dapur $dapur): bool
    {
        return $user->hasRole(['admin']);
    }

    public function delete(User $user, Dapur $dapur): bool
    {
        return $user->hasRole(['admin']);
    }
}
