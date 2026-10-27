# Absensi (Attendance) System Documentation

## Overview
GPS-based attendance system with geofencing validation, photo capture, and real-time tracking for pengawas (supervisors) at MBG kitchen locations.

## Database Schema

### `absensis` Table
```php
Schema::create('absensis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();      // Pengawas
    $table->foreignId('dapur_id')->constrained()->cascadeOnDelete();     // Kitchen
    $table->enum('status', [
        'hadir',      // Present
        'izin',       // Permitted leave
        'cuti',       // Annual leave
        'sakit',      // Sick leave
        'alpha'       // Absent without notice
    ])->default('hadir');
    $table->timestamp('check_in_at')->nullable();
    $table->timestamp('check_out_at')->nullable();
    $table->decimal('check_in_lat', 10, 8)->nullable();
    $table->decimal('check_in_lng', 11, 8)->nullable();
    $table->decimal('check_out_lat', 10, 8)->nullable();
    $table->decimal('check_out_lng', 11, 8)->nullable();
    $table->decimal('check_in_accuracy', 8, 2)->nullable();  // GPS accuracy in meters
    $table->decimal('check_out_accuracy', 8, 2)->nullable();
    $table->string('check_in_photo')->nullable();   // Base64 or path
    $table->string('check_out_photo')->nullable();
    $table->boolean('is_within_geofence')->default(false);
    $table->decimal('distance_from_dapur', 10, 2)->nullable();  // Meters
    $table->text('notes')->nullable();
    $table->timestamps();
    
    // Unique constraint: one active check-in per user per day per dapur
    $table->unique(['user_id', 'dapur_id', 'check_in_at'], 'absensi_unique_daily');
});
```

### Unique Constraint Migration
```php
// database/migrations/2026_10_25_194714_add_unique_constraint_to_absensi_table.php
Schema::table('absensis', function (Blueprint $table) {
    $table->unique(['user_id', 'dapur_id', 'check_in_at'], 'absensi_unique_daily');
});
```

## Models

### Absensi Model (`app/Models/Absensi.php`)
```php
class Absensi extends Model
{
    protected $fillable = [
        'user_id', 'dapur_id', 'status',
        'check_in_at', 'check_out_at',
        'check_in_lat', 'check_in_lng',
        'check_out_lat', 'check_out_lng',
        'check_in_accuracy', 'check_out_accuracy',
        'check_in_photo', 'check_out_photo',
        'is_within_geofence', 'distance_from_dapur', 'notes'
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_lat' => 'decimal:8',
        'check_in_lng' => 'decimal:8',
        'check_out_lat' => 'decimal:8',
        'check_out_lng' => 'decimal:8',
        'check_in_accuracy' => 'decimal:2',
        'check_out_accuracy' => 'decimal:2',
        'is_within_geofence' => 'boolean',
        'distance_from_dapur' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'hadir' => 'status-ok',
            'izin' => 'status-warn',
            'cuti' => 'status-info',
            'sakit' => 'status-info',
            'alpha' => 'status-off',
            default => 'status-gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'cuti' => 'Cuti',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => 'Unknown',
        };
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->check_in_at || !$this->check_out_at) return null;
        $diff = $this->check_in_at->diff($this->check_out_at);
        return $diff->format('%h jam %i menit');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in_at', now()->month)
            ->whereYear('check_in_at', now()->year);
    }
}
```

## Controllers

### Admin: `app/Http/Controllers/Admin/AbsensiController.php`
```php
class AbsensiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Absensi::class);
        $absensis = Absensi::with(['user', 'dapur'])
            ->latest('check_in_at')
            ->paginate(20);
        return view('admin.absensi.index', compact('absensis'));
    }

    public function show(Absensi $absensi)
    {
        $this->authorize('view', $absensi);
        $absensi->load(['user', 'dapur']);
        return view('admin.absensi.show', compact('absensi'));
    }
}
```

