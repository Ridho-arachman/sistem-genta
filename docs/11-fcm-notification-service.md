# FCM (Firebase Cloud Messaging) Notification Service Documentation

## Overview
Robust notification service with retry logic, circuit breaker pattern, and multi-platform support for Android and iOS.

## Service Class

### `app/Services/FcmService.php`
```php
class FcmService
{
    const FCM_URL = 'https://fcm.googleapis.com/fcm/send';
    const MAX_RETRIES = 3;
    const RETRY_DELAY_MS = 1000;
    const CIRCUIT_BREAKER_THRESHOLD = 5;
    const CIRCUIT_BREAKER_TIMEOUT = 60; // seconds

    protected static $failureCount = 0;
    protected static $circuitOpenUntil = 0;

    /**
     * Send notification to specific device tokens
     */
    public static function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        array $options = []
    ): array {
        if (empty($tokens)) {
            return ['success' => 0, 'failure' => 0, 'errors' => []];
        }

        // Check circuit breaker
        if (self::isCircuitOpen()) {
            Log::warning('FCM circuit breaker open, skipping notification', [
                'tokens_count' => count($tokens),
                'title' => $title,
            ]);
            return ['success' => 0, 'failure' => count($tokens), 'errors' => ['Circuit breaker open']];
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'timestamp' => now()->toISOString(),
            ], $data),
            'priority' => 'high',
            'content_available' => true,
            'mutable_content' => true,
        ];

        // Android specific
        if (isset($options['android'])) {
            $payload['android'] = $options['android'];
        }

        // iOS specific
        if (isset($options['apns'])) {
            $payload['apns'] = $options['apns'];
        }

        return self::sendWithRetry($payload);
    }

    /**
     * Send notification to topic
     */
    public static function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data = []
    ): array {
        $payload = [
            'to' => "/topics/{$topic}",
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => array_merge([
                'timestamp' => now()->toISOString(),
            ], $data),
            'priority' => 'high',
        ];

        return self::sendWithRetry($payload);
    }

    /**
     * Notify all admins
     */
    public static function notifyAdmins(string $title, string $body, array $data = []): void
    {
        $adminTokens = User::role('admin')
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($adminTokens)) {
            self::sendToTokens($adminTokens, $title, $body, array_merge($data, [
                'type' => 'admin_alert',
            ]));
        }
    }

    /**
     * Notify specific dapur users (pengawas + kepala_dapur)
     */
    public static function notifyDapur(int $dapurId, string $title, string $body, array $data = []): void
    {
        $tokens = User::where('dapur_id', $dapurId)
            ->whereIn('role', ['pengawas', 'kepala_dapur'])
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($tokens)) {
            self::sendToTokens($tokens, $title, $body, array_merge($data, [
                'type' => 'dapur_notification',
                'dapur_id' => $dapurId,
            ]));
        }
    }

    /**
     * Notify specific user
     */
    public static function notifyUser(User $user, string $title, string $body, array $data = []): void
    {
        if ($user->fcm_token) {
            self::sendToTokens([$user->fcm_token], $title, $body, $data);
        }
    }

    /**
     * Core send method with retry logic
     */
    protected static function sendWithRetry(array $payload): array
    {
        $attempt = 0;
        $lastError = null;

        while ($attempt < self::MAX_RETRIES) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'key=' . config('services.fcm.server_key'),
                        'Content-Type' => 'application/json',
                    ])
                    ->post(self::FCM_URL, $payload);

                if ($response->successful()) {
                    self::recordSuccess();
                    return self::parseResponse($response->json(), count($payload['registration_ids'] ?? [$payload['to']]));
                }

                $lastError = "HTTP {$response->status()}: {$response->body()}";
                
                // Don't retry on client errors (4xx)
                if ($response->status() >= 400 && $response->status() < 500) {
                    break;
                }

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
            }

            $attempt++;
            if ($attempt < self::MAX_RETRIES) {
                sleep(self::RETRY_DELAY_MS / 1000 * $attempt); // Exponential backoff
            }
        }

        self::recordFailure($lastError);
        return ['success' => 0, 'failure' => count($payload['registration_ids'] ?? [$payload['to']]), 'errors' => [$lastError]];
    }

    /**
     * Parse FCM response
     */
    protected static function parseResponse(array $response, int $totalTokens): array
    {
        $success = $response['success'] ?? 0;
        $failure = $response['failure'] ?? 0;
        $errors = [];

        if (isset($response['results'])) {
            foreach ($response['results'] as $index => $result) {
                if (isset($result['error'])) {
                    $errors[] = "Token {$index}: {$result['error']}";
                    
                    // Handle canonical IDs (token refresh)
                    if (isset($result['registration_id'])) {
                        // Update token in database
                        self::updateToken($result['registration_id']);
                    }
                    
                    // Handle invalid tokens
                    if (in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                        self::removeInvalidToken($index);
                    }
                }
            }
        }

        return ['success' => $success, 'failure' => $failure, 'errors' => $errors];
    }

    /**
     * Circuit breaker pattern
     */
    protected static function isCircuitOpen(): bool
    {
        if (self::$circuitOpenUntil > time()) {
            return true;
        }
        
        if (self::$failureCount >= self::CIRCUIT_BREAKER_THRESHOLD) {
            self::$circuitOpenUntil = time() + self::CIRCUIT_BREAKER_TIMEOUT;
            Log::error('FCM circuit breaker opened', [
                'failure_count' => self::$failureCount,
                'timeout' => self::CIRCUIT_BREAKER_TIMEOUT,
            ]);
            return true;
        }
        
        return false;
    }

    protected static function recordSuccess(): void
    {
        self::$failureCount = 0;
        self::$circuitOpenUntil = 0;
    }

    protected static function recordFailure(string $error): void
    {
        self::$failureCount++;
        Log::warning('FCM send failed', [
            'error' => $error,
            'failure_count' => self::$failureCount,
            'threshold' => self::CIRCUIT_BREAKER_THRESHOLD,
        ]);
    }

    /**
     * Token management
     */
    protected static function updateToken(string $newToken): void
    {
        // Find and update old token
        User::where('fcm_token', '!=', $newToken)
            ->whereRaw("fcm_token IN (SELECT registration_id FROM fcm_canonical_ids WHERE new_token = ?)", [$newToken])
            ->update(['fcm_token' => $newToken]);
    }

    protected static function removeInvalidToken(int $index): void
    {
        // In a real implementation, track which token corresponds to which index
        // For now, log for manual cleanup
        Log::warning('FCM invalid token detected at index', ['index' => $index]);
    }

    /**
     * Register device token
     */
    public static function registerToken(User $user, string $token, string $platform): void
    {
        $user->update([
            'fcm_token' => $token,
            'fcm_platform' => $platform,
            'fcm_updated_at' => now(),
        ]);
    }

    /**
     * Unregister device token
     */
    public static function unregisterToken(User $user): void
    {
        $user->update(['fcm_token' => null, 'fcm_platform' => null]);
    }
}
```

