# Mobile API Documentation

## Overview
RESTful API for mobile application (Android/iOS) used by pengawas (supervisors) for field operations.

## Base URL
```
Production: https://api.genta-banten.go.id/api
Staging: https://staging-api.genta-banten.go.id/api
Local: http://localhost:8000/api
```

## Authentication

### Sanctum Token Authentication
```bash
# Login to get token
POST /api/login
Content-Type: application/json

{
    "email": "pengawas@example.com",
    "password": "password123",
    "device_name": "mobile-app"
}

# Response
{
    "token": "1|abc123...",
    "user": {
        "id": 3,
        "name": "Siti Rahma",
        "email": "pengawas@example.com",
        "role": "pengawas",
        "dapur_id": 1
    }
}

# Use token in subsequent requests
Authorization: Bearer 1|abc123...
```

### Token Management
- Tokens expire after 30 days of inactivity
- Mobile app should store token securely (Keychain/Keystore)
- Refresh token by re-authenticating

## Endpoints

### Authentication

#### POST `/api/login`
```json
// Request
{
    "email": "string",
    "password": "string",
    "device_name": "string"  // optional, default: "mobile"
}

// Success Response (200)
{
    "token": "string",
    "user": { "id", "name", "email", "role", "dapur_id" }
}

// Error Response (401)
{
    "message": "Invalid credentials"
}
```

#### POST `/api/logout`
```bash
Authorization: Bearer <token>

// Response (200)
{
    "message": "Logged out successfully"
}
```

#### GET `/api/user`
```bash
Authorization: Bearer <token>

// Response (200)
{
    "id": 3,
    "name": "Siti Rahma",
    "email": "pengawas@example.com",
    "role": "pengawas",
    "dapur": { "id": 1, "nama": "Dapur MBG Serang 01" }
}
```

### Dapur (Kitchen)

#### GET `/api/dapurs`
```bash
Authorization: Bearer <token>

// Query params
?page=1&per_page=15&search=serang&status=operasional

// Response (200)
{
    "data": [
        {
            "id": 1,
            "nama": "Dapur MBG Serang 01",
            "lokasi": "Jl. Raya Serang No. 123",
            "latitude": -6.123456,
            "longitude": 106.123456,
            "status": "operasional",
            "progress": 92,
            "cctvs_count": 4,
            "cctvs_online": 4,
            "pengawas": { "id": 3, "name": "Siti Rahma" }
        }
    ],
    "meta": { "current_page": 1, "last_page": 1, "total": 4 }
}
```

#### GET `/api/dapurs/{id}`
```bash
Authorization: Bearer <token>

// Response (200)
{
    "data": {
        "id": 1,
        "nama": "Dapur MBG Serang 01",
        "lokasi": "Jl. Raya Serang No. 123",
        "latitude": -6.123456,
        "longitude": 106.123456,
        "status": "operasional",
        "progress": 92,
        "pengawas": { "id": 3, "name": "Siti Rahma" },
        "cctvs": [
            {
                "id": 1,
                "nama": "CAM-01",
                "ip_address": "192.168.1.100",
                "status": "online",
                "location_type": "persiapan",
                "last_online_at": "2026-10-26T10:30:00Z"
            }
        ]
    }
}
```

### Absensi (Attendance)

#### POST `/api/absensi/check-in`
```bash
Authorization: Bearer <token>
Content-Type: application/json

// Request
{
    "dapur_id": 1,
    "status": "hadir",  // hadir, izin, cuti, sakit
    "latitude": -6.123500,
    "longitude": 106.123500,
    "accuracy": 5.2,
    "photo": "base64_encoded_jpeg",  // optional
    "notes": "Check-in pagi"  // optional
}

// Success Response (201)
{
    "data": {
        "id": 10,
        "user_id": 3,
        "dapur_id": 1,
        "status": "hadir",
        "check_in_at": "2026-10-26T07:30:00Z",
        "check_in_lat": -6.123500,
        "check_in_lng": 106.123500,
        "check_in_accuracy": 5.2,
        "is_within_geofence": true,
        "distance_from_dapur": 15.5
    },
    "message": "Check-in berhasil"
}

// Error: Already checked in (409)
{
    "message": "Anda sudah check-in hari ini",
    "existing_absensi": { "id": 10, "check_in_at": "..." }
}

// Error: Outside geofence (422) - still creates record but flagged
{
    "message": "Check-in di luar area geofence (500m dari dapur)",
    "data": { ..., "is_within_geofence": false, "distance_from_dapur": 500 }
}
```