### Pengawas: `app/Http/Controllers/Pengawas/AbsensiController.php`
```php
class AbsensiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Absensi::class);
        $user = auth()->user();
        $absensis = Absensi::where('user_id', $user->id)
            ->with('dapur')
            ->latest('check_in_at')
            ->paginate(15);
        return view('pengawas.absensi.index', compact('absensis'));
    }

    public function create()
    {
        $this->authorize('create', Absensi::class);
        $user = auth()->user();
        $dapur = $user->dapur;
        
        if (!$dapur) {
            return redirect()->route('pengawas.absensi.index')
                ->with('error', 'Anda tidak memiliki dapur yang ditugaskan.');
        }
        
        // Check if already checked in today
        $existing = Absensi::where('user_id', $user->id)
            ->where('dapur_id', $dapur->id)
            ->whereDate('check_in_at', today())
            ->first();
            
        if ($existing && !$existing->check_out_at) {
            return redirect()->route('pengawas.absensi.index')
                ->with('info', 'Anda sudah check-in hari ini. Silakan check-out.');
        }
        
        return view('pengawas.absensi.create', compact('dapur'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Absensi::class);
        $user = auth()->user();
        $dapur = $user->dapur;
        
        if (!$dapur) {
            return back()->with('error', 'Tidak ada dapur yang ditugaskan.');
        }

        $data = $request->validate([
            'status' => 'required|in:hadir,izin,cuti,sakit',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',  // Base64 encoded
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate distance using GeolocationService
        $distance = GeolocationService::calculateDistance(
            $data['latitude'], $data['longitude'],
            $dapur->latitude, $dapur->longitude
        );

        $isWithinGeofence = $distance <= 100; // 100 meters default radius

        // For non-hadir status, geofence not required
        if ($data['status'] !== 'hadir') {
            $isWithinGeofence = true;
            $distance = 0;
        }

        $absensi = Absensi::create([
            'user_id' => $user->id,
            'dapur_id' => $dapur->id,
            'status' => $data['status'],
            'check_in_at' => now(),
            'check_in_lat' => $data['latitude'],
            'check_in_lng' => $data['longitude'],
            'check_in_accuracy' => $data['accuracy'],
            'check_in_photo' => $data['photo'],
            'is_within_geofence' => $isWithinGeofence,
            'distance_from_dapur' => $distance,
            'notes' => $data['notes'],
        ]);

        // Notify admin if outside geofence
        if (!$isWithinGeofence && $data['status'] === 'hadir') {
            FcmService::notifyAdmins(
                'Absensi Di Luar Area',
                "{$user->name} check-in di luar area geofence ({$distance}m dari dapur)"
            );
        }

        return redirect()->route('pengawas.absensi.index')
            ->with('success', 'Check-in berhasil dicatat');
    }

    public function checkOut(Request $request, Absensi $absensi)
    {
        $this->authorize('update', $absensi);
        
        if ($absensi->check_out_at) {
            return back()->with('error', 'Sudah check-out sebelumnya.');
        }

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',
        ]);

        $dapur = $absensi->dapur;
        $distance = GeolocationService::calculateDistance(
            $data['latitude'], $data['longitude'],
            $dapur->latitude, $dapur->longitude
        );

        $absensi->update([
            'check_out_at' => now(),
            'check_out_lat' => $data['latitude'],
            'check_out_lng' => $data['longitude'],
            'check_out_accuracy' => $data['accuracy'],
            'check_out_photo' => $data['photo'],
        ]);

        return redirect()->route('pengawas.absensi.index')
            ->with('success', 'Check-out berhasil dicatat');
    }
}
```

## Geolocation Service

