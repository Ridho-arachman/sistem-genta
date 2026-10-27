# CCTV Monitoring System Documentation

## Overview
Complete CCTV device management with status tracking, anomaly detection, and monitoring capabilities.

## Database Schema

### `cctvs` Table
```php
Schema::create('cctvs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dapur_id')->constrained()->cascadeOnDelete();
    $table->string('nama');                    // Camera name (e.g., CAM-01)
    $table->string('ip_address')->nullable();  // IP address
    $table->string('rtsp_url')->nullable();    // Stream URL
    $table->enum('status', [
        'online', 
        'offline', 
        'maintenance'
    ])->default('offline');
    $table->enum('location_type', [
        'persiapan',    // Preparation area
        'memasak',      // Cooking area
        'pengemasan',   // Packaging area
        'distribusi'    // Distribution area
    ])->nullable();
    $table->text('keterangan')->nullable();
    $table->timestamp('last_online_at')->nullable();
    $table->timestamps();
});
```

### `cctv_anomaly_reports` Table
```php
Schema::create('cctv_anomaly_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cctv_id')->constrained()->cascadeOnDelete();
    $table->foreignId('reported_by')->constrained('users');
    $table->enum('type', [
        'offline',
        'blurry',
        'frozen',
        'no_signal',
        'tampered',
        'other'
    ]);
    $table->text('description');
    $table->enum('status', [
        'open',
        'in_progress',
        'resolved',
        'dismissed'
    ])->default('open');
    $table->foreignId('resolved_by')->nullable()->constrained('users');
    $table->timestamp('resolved_at')->nullable();
    $table->text('resolution_notes')->nullable();
    $table->timestamps();
});
```

## Models

### Cctv Model (`app/Models/Cctv.php`)
```php
class Cctv extends Model
{
    protected $fillable = [
        'dapur_id', 'nama', 'ip_address', 'rtsp_url',
        'status', 'location_type', 'keterangan', 'last_online_at'
    ];

    protected $casts = [
        'last_online_at' => 'datetime',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function anomalyReports(): HasMany
    {
        return $this->hasMany(CctvAnomalyReport::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'online' => 'status-ok',
            'offline' => 'status-off',
            'maintenance' => 'status-warn',
            default => 'status-gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'online' => 'Online',
            'offline' => 'Offline',
            'maintenance' => 'Maintenance',
            default => 'Unknown',
        };
    }
}
```

### CctvAnomalyReport Model (`app/Models/CctvAnomalyReport.php`)
```php
class CctvAnomalyReport extends Model
{
    protected $fillable = [
        'cctv_id', 'reported_by', 'type', 'description',
        'status', 'resolved_by', 'resolved_at', 'resolution_notes'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function cctv(): BelongsTo
    {
        return $this->belongsTo(Cctv::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
```

## Controllers

### Admin: `app/Http/Controllers/Admin/CctvController.php`
```php
class CctvController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Cctv::class);
        $cctvs = Cctv::with(['dapur', 'anomalyReports'])
            ->latest()
            ->paginate(15);
        return view('admin.cctvs.index', compact('cctvs'));
    }

    public function create()
    {
        $this->authorize('create', Cctv::class);
        $dapurs = Dapur::orderBy('nama')->get();
        return view('admin.cctvs.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Cctv::class);
        $data = $request->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'nama' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'rtsp_url' => 'nullable|url',
            'status' => 'required|in:online,offline,maintenance',
            'location_type' => 'nullable|in:persiapan,memasak,pengemasan,distribusi',
            'keterangan' => 'nullable|string',
        ]);
        Cctv::create($data);
        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV created');
    }

    public function show(Cctv $cctv)
    {
        $this->authorize('view', $cctv);
        $cctv->load(['dapur', 'anomalyReports' => fn($q) => $q->latest()]);
        return view('admin.cctvs.show', compact('cctv'));
    }

    public function edit(Cctv $cctv)
    {
        $this->authorize('update', $cctv);
        $dapurs = Dapur::orderBy('nama')->get();
        return view('admin.cctvs.edit', compact('cctv', 'dapurs'));
    }

    public function update(Request $request, Cctv $cctv)
    {
        $this->authorize('update', $cctv);
        $data = $request->validate([/* same as store */]);
        
        // Update last_online_at when status changes to online
        if ($data['status'] === 'online' && $cctv->status !== 'online') {
            $data['last_online_at'] = now();
        }
        
        $cctv->update($data);
        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV updated');
    }

    public function destroy(Cctv $cctv)
    {
        $this->authorize('delete', $cctv);
        $cctv->delete();
        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV deleted');
    }
}
```

