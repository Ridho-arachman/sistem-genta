<?php

namespace App\Jobs;

use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AbsensiReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public string $type, // 'checkin_reminder' | 'checkout_reminder'
        public int $dapurId,
        public string $scheduledTime // '07:00' format
    ) {}

    public function handle(FcmService $fcmService): void
    {
        $user = User::find($this->userId);
        $dapur = Dapur::find($this->dapurId);

        if (! $user || ! $dapur) {
            Log::warning('AbsensiReminderJob: user/dapur not found', [
                'user_id' => $this->userId,
                'dapur_id' => $this->dapurId,
            ]);

            return;
        }

        // Idempotency: prevent duplicate notifications on job retry
        $cacheKey = "reminder:{$this->userId}:{$this->type}:{$this->dapurId}:".today()->toDateString();
        if (! Cache::add($cacheKey, true, 86400)) {
            return; // Already sent today
        }

        // Check if already checked in/out today
        $existing = Absensi::where('user_id', $this->userId)
            ->where('dapur_id', $this->dapurId)
            ->whereDate('created_at', today())
            ->first();

        $shouldSend = match ($this->type) {
            'checkin_reminder' => ! $existing,
            'checkout_reminder' => $existing && ! $existing->check_out_at,
            default => false,
        };

        if (! $shouldSend) {
            return;
        }

        $fcmService->sendAbsensiReminder(
            $this->userId,
            $this->type,
            $dapur->nama,
            $this->scheduledTime
        );
    }
}