### `app/Services/GeolocationService.php`
```php
class GeolocationService
{
    const EARTH_RADIUS_KM = 6371;
    const DEFAULT_GEOFENCE_RADIUS_METERS = 100;

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public static function calculateDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        // Convert to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        
        $a = sin($dLat / 2) ** 2 + 
             cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distanceKm = self::EARTH_RADIUS_KM * $c;
        return $distanceKm * 1000; // Return in meters
    }

    /**
     * Check if coordinate is within geofence radius
     */
    public static function isWithinGeofence(
        float $userLat, float $userLng,
        float $targetLat, float $targetLng,
        int $radiusMeters = self::DEFAULT_GEOFENCE_RADIUS_METERS
    ): bool {
        $distance = self::calculateDistance($userLat, $userLng, $targetLat, $targetLng);
        return $distance <= $radiusMeters;
    }

    /**
     * Validate coordinate format
     */
    public static function validateCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    /**
     * Get geofence radius for dapur (configurable per dapur)
     */
    public static function getGeofenceRadius(Dapur $dapur): int
    {
        return $dapur->geofence_radius ?? self::DEFAULT_GEOFENCE_RADIUS_METERS;
    }
}
```

## Routes

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('absensi', AbsensiController::class)->only(['index', 'show']);
});
```

### Pengawas Routes
```php
Route::prefix('pengawas')->name('pengawas.')->middleware(['auth', 'role:pengawas'])->group(function () {
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::post('absensi/{absensi}/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.check-out');
});
```

### Manajerial Routes
```php
Route::prefix('manajerial')->name('manajerial.')->middleware(['auth', 'role:manajerial'])->group(function () {
    Route::resource('absensi', AbsensiController::class)->only(['index', 'show']);
});
```

## Views

### Pengawas Check-in Form (`resources/views/pengawas/absensi/create.blade.php`)
```blade
<form action="{{ route('pengawas.absensi.store') }}" method="POST" x-data="absensiForm()">
    @csrf
    
    <!-- Status Selection -->
    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Status Kehadiran</label>
        <div class="grid grid-cols-2 gap-3" x-data="{ status: 'hadir' }">
            @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'cuti' => 'Cuti', 'sakit' => 'Sakit'] as $value => $label)
                <label class="relative cursor-pointer">
                    <input type="radio" name="status" value="{{ $value }}" 
                           :checked="status === '{{ $value }}'" 
                           @click="status = '{{ $value }}'"
                           class="sr-only peer">
                    <div class="p-4 border-2 rounded-lg text-center 
                        peer-checked:border-[var(--color-primary)] 
                        peer-checked:bg-[var(--color-primary)]/5
                        border-[var(--color-border)]
                        hover:border-[var(--color-primary)]/50
                        transition-colors">
                        {{ $label }}
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <!-- GPS Location (auto-captured) -->
    <div class="mb-6" x-show="status === 'hadir'">
        <label class="block text-sm font-medium mb-2">Lokasi GPS</label>
        <div class="space-y-2">
            <div class="input-field bg-[var(--color-surface)]" readonly>
                <span x-text="gpsStatus"></span>
            </div>
            <button type="button" @click="getLocation()" 
                    class="btn-secondary w-full" :disabled="gettingLocation">
                <span x-show="!gettingLocation">📍 Ambil Lokasi GPS</span>
                <span x-show="gettingLocation">Mengambil lokasi...</span>
            </button>
            <input type="hidden" name="latitude" x-model="latitude">
            <input type="hidden" name="longitude" x-model="longitude">
            <input type="hidden" name="accuracy" x-model="accuracy">
        </div>
    </div>

    <!-- Photo Capture -->
    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Foto Bukti (Opsional)</label>
        <div class="space-y-2">
            <video id="camera" class="w-full rounded-lg bg-black" autoplay playsinline style="display:none"></video>
            <canvas id="canvas" class="w-full rounded-lg" style="display:none"></canvas>
            <img id="preview" class="w-full rounded-lg" style="display:none">
            <div class="flex gap-2">
                <button type="button" @click="startCamera()" class="btn-secondary">📷 Buka Kamera</button>
                <button type="button" @click="capturePhoto()" class="btn-primary" style="display:none" x-ref="captureBtn">📸 Ambil Foto</button>
                <button type="button" @click="retakePhoto()" class="btn-light" style="display:none" x-ref="retakeBtn">🔄 Ulangi</button>
            </div>
            <input type="hidden" name="photo" x-model="photoBase64">
        </div>
    </div>

    <!-- Notes -->
    <div class="mb-6">
        <label for="notes" class="block text-sm font-medium mb-2">Catatan</label>
        <textarea name="notes" id="notes" rows="3" class="input-field" 
                  placeholder="Catatan tambahan..."></textarea>
    </div>

    <!-- Submit -->
    <button type="submit" class="btn-primary w-full" :disabled="submitting">
        <span x-show="!submitting">✅ Check-in</span>
        <span x-show="submitting" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">...</svg>
            Memproses...
        </span>
    </button>