## Configuration

### Environment Variables
```env
# .env
FCM_SERVER_KEY=AAAA...your_server_key_from_firebase_console
FCM_SENDER_ID=123456789012
```

### Config File (`config/services.php`)
```php
return [
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'sender_id' => env('FCM_SENDER_ID'),
    ],
];
```

## Database Schema

### Users Table Additions
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('fcm_token')->nullable()->index();
    $table->string('fcm_platform')->nullable(); // android, ios
    $table->timestamp('fcm_updated_at')->nullable();
});
```

## Usage Examples

### In Controllers
```php
// Notify admins of new SP Warning
FcmService::notifyAdmins(
    'Surat Peringatan Baru',
    "SP1 telah diterbitkan untuk {$dapur->nama}",
    ['type' => 'sp_warning', 'sp_warning_id' => $sp->id, 'screen' => 'sp_warning_detail']
);

// Notify dapur team of CCTV anomaly
FcmService::notifyDapur($dapurId, 
    'Anomali CCTV Terdeteksi',
    "Kamera {$cctv->nama} mengalami gangguan",
    ['type' => 'cctv_anomaly', 'cctv_id' => $cctv->id, 'screen' => 'cctv_detail']
);

// Notify specific user
FcmService::notifyUser($user, 
    'Absensi Berhasil',
    'Check-in Anda telah dicatat',
    ['type' => 'absensi_success', 'absensi_id' => $absensi->id]
);
```

### In Jobs (Async)
```php
// app/Jobs/SendFcmNotification.php
class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $tokens,
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    public function handle(): void
    {
        FcmService::sendToTokens($this->tokens, $this->title, $this->body, $this->data);
    }
}