#### POST `/api/absensi/{id}/check-out`
```bash
Authorization: Bearer <token>
Content-Type: application/json

// Request
{
    "latitude": -6.123500,
    "longitude": 106.123500,
    "accuracy": 4.8,
    "photo": "base64_encoded_jpeg"  // optional
}

// Response (200)
{
    "data": {
        "id": 10,
        "check_out_at": "2026-10-26T15:30:00Z",
        "check_out_lat": -6.123500,
        "check_out_lng": 106.123500,
        "check_out_accuracy": 4.8,
        "duration": "8 jam 0 menit"
    },
    "message": "Check-out berhasil"
}
```

#### GET `/api/absensi`
```bash
Authorization: Bearer <token>

// Query params
?page=1&per_page=15&date=2026-10-26&status=hadir

// Response (200)
{
    "data": [
        {
            "id": 10,
            "dapur_id": 1,
            "dapur_nama": "Dapur MBG Serang 01",
            "status": "hadir",
            "check_in_at": "2026-10-26T07:30:00Z",
            "check_out_at": "2026-10-26T15:30:00Z",
            "is_within_geofence": true,
            "distance_from_dapur": 15.5
        }
    ],
    "meta": { ... }
}
```

#### GET `/api/absensi/today`
```bash
Authorization: Bearer <token>

// Response (200)
{
    "data": {
        "id": 10,
        "dapur_id": 1,
        "dapur_nama": "Dapur MBG Serang 01",
        "status": "hadir",
        "check_in_at": "2026-10-26T07:30:00Z",
        "check_out_at": null,
        "is_within_geofence": true,
        "can_check_out": true
    }
}
```

### CCTV

#### GET `/api/cctvs`
```bash
Authorization: Bearer <token>

// Query params
?dapur_id=1&status=online

// Response (200)
{
    "data": [
        {
            "id": 1,
            "dapur_id": 1,
            "dapur_nama": "Dapur MBG Serang 01",
            "nama": "CAM-01",
            "ip_address": "192.168.1.100",
            "rtsp_url": "rtsp://192.168.1.100:554/stream1",
            "status": "online",
            "location_type": "persiapan",
            "last_online_at": "2026-10-26T10:30:00Z"
        }
    ]
}
```

#### GET `/api/cctvs/{id}/stream`
```bash
Authorization: Bearer <token>

// Response (200) - Returns stream info for player
{
    "data": {
        "rtsp_url": "rtsp://192.168.1.100:554/stream1",
        "hls_url": "https://stream.genta-banten.go.id/cam1/playlist.m3u8",  // if transcoded
        "status": "online"
    }
}
```

### CCTV Anomaly Reports

#### POST `/api/cctv-anomalies`
```bash
Authorization: Bearer <token>
Content-Type: application/json

// Request
{
    "cctv_id": 1,
    "type": "offline",  // offline, blurry, frozen, no_signal, tampered, other
    "description": "Kamera tidak bisa diakses sejak 09:00",
    "photo": "base64_encoded_jpeg"  // optional
}

// Response (201)
{
    "data": {
        "id": 5,
        "cctv_id": 1,
        "reported_by": 3,
        "type": "offline",
        "description": "Kamera tidak bisa diakses sejak 09:00",
        "status": "open",
        "created_at": "2026-10-26T10:35:00Z"
    },
    "message": "Laporan anomali berhasil dikirim"
}
```

#### GET `/api/cctv-anomalies`
```bash
Authorization: Bearer <token>

// Query params
?cctv_id=1&status=open

// Response (200)
{
    "data": [
        {
            "id": 5,
            "cctv_id": 1,
            "cctv_nama": "CAM-01",
            "dapur_nama": "Dapur MBG Serang 01",
            "type": "offline",
            "description": "Kamera tidak bisa diakses sejak 09:00",
            "status": "open",
            "reported_by": 3,
            "reporter_name": "Siti Rahma",
            "created_at": "2026-10-26T10:35:00Z"
        }
    ]
}
```

### Notifications

#### GET `/api/notifications`
```bash
Authorization: Bearer <token>

// Query params
?unread_only=true&page=1

// Response (200)
{
    "data": [
        {
            "id": 1,
            "type": "App\\Notifications\\SpWarningIssued",
            "notifiable_type": "App\\Models\\User",
            "notifiable_id": 3,
            "data": {
                "title": "Surat Peringatan Baru",
                "body": "SP1 telah diterbitkan untuk Dapur MBG Serang 01",
                "sp_warning_id": 5
            },
            "read_at": null,
            "created_at": "2026-10-26T10:00:00Z"
        }
    ]
}
```