### Anomaly Report Controller: `app/Http/Controllers/Admin/CctvAnomalyReportController.php`
```php
class CctvAnomalyReportController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', CctvAnomalyReport::class);
        $reports = CctvAnomalyReport::with(['cctv.dapur', 'reporter', 'resolver'])
            ->latest()
            ->paginate(15);
        return view('admin.cctv-anomalies.index', compact('reports'));
    }

    public function create()
    {
        $this->authorize('create', CctvAnomalyReport::class);
        $cctvs = Cctv::with('dapur')->orderBy('nama')->get();
        return view('admin.cctv-anomalies.create', compact('cctvs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', CctvAnomalyReport::class);
        $data = $request->validate([
            'cctv_id' => 'required|exists:cctvs,id',
            'type' => 'required|in:offline,blurry,frozen,no_signal,tampered,other',
            'description' => 'required|string|max:1000',
        ]);
        $data['reported_by'] = auth()->id();
        CctvAnomalyReport::create($data);
        
        // Notify admins via FCM
        FcmService::notifyAdmins('Anomali CCTV Baru', 
            "Anomali terdeteksi pada CCTV: {$request->cctv_id}");
        
        return redirect()->route('admin.cctv-anomalies.index')
            ->with('success', 'Laporan anomali dibuat');
    }

    public function show(CctvAnomalyReport $report)
    {
        $this->authorize('view', $report);
        $report->load(['cctv.dapur', 'reporter', 'resolver']);
        return view('admin.cctv-anomalies.show', compact('report'));
    }

    public function resolve(Request $request, CctvAnomalyReport $report)
    {
        $this->authorize('resolve', $report);
        $data = $request->validate([
            'resolution_notes' => 'nullable|string|max:1000',
        ]);
        $report->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_notes' => $data['resolution_notes'],
        ]);
        return redirect()->route('admin.cctv-anomalies.index')
            ->with('success', 'Anomali diselesaikan');
    }
}
```

### Pengawas CCTV Monitoring: `app/Http/Controllers/Pengawas/CctvController.php`
```php
class CctvController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Cctv::class);
        $dapurId = auth()->user()->dapur_id;
        $cctvs = Cctv::where('dapur_id', $dapurId)
            ->with('anomalyReports')
            ->get();
        return view('pengawas.cctvs.index', compact('cctvs'));
    }
}
```

## Routes

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('cctvs', CctvController::class);
    Route::resource('cctv-anomalies', CctvAnomalyReportController::class)
        ->only(['index', 'create', 'store', 'show', 'resolve']);
    Route::post('cctv-anomalies/{report}/resolve', [CctvAnomalyReportController::class, 'resolve'])
        ->name('cctv-anomalies.resolve');
});
```

### Pengawas Routes
```php
Route::prefix('pengawas')->name('pengawas.')->middleware(['auth', 'role:pengawas'])->group(function () {
    Route::get('cctvs', [CctvController::class, 'index'])->name('cctvs.index');
    Route::get('cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');
});
```

### Manajerial Routes
```php
Route::prefix('manajerial')->name('manajerial.')->middleware(['auth', 'role:manajerial'])->group(function () {
    Route::resource('cctvs', CctvController::class)->only(['index', 'show']);
    Route::resource('cctv-anomalies', CctvAnomalyReportController::class)->only(['index', 'show']);
});
```

## Views

### CCTV List (`resources/views/admin/cctvs/index.blade.php`)
- Table: Nama, Dapur, Lokasi, IP, Status, Last Online, Aksi
- Status badges with color coding
- Filter by status, dapur
- Bulk actions

### CCTV Detail (`resources/views/admin/cctvs/show.blade.php`)
- Camera info card
- RTSP stream embed (iframe placeholder)
- Anomaly history timeline
- Status change log

### Anomaly Reports List (`resources/views/admin/cctv-anomalies/index.blade.php`)
- Table: CCTV, Dapur, Tipe, Deskripsi, Status, Pelapor, Waktu
- Status badges (open=red, in_progress=yellow, resolved=green, dismissed=gray)
- Filter by status, type, dapur

### Pengawas CCTV Monitor (`resources/views/pengawas/cctvs/index.blade.php`)
- Grid of camera cards
- Status indicator (live dot animation)
- Camera preview placeholder
- Quick anomaly report button

## Dashboard Integration

### Stats Cards (Admin/Manajerial)
```php
$stats['total_cctv'] = Cctv::count();
$stats['cctv_online'] = Cctv::where('status', 'online')->count();
$stats['cctv_offline'] = Cctv::where('status', 'offline')->count();
$stats['cctv_maintenance'] = Cctv::where('status', 'maintenance')->count();
$stats['anomaly_reports_open'] = CctvAnomalyReport::where('status', 'open')->count();
```

### Camera Panel (All Dashboards)
```blade
<div class="panel">
    <div class="panel-head"><h2>Pratinjau CCTV</h2><small>Status terkini perangkat</small></div>
    <div class="camera">
        <span class="cam-tag inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
            <span class="live-dot" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:green;margin-right:6px;animation:pulse 2s infinite"></span> LIVE
        </span>
        <div class="camera-art" style="background:#1a1a1a;border-radius:8px;padding:24px;text-align:center;min-height:200px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:12px;margin-top:8px">
            <div style="font-size:48px;color:#444">▥</div>
            <strong style="color:#fff">Pratinjau Kamera Dapur</strong>
            <small style="color:#666">Stream CCTV akan tampil setelah integrasi perangkat</small>
        </div>
        <span class="cam-time">CAM-01 · {{ $dapurs->first()->nama ?? 'Dapur' }}</span>
    </div>
    <div class="camera-list mt-4 space-y-2">
        <div class="cam-row flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
            <div class="cam-left flex items-center gap-3">
                <div class="cam-mini w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">▣</div>
                <div><b>Area persiapan</b><div class="kitchen-meta">{{ $dapurs->first()->nama ?? 'Dapur' }}</div></div>
            </div>
            <span class="status-badge status-ok">Online</span>
        </div>
        <div class="cam-row flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
            <div class="cam-left flex items-center gap-3">
                <div class="cam-mini w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">▣</div>
                <div><b>Area memasak</b><div class="kitchen-meta">{{ $dapurs->skip(1)->first()->nama ?? 'Dapur' }}</div></div>
            </div>
            <span class="status-badge status-warn">Periksa</span>
        </div>
    </div>
