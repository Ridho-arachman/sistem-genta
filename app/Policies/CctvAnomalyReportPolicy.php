<?php

namespace App\Policies;

use App\Models\CctvAnomalyReport;
use App\Models\User;

class CctvAnomalyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function view(User $user, CctvAnomalyReport $anomaly): bool
    {
        return $user->hasRole(['admin', 'manajerial', 'kepala_dapur']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    public function update(User $user, CctvAnomalyReport $anomaly): bool
    {
        return $user->hasRole(['admin']);
    }

    public function delete(User $user, CctvAnomalyReport $anomaly): bool
    {
        return $user->hasRole(['admin']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'manajerial']);
    }
}