#### POST `/api/notifications/{id}/read`
```bash
Authorization: Bearer <token>

// Response (200)
{
    "message": "Notification marked as read"
}
```

### Sync (Offline Support)

#### POST `/api/sync`
```bash
Authorization: Bearer <token>
Content-Type: application/json

// Request - Batch of operations performed offline
{
    "absensi": [
        {
            "local_id": "temp-123",
            "type": "check-in",
            "data": { "dapur_id": 1, "status": "hadir", "latitude": ..., "longitude": ... },
            "timestamp": "2026-10-26T07:30:00Z"
        }
    ],
    "cctv_anomalies": [
        {
            "local_id": "temp-456",
            "data": { "cctv_id": 1, "type": "offline", "description": "..." },
            "timestamp": "2026-10-26T09:00:00Z"
        }
    ]
}

// Response (200)
{
    "data": {
        "absensi": [
            { "local_id": "temp-123", "server_id": 10, "status": "synced" }
        ],
        "cctv_anomalies": [
            { "local_id": "temp-456", "server_id": 5, "status": "synced" }
        ],
        "conflicts": []  // If any conflicts detected
    }
}
```

## Error Responses

### Standard Error Format
```json
{
    "message": "Human readable error message",
    "errors": {           // Validation errors (422)
        "field": ["Error message"]
    },
    "code": "ERROR_CODE"  // Optional error code
}
```

### HTTP Status Codes
| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized (invalid/missing token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not Found |
| 409 | Conflict (duplicate) |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

## Rate Limiting
- 60 requests/minute for authenticated users
- 10 requests/minute for login attempts
- Exceeding returns 429 with `Retry-After` header

## Versioning
- Current version: v1 (implicit)
- Future versions: `/api/v2/...`
- Header: `Accept: application/vnd.genta.v1+json`

## Mobile App Integration

### Flutter/Dart Example
```dart
class ApiService {
  static const baseUrl = 'https://api.genta-banten.go.id/api';
  late Dio _dio;
  
  ApiService() {
    _dio = Dio(BaseOptions(baseUrl: baseUrl));
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        final token = storage.getString('auth_token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          // Token expired, redirect to login
          authService.logout();
        }
        handler.next(error);
      },
    ));
  }
  
  Future<AbsensiResponse> checkIn(CheckInRequest request) async {
    final response = await _dio.post('/absensi/check-in', data: request.toJson());
    return AbsensiResponse.fromJson(response.data);
  }
}
```

### React Native Example
```javascript
const api = axios.create({
  baseURL: 'https://api.genta-banten.go.id/api',
});

api.interceptors.request.use((config) => {
  const token = await AsyncStorage.getItem('auth_token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

export const checkIn = async (data) => {
  const response = await api.post('/absensi/check-in', data);
  return response.data;
};
```

## Push Notifications (FCM)

### Device Registration
```bash
POST /api/fcm/register
Authorization: Bearer <token>
Content-Type: application/json

{
    "fcm_token": "firebase_cloud_messaging_token",
    "platform": "android"  // or "ios"
}

// Response (200)
{ "message": "FCM token registered" }
```

### Notification Payload
```json
{
    "title": "Surat Peringatan Baru",
    "body": "SP1 diterbitkan untuk Dapur MBG Serang 01",
    "data": {
        "type": "sp_warning",
        "id": "5",
        "screen": "sp_warning_detail"
    }
}
```

## Testing

### API Tests (`tests/Feature/MobileApiTest.php`)
```php
/** @test */
public function pengawas_can_login_and_get_token()
{
    $user = User::factory()->pengawas()->create(['password' => bcrypt('password')]);
    
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'role']]);
}

/** @test */
public function pengawas_can_check_in_via_api()
{
    $user = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create(['user_id' => $user->id]);
    $token = $user->createToken('mobile')->plainTextToken;
    
    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/absensi/check-in', [
            'dapur_id' => $dapur->id,
            'status' => 'hadir',
            'latitude' => $dapur->latitude,
            'longitude' => $dapur->longitude,
        ]);
    
    $response->assertCreated()
        ->assertJsonPath('data.is_within_geofence', true);
}

/** @test */
public function unauthorized_request_rejected()
{
    $response = $this->getJson('/api/dapurs');
    $response->assertUnauthorized();
}
```

## Security Considerations
- All endpoints require authentication (except `/api/login`)
- Role-based access: pengawas only access own dapur data
- Rate limiting prevents abuse
- HTTPS enforced in production
- FCM tokens stored encrypted
- Input validation on all endpoints
- SQL injection prevention via Eloquent