<?php

namespace App\Jobs;

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CctvAlertJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $cctvId,
        public string $status, // 'offline' | 'maintenance' | 'online'
        public bool $notifyAdmins = true
    ) {}

    public function handle(FcmService $fcmService): void
    {
        $cctv = Cctv::with('dapur')->find($this->cctvId);

        if (! $cctv) {
            Log::warning('CctvAlertJob: CCTV not found', ['cctv_id' => $this->cctvId]);

            return;
        }

        $dapur = $cctv->dapur;

        if ($this->notifyAdmins) {
            // Notify all admins
            $fcmService->sendToAdmins([
                'title' => '📷 Notifikasi CCTV',
                'body' => "Kamera {$cctv->nama} berstatus ".$this->getStatusLabel($this->status),
                'icon' => 'ic_stat_icon_config_sample',
                'color' => $this->getStatusColor($this->status),
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ], [
                'type' => 'cctv_alert',
                'cctv_name' => $cctv->nama,
                'status' => $this->status,
                'dapur_id' => $dapur?->id,
                'action_url' => $dapur ? "/cctvs/monitoring/{$dapur->id}" : null,
            ]);
        } elseif ($dapur) {
            // Notify pengawas assigned to this dapur (optional: you can add dapur-user relation)
            $pengawas = User::where('role', 'pengawas')
                ->whereHas('absensis', function ($q) use ($dapur) {
                    $q->where('dapur_id', $dapur->id)
                        ->whereDate('created_at', today());
                })
                ->get();

            foreach ($pengawas as $user) {
                $fcmService->sendCctvAlert(
                    $user->id,
                    $cctv->nama,
                    $this->status,
                    $dapur->id
                );
            }
        }
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'offline' => '🔴 OFFLINE',
            'maintenance' => '🟡 MAINTENANCE',
            'online' => '🟢 ONLINE',
            default => ucfirst($status),
        };
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'offline' => '#ef4444',
            'maintenance' => '#f59e0b',
            'online' => '#22c55e',
            default => '#22c55e',
        };
    }
}