</div>
```

## Anomaly Types

| Type | Label | Description |
|------|-------|-------------|
| `offline` | Offline | Camera not reachable |
| `blurry` | Blur/Tidak Jelas | Image quality degraded |
| `frozen` | Beku/Frozen | Stream frozen |
| `no_signal` | Tidak Ada Sinyal | No video signal |
| `tampered` | Disusupi/Dimodifikasi | Physical tampering |
| `other` | Lainnya | Other issues |

## Status Flow

```
open → in_progress → resolved
  ↘ dismissed
```

## Mobile API

### GET `/api/cctvs`
```json
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

### POST `/api/cctv-anomalies`
```json
{
    "cctv_id": 1,
    "type": "offline",
    "description": "Kamera tidak bisa diakses sejak 09:00"
}
```

Response:
```json
{
    "data": {
        "id": 5,
        "cctv_id": 1,
        "reported_by": 3,
        "type": "offline",
        "description": "Kamera tidak bisa diakses sejak 09:00",
        "status": "open",
        "created_at": "2026-10-26T10:35:00Z"
    }
}
```

## Testing

### Feature Tests (`tests/Feature/CctvTest.php`)
```php
/** @test */
public function admin_can_create_cctv()
{
    $user = User::factory()->admin()->create();
    $dapur = Dapur::factory()->create();
    $data = [
        'dapur_id' => $dapur->id,
        'nama' => 'CAM-01',
        'status' => 'online',
    ];
    
    $this->actingAs($user)
        ->post(route('admin.cctvs.store'), $data)
        ->assertRedirect();
    
    $this->assertDatabaseHas('cctvs', ['nama' => 'CAM-01']);
}

/** @test */
public function pengawas_can_report_anomaly()
{
    $pengawas = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create(['user_id' => $pengawas->id]);
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);
    $data = ['cctv_id' => $cctv->id, 'type' => 'offline', 'description' => 'Test'];
    
    $this->actingAs($pengawas)
        ->post(route('admin.cctv-anomalies.store'), $data)
        ->assertRedirect();
    
    $this->assertDatabaseHas('cctv_anomaly_reports', ['cctv_id' => $cctv->id]);
}

/** @test */
public function only_admin_can_resolve_anomaly()
{
    $admin = User::factory()->admin()->create();
    $pengawas = User::factory()->pengawas()->create();
    $report = CctvAnomalyReport::factory()->create(['status' => 'open']);
    
    $this->actingAs($pengawas)
        ->post(route('admin.cctv-anomalies.resolve', $report))
        ->assertForbidden();
    
    $this->actingAs($admin)
        ->post(route('admin.cctv-anomalies.resolve', $report), ['resolution_notes' => 'Fixed'])
        ->assertRedirect();
    
    $this->assertDatabaseHas('cctv_anomaly_reports', [
        'id' => $report->id,
        'status' => 'resolved'
    ]);
}
```

## Real-time Considerations

### WebSocket Integration (Future)
- Laravel Echo + Pusher/Soketi
- Real-time status updates
- Live anomaly notifications
- Stream health monitoring

### Polling Fallback (Current)
- Client-side polling every 30 seconds
- Status check via AJAX
- Anomaly count badge updates

## Performance
- Index on `dapur_id`, `status` columns
- Eager load: `with(['dapur', 'anomalyReports'])`
- Paginate lists (15 per page)
- Cache online count for dashboard (1 min TTL)