</form>

<script>
function absensiForm() {
    return {
        status: 'hadir',
        latitude: null,
        longitude: null,
        accuracy: null,
        photoBase64: null,
        gettingLocation: false,
        gpsStatus: 'Belum mengambil lokasi',
        submitting: false,
        
        async getLocation() {
            this.gettingLocation = true;
            this.gpsStatus = 'Mengambil lokasi...';
            
            try {
                const pos = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                });
                
                this.latitude = pos.coords.latitude;
                this.longitude = pos.coords.longitude;
                this.accuracy = pos.coords.accuracy;
                this.gpsStatus = `Lokasi didapat: ${this.latitude.toFixed(6)}, ${this.longitude.toFixed(6)} (Akurasi: ${this.accuracy}m)`;
            } catch (e) {
                this.gpsStatus = 'Gagal mengambil lokasi: ' + e.message;
            } finally {
                this.gettingLocation = false;
            }
        },
        
        async startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                const video = document.getElementById('camera');
                video.srcObject = stream;
                video.style.display = 'block';
                this.$refs.captureBtn.style.display = 'inline-flex';
            } catch (e) {
                alert('Tidak bisa mengakses kamera: ' + e.message);
            }
        },
        
        capturePhoto() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            this.photoBase64 = canvas.toDataURL('image/jpeg', 0.8);
            
            preview.src = this.photoBase64;
            preview.style.display = 'block';
            video.style.display = 'none';
            this.$refs.captureBtn.style.display = 'none';
            this.$refs.retakeBtn.style.display = 'inline-flex';
            
            // Stop camera stream
            video.srcObject.getTracks().forEach(track => track.stop());
        },
        
        retakePhoto() {
            this.photoBase64 = null;
            document.getElementById('preview').style.display = 'none';
            this.$refs.retakeBtn.style.display = 'none';
            this.startCamera();
        }
    }
}
</script>
```

## Dashboard Integration

### Stats Card
```php
$stats['absensi_hari_ini'] = Absensi::whereDate('check_in_at', today())->count();
$stats['absensi_hadir'] = Absensi::whereDate('check_in_at', today())
    ->where('status', 'hadir')->count();
$stats['absensi_izin'] = Absensi::whereDate('check_in_at', today())
    ->where('status', 'izin')->count();
```

### Activity Feed
```blade
<div class="activity-row flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
    <div class="activity-icon w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">✓</div>
    <div class="flex-1">
        <strong>Absensi check-in baru</strong>
        <p class="text-sm text-[var(--color-text-muted)]">
            {{ $absensi->user->name }} · {{ $absensi->dapur->nama }}
        </p>
    </div>
    <time class="text-sm text-[var(--color-text-muted)]">
        {{ $absensi->check_in_at->format('H:i') }}
    </time>