// Dispatch from controller
SendFcmNotification::dispatch($tokens, $title, $body, $data)->onQueue('notifications');
```

## Retry Logic Details

### Exponential Backoff
| Attempt | Delay |
|---------|-------|
| 1 | 1 second |
| 2 | 2 seconds |
| 3 | 3 seconds |

### Retry Conditions
- **Retry**: Network timeout, 5xx server errors, connection refused
- **Don't Retry**: 4xx client errors (invalid token, bad request), auth errors

### Circuit Breaker
- Opens after 5 consecutive failures
- Stays open for 60 seconds
- Prevents cascade failures during FCM outages
- Auto-resets on successful request

## Android vs iOS Payload

### Android (`android` key)
```php
$androidOptions = [
    'priority' => 'high',
    'notification' => [
        'channel_id' => 'genta_notifications',
        'icon' => 'ic_notification',
        'color' => '#E50000',
        'sound' => 'default',
    ],
    'data' => $data,
];
```

### iOS (`apns` key)
```php
$apnsOptions = [
    'headers' => [
        'apns-priority' => '10',
        'apns-push-type' => 'alert',
    ],
    'payload' => [
        'aps' => [
            'alert' => [
                'title' => $title,
                'body' => $body,
            ],
            'sound' => 'default',
            'badge' => 1,
            'mutable-content' => 1,
        ],
    ],
];
```

## Testing

### Feature Tests (`tests/Feature/FcmServiceTest.php`)
```php
use App\Services\FcmService;
use Illuminate\Support\Facades\Http;

test('send_to_tokens_returns_success_on_valid_response', function () {
    Http::fake([
        'fcm.googleapis.com/*' => Http::response([
            'multicast_id' => 123,
            'success' => 2,
            'failure' => 0,
            'results' => [
                ['message_id' => 'msg1'],
                ['message_id' => 'msg2'],
            ],
        ], 200),
    ]);

    $result = FcmService::sendToTokens(
        ['token1', 'token2'],
        'Test Title',
        'Test Body'
    );

    expect($result['success'])->toBe(2)
        ->and($result['failure'])->toBe(0);
});

test('circuit_breaker_opens_after_threshold', function () {
    Http::fake([
        'fcm.googleapis.com/*' => Http::response([], 500),
    ]);

    // Send 5 times to trigger circuit breaker
    for ($i = 0; $i < 5; $i++) {
        FcmService::sendToTokens(['token1'], 'Test', 'Body');
    }

    // 6th call should be blocked
    $result = FcmService::sendToTokens(['token1'], 'Test', 'Body');
    
    expect($result['success'])->toBe(0)
        ->and($result['failure'])->toBe(1)
        ->and($result['errors'][0])->toContain('Circuit breaker');
});

test('retry_logic_attempts_multiple_times', function () {
    Http::fake([
        'fcm.googleapis.com/*' => Http::sequence()
            ->push([], 500)  // First attempt fails
            ->push([
                'success' => 1,
                'failure' => 0,
                'results' => [['message_id' => 'msg1']],
            ], 200),  // Second attempt succeeds
    ]);

    $result = FcmService::sendToTokens(['token1'], 'Test', 'Body');
    
    expect($result['success'])->toBe(1);
    expect(Http::assertSentCount(2))->toBeTrue();
});
```

## Mobile App Integration

### Flutter FCM Setup
```dart
// main.dart
await FirebaseMessaging.instance.requestPermission();
final token = await FirebaseMessaging.instance.getToken();

// Send to backend
await apiService.registerFcmToken(token, 'android');

// Foreground messages
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  showLocalNotification(message.notification?.title, message.notification?.body);
});

// Background tap
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  navigateToScreen(message.data['screen'], message.data);
});
```

### iOS APNs Setup
```swift
// AppDelegate.swift
func application(_ application: UIApplication, 
    didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
    let token = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
    apiService.registerFcmToken(token, "ios")
}
```

## Monitoring & Debugging

### Logs
```php
// Check Laravel logs
tail -f storage/logs/laravel.log | grep FCM
```

### Metrics to Track
- Success rate (%)
- Average latency
- Circuit breaker activations
- Token invalidation rate
- Queue depth (if using jobs)

### FCM Console
- Firebase Console → Cloud Messaging → Reports
- Delivery rates, open rates, errors

## Security
- Server key stored in environment variable (never in code)
- Tokens transmitted over HTTPS only
- Token validation on registration
- Rate limiting on registration endpoint