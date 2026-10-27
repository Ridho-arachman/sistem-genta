# Dapur (Kitchen) Management Documentation

## Overview
Complete CRUD system for managing MBG (Makan Bergizi Gratis) kitchen locations with status tracking, progress monitoring, and CCTV association.

## Database Schema

### `dapurs` Table
```php
Schema::create('dapurs', function (Blueprint $table) {
    $table->id();
    $table->string('nama');                    // Kitchen name
    $table->text('lokasi')->nullable();        // Address/location
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->enum('status', [
        'operasional', 
        'perlu_perhatian', 
        'non_operasional'
    ])->default('operasional');
    $table->integer('progress')->default(0);   // 0-100%
    $table->foreignId('user_id')->nullable()   // Assigned pengawas
        ->constrained()->nullOnDelete();
    $table->timestamps();
});
```

### Relationships
```php
// Dapur.php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function cctvs(): HasMany
{
    return $this->hasMany(Cctv::class);
}

public function absensis(): HasMany
{
    return $this->hasMany(Absensi::class);
}

public function getCctvsCountAttribute(): int
{
    return $this->cctvs()->count();
}

public function getCctvsOnlineAttribute(): int
{
    return $this->cctvs()->where('status', 'online')->count();
}
```

## Controllers

### Admin: `app/Http/Controllers/Admin/DapurController.php`
```php
class DapurController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Dapur::class);
        $dapurs = Dapur::with(['user', 'cctvs'])->latest()->paginate(15);
        return view('admin.dapurs.index', compact('dapurs'));
    }

    public function create()
    {
        $this->authorize('create', Dapur::class);
        $pengawas = User::role('pengawas')->get();
        return view('admin.dapurs.create', compact('pengawas'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Dapur::class);
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:operasional,perlu_perhatian,non_operasional',
            'progress' => 'nullable|integer|min:0|max:100',
            'user_id' => 'nullable|exists:users,id',
        ]);
        Dapur::create($data);
        return redirect()->route('admin.dapurs.index')->with('success', 'Dapur created');
    }

    public function show(Dapur $dapur)
    {
        $this->authorize('view', $dapur);
        $dapur->load(['user', 'cctvs', 'absensis' => fn($q) => $q->latest()->limit(10)]);
        return view('admin.dapurs.show', compact('dapur'));
    }

    public function edit(Dapur $dapur)
    {
        $this->authorize('update', $dapur);
        $pengawas = User::role('pengawas')->get();
        return view('admin.dapurs.edit', compact('dapur', 'pengawas'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        $this->authorize('update', $dapur);
        $data = $request->validate([/* same as store */]);
        $dapur->update($data);
        return redirect()->route('admin.dapurs.index')->with('success', 'Dapur updated');
    }

    public function destroy(Dapur $dapur)
    {
        $this->authorize('delete', $dapur);
        $dapur->delete();
        return redirect()->route('admin.dapurs.index')->with('success', 'Dapur deleted');
    }
}
```

### Pengawas: `app/Http/Controllers/Pengawas/DapurController.php`
```php
class DapurController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Dapur::class);
        $dapurs = auth()->user()->dapur ? 
            Dapur::where('id', auth()->user()->dapur_id)->get() : 
            collect();
        return view('pengawas.dapurs.index', compact('dapurs'));
    }
}
```

## Routes

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('dapurs', DapurController::class);
});
```

### Pengawas Routes
```php
Route::prefix('pengawas')->name('pengawas.')->middleware(['auth', 'role:pengawas'])->group(function () {
    Route::resource('dapurs', DapurController::class)->only(['index', 'show']);
});
```

### Manajerial Routes
```php
Route::prefix('manajerial')->name('manajerial.')->middleware(['auth', 'role:manajerial'])->group(function () {
    Route::resource('dapurs', DapurController::class)->only(['index', 'show']);
});
```

## Views

### Index Page (`resources/views/admin/dapurs/index.blade.php`)
- DataTable with columns: Nama, Lokasi, Status, Progress, CCTV, Pengawas, Aksi
- Search/filter by name, status
- Pagination
- Create button

### Create/Edit Form (`resources/views/admin/dapurs/create.blade.php`)
- Nama (required)
- Lokasi (textarea)
- Koordinat (latitude/longitude) - for geofencing
- Status dropdown
- Progress slider (0-100%)
- Pengawas assignment dropdown

### Show Page (`resources/views/admin/dapurs/show.blade.php`)
- Detail card with all fields
- CCTV list table
- Recent absensi
- Action buttons (Edit, Delete)

## Status System

### Status Values
| Value | Label | Color | Description |
|-------|-------|-------|-------------|
| `operasional` | Operasional | Green | Fully functional |
| `perlu_perhatian` | Perlu Perhatian | Yellow | Needs attention |
| `non_operasional` | Non Operasional | Red | Not functioning |

### Badge Rendering
```blade
<span class="status-badge 
    @if($dapur->status === 'operasional') status-ok
    @elseif($dapur->status === 'perlu_perhatian') status-warn
    @else status-off @endif">
    {{ ucfirst(str_replace('_', ' ', $dapur->status)) }}
