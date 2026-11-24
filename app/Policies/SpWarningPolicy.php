<?php

namespace App\Policies;

use App\Models\SpWarning;
use App\Models\User;

class SpWarningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial']);
    }

    public function view(User $user, SpWarning $spWarning): bool
    {
        return $user->hasRole(['admin', 'manajerial']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    public function update(User $user, SpWarning $spWarning): bool
    {
        return $user->hasRole(['admin']);
    }

    public function delete(User $user, SpWarning $spWarning): bool
    {
        return $user->hasRole(['admin']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial']);
    }
}
