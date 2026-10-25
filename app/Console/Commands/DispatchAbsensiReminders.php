<?php

namespace App\Console\Commands;

use App\Jobs\AbsensiReminderJob;
use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:dispatch-absensi-reminders')]
#[Description('Dispatch check-in reminders for all dapurs with geofence')]
class DispatchAbsensiReminders extends Command
{
    public function handle(): int
    {
        $dapurs = Dapur::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('geofence_radius')
            ->get();

        if ($dapurs->isEmpty()) {
            $this->info('No dapurs with geofence configured.');

            return 0;
        }

        // Default check-in time (can be made configurable per dapur)
        $checkInTime = '07:00';

        $count = 0;

        foreach ($dapurs as $dapur) {
            // Get pengawas assigned to this dapur (has absensi today or role pengawas)
            $pengawas = User::where('role', 'pengawas')
                ->where(function ($q) use ($dapur) {
                    $q->whereHas('absensis', function ($subQ) use ($dapur) {
                        $subQ->where('dapur_id', $dapur->id)
                            ->whereDate('created_at', today());
                    })->orWhereDoesntHave('absensis', function ($subQ) use ($dapur) {
                        $subQ->where('dapur_id', $dapur->id)
                            ->whereDate('created_at', today());
                    });
                })
                ->get();

            foreach ($pengawas as $user) {
                // Check if already checked in today
                $existing = Absensi::where('user_id', $user->id)
                    ->where('dapur_id', $dapur->id)
                    ->whereDate('created_at', today())
                    ->first();

                if (! $existing) {
                    AbsensiReminderJob::dispatch(
                        $user->id,
                        'checkin_reminder',
                        $dapur->id,
                        $checkInTime
                    );
                    $count++;
                }
            }
        }

        $this->info("Dispatched {$count} check-in reminder jobs for {$dapurs->count()} dapurs.");
        Log::info('Absensi reminders dispatched', ['count' => $count, 'dapurs' => $dapurs->pluck('id')]);

        return 0;
    }
}
