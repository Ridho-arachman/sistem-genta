<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    private ?string $serverKey;
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 100;
    private const CIRCUIT_BREAKER_THRESHOLD = 5; // failures before circuit opens
    private const CIRCUIT_BREAKER_TIMEOUT = 60; // seconds

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
     * Send push notification to multiple tokens with retry and circuit breaker.
     */
    public function sendToTokens(array $tokens, array $notification, array $data = []): array
    {
        if (empty($this->serverKey)) {
            Log::warning('FCM Server Key not configured');
            return ['success' => false, 'message' => 'FCM not configured'];
        }

        // Check circuit breaker
        if ($this->isCircuitOpen()) {
            Log::warning('FCM circuit breaker open, skipping notification');
            return ['success' => false, 'message' => 'FCM circuit breaker open'];
        }

        $chunks = array_chunk($tokens, 500); // FCM limit per request
        $results = [];
        $failureCount = 0;

        foreach ($chunks as $chunk) {
            $success = false;
            
            for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
                $response = Http::retry(3, 100)
                    ->withHeaders([
                        'Authorization' => 'key=' . $this->serverKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(10)
                    ->post('https://fcm.googleapis.com/fcm/send', [
                        'registration_ids' => $chunk,
                        'notification' => $notification,
                        'data' => $data,
                        'priority' => 'high',
                    ]);

                if ($response->successful()) {
                    $results[] = $response->json();
                    $success = true;
                    break;
                }

                // Don't retry on client errors (4xx)
                if ($response->status() >= 400 && $response->status() < 500) {
                    break;
                }

                // Exponential backoff
                usleep(self::RETRY_DELAY_MS * 1000 * $attempt);
            }

            if (! $success) {
                $failureCount++;
                $results[] = [
                    'success' => false,
                    'error' => 'Max retries exceeded',
                    'chunk_size' => count($chunk),
                ];
            }
        }

        // Update circuit breaker state
        $this->updateCircuitBreaker($failureCount > 0);

        return [
            'success' => $failureCount === 0,
            'results' => $results,
            'failures' => $failureCount,
        ];
    }

    /**
     * Check if circuit breaker is open.
     */
    private function isCircuitOpen(): bool
    {
        return Cache::get('fcm:circuit:open', false);
    }

    /**
     * Update circuit breaker state.
     */
    private function updateCircuitBreaker(bool $hadFailure): void
    {
        $key = 'fcm:circuit:failures';
        $failures = Cache::increment($key);
        
        if ($failures >= self::CIRCUIT_BREAKER_THRESHOLD) {
            Cache::put('fcm:circuit:open', true, self::CIRCUIT_BREAKER_TIMEOUT);
            Log::warning('FCM circuit breaker opened');
        } elseif (! $hadFailure) {
            // Reset on success
            Cache::forget($key);
            Cache::forget('fcm:circuit:open');
        }
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