</span>
```

### Progress Bar
```blade
<div class="progress-bar">
    <i class="progress-fill 
        @if($dapur->progress >= 80) progress-ok
        @elseif($dapur->progress >= 50) progress-warn
        @else progress-off @endif" 
        style="width:{{ $dapur->progress }}%"></i>
</div>
<small>{{ $dapur->progress }}%</small>
```

## Dashboard Integration

### Stats Cards (Admin/Manajerial)
```php
// In DashboardController
$stats['total_dapur'] = Dapur::count();
$stats['dapur_operasional'] = Dapur::where('status', 'operasional')->count();
$stats['dapur_perlu_perhatian'] = Dapur::where('status', 'perlu_perhatian')->count();
```

### Kitchen Cards (All Dashboards)
```blade
@forelse ($dapurs as $dapur)
<div class="kitchen-card">
    <div class="kitchen-card-content">
        <div class="kitchen-name">{{ $dapur->nama }}</div>
        <div class="kitchen-meta">{{ $dapur->lokasi ?? '-' }}</div>
        <div class="progress-bar">
            <i class="progress-fill progress-{{ $dapur->status === 'operasional' ? 'ok' : ($dapur->status === 'perlu_perhatian' ? 'warn' : 'off') }}" 
               style="width:{{ $dapur->progress ?? 0 }}%"></i>
        </div>
        <div class="small-label">
            <span>{{ $dapur->cctvs_count ?? 0 }} kamera</span>
            <span>{{ $dapur->progress ?? 0 }}%</span>
        </div>
    </div>
    <span class="status-badge @if($dapur->status === 'operasional') status-ok @elseif($dapur->status === 'perlu_perhatian') status-warn @else status-off @endif">
        {{ ucfirst(str_replace('_', ' ', $dapur->status)) }}
    </span>
</div>
@empty
<div class="text-center py-8 text-[var(--color-text-muted)]">
    Belum ada dapur terdaftar
</div>
@endforelse
```

## API Endpoints (Mobile)

### GET `/api/dapurs`
```json
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
            "cctvs_online": 4
        }
    ]
}
```

### GET `/api/dapurs/{id}`
```json
{
    "data": {
        "id": 1,
        "nama": "Dapur MBG Serang 01",
        "lokasi": "Jl. Raya Serang No. 123",
        "latitude": -6.123456,
        "longitude": 106.123456,
        "status": "operasional",
        "progress": 92,
        "pengawas": {
            "id": 5,
            "name": "Siti Rahma"
        },
        "cctvs": [...]
    }
}
```

## Geofencing Integration

### Coordinate Validation
```php
// In Dapur model
public function setLatitudeAttribute($value)
{
    $this->attributes['latitude'] = $value ? round($value, 8) : null;
}

public function setLongitudeAttribute($value)
{
    $this->attributes['longitude'] = $value ? round($value, 8) : null;
}
```

### Distance Calculation (GeolocationService)
```php
// Used for absensi geofencing
$distance = GeolocationService::calculateDistance(
    $userLat, $userLng,
    $dapur->latitude, $dapur->longitude
);

$isWithinRange = $distance <= 100; // 100 meters default
```

## Testing

### Feature Tests (`tests/Feature/DapurTest.php`)
```php
/** @test */
public function admin_can_create_dapur()
{
    $user = User::factory()->admin()->create();
    $data = ['nama' => 'Test Dapur', 'status' => 'operasional'];
    
    $this->actingAs($user)
        ->post(route('admin.dapurs.store'), $data)
        ->assertRedirect(route('admin.dapurs.index'));
    
    $this->assertDatabaseHas('dapurs', ['nama' => 'Test Dapur']);
}

/** @test */
public function pengawas_cannot_create_dapur()
{
    $user = User::factory()->pengawas()->create();
    
    $this->actingAs($user)
        ->post(route('admin.dapurs.store'), ['nama' => 'Test'])
        ->assertForbidden();
}

/** @test */
public function pengawas_can_only_view_own_dapur()
{
    $pengawas = User::factory()->pengawas()->create();
    $ownDapur = Dapur::factory()->create(['user_id' => $pengawas->id]);
    $otherDapur = Dapur::factory()->create();
    
    $this->actingAs($pengawas)
        ->get(route('admin.dapurs.show', $ownDapur))
        ->assertOk();
    
    $this->actingAs($pengawas)
        ->get(route('admin.dapurs.show', $otherDapur))
        ->assertForbidden();
}
```

## Unique Constraints
```php
// Migration: 2026_10_25_194714_add_unique_constraint_to_absensi_table.php
// Note: Dapur names should be unique per region
// Consider adding: $table->unique(['nama', 'kabupaten_kota']);
```

## Performance Considerations
- Eager load relationships: `with(['user', 'cctvs'])`
- Paginate large datasets (15 per page)
- Index on `status`, `user_id` columns
- Cache dashboard stats for 5 minutes