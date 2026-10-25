<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private ?string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    /**
     * Send push notification to a specific user.
     */
    public function sendToUser(int $userId, array $notification, array $data = []): array
    {
        $tokens = FcmToken::where('user_id', $userId)
            ->where('last_used_at', '>=', now()->subDays(30))
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No active FCM tokens for user'];
        }

        return $this->sendToTokens($tokens, $notification, $data);
    }

    /**
     * Send push notification to multiple tokens.
     */
    public function sendToTokens(array $tokens, array $notification, array $data = []): array
    {
        if (empty($this->serverKey)) {
            Log::warning('FCM Server Key not configured');

            return ['success' => false, 'message' => 'FCM not configured'];
        }

        $chunks = array_chunk($tokens, 500); // FCM limit per request
        $results = [];

        foreach ($chunks as $chunk) {
            $response = Http::withHeaders([
                'Authorization' => 'key='.$this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'registration_ids' => $chunk,
                'notification' => $notification,
                'data' => $data,
                'priority' => 'high',
            ]);

            $results[] = $response->json();
        }

        return [
            'success' => true,
            'results' => $results,
        ];
    }

    /**
     * Send absensi reminder notification.
     */
    public function sendAbsensiReminder(int $userId, string $type, string $dapurName, string $time): array
    {
        $titles = [
            'checkin_reminder' => '⏰ Waktu Check-in Absen',
            'checkout_reminder' => '🏁 Waktu Check-out Absen',
        ];

        $bodies = [
            'checkin_reminder' => "Jangan lupa check-in di {$dapurName} pukul {$time}",
            'checkout_reminder' => "Waktunya check-out dari {$dapurName} pukul {$time}",
        ];

        return $this->sendToUser($userId, [
            'title' => $titles[$type] ?? 'Pengingat Absensi',
            'body' => $bodies[$type] ?? 'Anda memiliki jadwal absensi',
            'icon' => 'ic_stat_icon_config_sample',
            'color' => '#22c55e',
            'sound' => 'default',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], [
            'type' => $type,
            'dapur_name' => $dapurName,
            'time' => $time,
            'action_url' => $type === 'checkin_reminder' ? '/absensi/create' : '/absensi',
        ]);
    }

    /**
     * Send CCTV alert notification.
     */
    public function sendCctvAlert(int $userId, string $cctvName, string $status, int $dapurId): array
    {
        $statusLabels = [
            'offline' => '🔴 OFFLINE',
            'maintenance' => '🟡 MAINTENANCE',
            'online' => '🟢 ONLINE',
        ];

        return $this->sendToUser($userId, [
            'title' => '📷 Notifikasi CCTV',
            'body' => "Kamera {$cctvName} berstatus ".($statusLabels[$status] ?? ucfirst($status)),
            'icon' => 'ic_stat_icon_config_sample',
            'color' => $status === 'offline' ? '#ef4444' : ($status === 'maintenance' ? '#f59e0b' : '#22c55e'),
            'sound' => 'default',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ], [
            'type' => 'cctv_alert',
            'cctv_name' => $cctvName,
            'status' => $status,
            'dapur_id' => $dapurId,
            'action_url' => "/cctvs/monitoring/{$dapurId}",
        ]);
    }

    /**
     * Send notification to all admins.
     */
    public function sendToAdmins(array $notification, array $data = []): array
    {
        $adminTokens = FcmToken::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })->where('last_used_at', '>=', now()->subDays(30))
            ->pluck('token')
            ->toArray();

        if (empty($adminTokens)) {
            return ['success' => false, 'message' => 'No admin tokens'];
        }

        return $this->sendToTokens($adminTokens, $notification, $data);
    }

    /**
     * Send test notification.
     */
    public function sendTest(int $userId): array
    {
        return $this->sendToUser($userId, [
            'title' => '🧪 Test Notification',
            'body' => 'Ini adalah test notifikasi dari GENTAPANGAN',
            'icon' => 'ic_stat_icon_config_sample',
            'color' => '#22c55e',
            'sound' => 'default',
        ], [
            'type' => 'test',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