</div>
```

## Mobile API

### POST `/api/absensi/check-in`
```json
{
    "dapur_id": 1,
    "status": "hadir",
    "latitude": -6.123456,
    "longitude": 106.123456,
    "accuracy": 5.2,
    "photo": "base64_encoded_image",
    "notes": "Check-in pagi"
}
```

Response:
```json
{
    "data": {
        "id": 10,
        "user_id": 3,
        "dapur_id": 1,
        "status": "hadir",
        "check_in_at": "2026-10-26T07:30:00Z",
        "is_within_geofence": true,
        "distance_from_dapur": 15.5
    },
    "message": "Check-in berhasil"
}
```

### POST `/api/absensi/{id}/check-out`
```json
{
    "latitude": -6.123456,
    "longitude": 106.123456,
    "accuracy": 4.8,
    "photo": "base64_encoded_image"
}
```

## Testing

### Feature Tests (`tests/Feature/AbsensiTest.php`)
```php
/** @test */
public function pengawas_can_check_in_within_geofence()
{
    $user = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create([
        'user_id' => $user->id,
        'latitude' => -6.123456,
        'longitude' => 106.123456,
    ]);
    
    // Within 50 meters
    $data = [
        'status' => 'hadir',
        'latitude' => -6.123500,
        'longitude' => 106.123500,
    ];
    
    $this->actingAs($user)
        ->post(route('pengawas.absensi.store'), $data)
        ->assertRedirect();
    
    $absensi = Absensi::latest()->first();
    $this->assertTrue($absensi->is_within_geofence);
    $this->assertLessThan(100, $absensi->distance_from_dapur);
}

/** @test */
public function pengawas_check_in_outside_geofence_flagged()
{
    $user = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create([
        'user_id' => $user->id,
        'latitude' => -6.123456,
        'longitude' => 106.123456,
    ]);
    
    // 500 meters away
    $data = [
        'status' => 'hadir',
        'latitude' => -6.128000,
        'longitude' => 106.128000,
    ];
    
    $this->actingAs($user)
        ->post(route('pengawas.absensi.store'), $data)
        ->assertRedirect();
    
    $absensi = Absensi::latest()->first();
    $this->assertFalse($absensi->is_within_geofence);
    $this->assertGreaterThan(100, $absensi->distance_from_dapur);
}

/** @test */
public function unique_constraint_prevents_duplicate_check_in()
{
    $user = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create(['user_id' => $user->id]);
    
    Absensi::factory()->create([
        'user_id' => $user->id,
        'dapur_id' => $dapur->id,
        'check_in_at' => now(),
    ]);
    
    $data = ['status' => 'hadir', 'latitude' => 0, 'longitude' => 0];
    
    $this->actingAs($user)
        ->post(route('pengawas.absensi.store'), $data)
        ->assertSessionHas('error');
}
```

## Geofence Configuration

### Per-Dapur Radius (Future Enhancement)
```php
// Add to dapurs table
$table->integer('geofence_radius')->default(100); // meters

// In GeolocationService
public static function getGeofenceRadius(Dapur $dapur): int
{
    return $dapur->geofence_radius ?? self::DEFAULT_GEOFENCE_RADIUS_METERS;
}
```

## Reports

### Daily Absensi Report
```php
// Admin controller method
public function report(Request $request)
{
    $date = $request->date ?? today();
    $absensis = Absensi::whereDate('check_in_at', $date)
        ->with(['user', 'dapur'])
        ->get()
        ->groupBy('dapur_id');
    
    return view('admin.absensi.report', compact('absensis', 'date'));
}
```

### Monthly Summary
```php
Absensi::selectRaw('user_id, status, count(*) as count')
    ->whereMonth('check_in_at', now()->month)
    ->groupBy('user_id', 'status')
    ->get();
```

## Security & Privacy
- GPS coordinates encrypted at rest (future)
- Photo storage: Base64 in DB or S3 (configurable)
- Accuracy threshold: Reject if > 50m accuracy
- Rate limiting: Max 2 check-ins per hour per user