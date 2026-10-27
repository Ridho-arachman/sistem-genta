# Surat Peringatan (SP Warning) System Documentation

## Overview
Multi-level warning letter system (SP1, SP2, SP3) for compliance management in MBG kitchens.

## Database Schema

### `sp_warnings` Table
```php
Schema::create('sp_warnings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dapur_id')->constrained()->cascadeOnDelete();
    $table->foreignId('issued_by')->constrained('users');      // Admin who issued
    $table->enum('level', ['SP1', 'SP2', 'SP3'])->default('SP1');
    $table->string('nomor_surat')->unique();                   // SP-2026-001
    $table->text('pelanggaran');                               // Violation description
    $table->text('dasar_hukum')->nullable();                   // Legal basis
    $table->date('tanggal_terbit');                            // Issue date
    $table->date('tanggal_tenggat')->nullable();               // Deadline
    $table->enum('status', [
        'draft',
        'terbit',           // Issued
        'diterima',         // Received by kitchen
        'ditanggapi',       // Responded
        'selesai',          // Resolved
        'batal'             // Cancelled
    ])->default('draft');
    $table->text('tanggapan')->nullable();                     // Kitchen response
    $table->timestamp('tanggapan_at')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users');
    $table->timestamp('verified_at')->nullable();
    $table->timestamps();
});
```

### SP Warning Numbering
```
Format: SP-{YEAR}-{SEQUENTIAL}
Example: SP-2026-001, SP-2026-002
```

## Models

### SpWarning Model (`app/Models/SpWarning.php`)
```php
class SpWarning extends Model
{
    protected $fillable = [
        'dapur_id', 'issued_by', 'level', 'nomor_surat',
        'pelanggaran', 'dasar_hukum', 'tanggal_terbit', 'tanggal_tenggat',
        'status', 'tanggapan', 'tanggapan_at', 'verified_by', 'verified_at'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_tenggat' => 'date',
        'tanggapan_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Auto-generate nomor_surat on creating
    protected static function booted()
    {
        static::creating(function ($sp) {
            if (empty($sp->nomor_surat)) {
                $year = now()->year;
                $last = static::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->first();
                $sequence = $last ? (int)explode('-', $last->nomor_surat)[2] + 1 : 1;
                $sp->nomor_surat = "SP-{$year}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['terbit', 'diterima', 'ditanggapi']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'selesai')
            ->where('tanggal_tenggat', '<', now());
    }

    // Helpers
    public function getLevelColor(): string
    {
        return match($this->level) {
            'SP1' => 'blue',
            'SP2' => 'yellow',
            'SP3' => 'red',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'status-gray',
            'terbit' => 'status-info',
            'diterima' => 'status-warn',
            'ditanggapi' => 'status-info',
            'selesai' => 'status-ok',
            'batal' => 'status-off',
            default => 'status-gray',
        };
    }

    public function isOverdue(): bool
    {
        return $this->tanggal_tenggat 
            && $this->tanggal_tenggat->isPast() 
            && !in_array($this->status, ['selesai', 'batal']);
    }
}
```

## Controllers

### Admin: `app/Http/Controllers/Admin/SpWarningController.php`
```php
class SpWarningController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', SpWarning::class);
        $warnings = SpWarning::with(['dapur', 'issuer', 'verifier'])
            ->latest()
            ->paginate(20);
        return view('admin.sp-warnings.index', compact('warnings'));
    }

    public function create()
    {
        $this->authorize('create', SpWarning::class);
        $dapurs = Dapur::orderBy('nama')->get();
        return view('admin.sp-warnings.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SpWarning::class);
        $data = $request->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'level' => 'required|in:SP1,SP2,SP3',
            'pelanggaran' => 'required|string|max:2000',
            'dasar_hukum' => 'nullable|string|max:1000',
            'tanggal_terbit' => 'required|date',
            'tanggal_tenggat' => 'nullable|date|after_or_equal:tanggal_terbit',
            'status' => 'required|in:draft,terbit',
        ]);
        $data['issued_by'] = auth()->id();
        SpWarning::create($data);
        return redirect()->route('admin.sp-warnings.index')->with('success', 'SP Warning created');
    }

    public function show(SpWarning $warning)
    {
        $this->authorize('view', $warning);
        $warning->load(['dapur', 'issuer', 'verifier']);
        return view('admin.sp-warnings.show', compact('warning'));
    }

    public function edit(SpWarning $warning)
    {
        $this->authorize('update', $warning);
        if ($warning->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa diedit.');
        }
        $dapurs = Dapur::orderBy('nama')->get();
        return view('admin.sp-warnings.edit', compact('warning', 'dapurs'));
    }

    public function update(Request $request, SpWarning $warning)
    {
        $this->authorize('update', $warning);
        if ($warning->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa diedit.');
        }
        $data = $request->validate([/* same as store */]);
        $warning->update($data);
        return redirect()->route('admin.sp-warnings.index')->with('success', 'SP Warning updated');
    }

    public function terbit(SpWarning $warning)
    {
        $this->authorize('update', $warning);
        $warning->update(['status' => 'terbit']);
        
        // Notify kitchen head and pengawas
        FcmService::notifyDapur($warning->dapur_id, 
            'Surat Peringatan Baru', 
            "SP {$warning->level} telah diterbitkan: {$warning->nomor_surat}"
        );
        
        return back()->with('success', 'SP Warning diterbitkan');
    }

    public function verifikasi(Request $request, SpWarning $warning)
    {
        $this->authorize('update', $warning);
        $data = $request->validate([
            'tanggapan' => 'nullable|string|max:2000',
            'status' => 'required|in:ditanggapi,selesai',
        ]);
        $warning->update([
            'tanggapan' => $data['tanggapan'],
            'tanggapan_at' => now(),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'status' => $data['status'],
        ]);
        return back()->with('success', 'Verifikasi selesai');
    }

    public function destroy(SpWarning $warning)
    {
        $this->authorize('delete', $warning);
        if ($warning->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa dihapus.');
        }
        $warning->delete();
        return redirect()->route('admin.sp-warnings.index')->with('success', 'SP Warning deleted');
    }
}
```

## Routes

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('sp-warnings', SpWarningController::class);
    Route::post('sp-warnings/{warning}/terbit', [SpWarningController::class, 'terbit'])
        ->name('sp-warnings.terbit');
    Route::post('sp-warnings/{warning}/verifikasi', [SpWarningController::class, 'verifikasi'])
        ->name('sp-warnings.verifikasi');
});
```

### Manajerial Routes
```php
Route::prefix('manajerial')->name('manajerial.')->middleware(['auth', 'role:manajerial'])->group(function () {
    Route::resource('sp-warnings', SpWarningController::class)->only(['index', 'show']);
});
```

## Views

### Index Page (`resources/views/admin/sp-warnings/index.blade.php`)
- Table: No. Surat, Dapur, Level, Pelanggaran, Status, Tanggal, Tenggat, Aksi
- Status badges with level colors
- Filter by level, status, dapur
- Overdue highlighting (red row)
- Action buttons: View, Edit (draft), Terbit (draft), Verifikasi (terbit), Delete (draft)

### Create/Edit Form (`resources/views/admin/sp-warnings/create.blade.php`)
- Dapur dropdown (required)
- Level radio: SP1 (blue), SP2 (yellow), SP3 (red)
- Pelanggaran textarea (required)
- Dasar hukum textarea
- Tanggal terbit (date picker)
- Tanggal tenggat (date picker, optional)
- Status: Draft / Terbit

### Show Page (`resources/views/admin/sp-warnings/show.blade.php`)
- Full letter preview
- Dapur info card
- Timeline: Draft → Terbit → Diterima → Ditanggapi → Selesai
- Response section (if verified)
- Action buttons based on status

## Dashboard Integration

### Stats Card (Admin/Manajerial)
```php
$stats['sp_warnings_aktif'] = SpWarning::whereIn('status', ['terbit', 'diterima', 'ditanggapi'])->count();
$stats['sp_warnings_overdue'] = SpWarning::overdue()->count();
$stats['sp_warnings_sp1'] = SpWarning::where('level', 'SP1')->whereIn('status', ['terbit','diterima','ditanggapi'])->count();
$stats['sp_warnings_sp2'] = SpWarning::where('level', 'SP2')->whereIn('status', ['terbit','diterima','ditanggapi'])->count();
$stats['sp_warnings_sp3'] = SpWarning::where('level', 'SP3')->whereIn('status', ['terbit','diterima','ditanggapi'])->count();
```

### Activity Feed
```blade
<div class="activity-row">
    <div class="activity-icon w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600">⚠</div>
    <div class="flex-1">
        <strong>SP Warning diterbitkan</strong>
        <p class="text-sm text-[var(--color-text-muted)]">
            SP2 - {{ $warning->dapur->nama }} · {{ $warning->nomor_surat }}
        </p>
    </div>
    <time class="text-sm text-[var(--color-text-muted)]">
        {{ $warning->tanggal_terbit->format('d M Y') }}
    </time>
</div>
```

## SP Level Escalation

### Level Definitions
| Level | Name | Color | Typical Use | Next Step |
|-------|------|-------|-------------|-----------|
| **SP1** | Surat Peringatan I | Blue | First violation, minor issues | SP2 if not resolved |
| **SP2** | Surat Peringatan II | Yellow | Repeat violation, moderate issues | SP3 if not resolved |
| **SP3** | Surat Peringatan III | Red | Serious/repeated violations | Administrative action |

### Escalation Rules
1. SP1 → SP2: If not resolved within deadline
2. SP2 → SP3: If not resolved within deadline
3. SP3: May lead to contract termination recommendation

## Status Flow

```
draft → terbit → diterima → ditanggapi → selesai
              ↘ batal
```

### Status Transitions
| From | To | Who | Condition |
|------|-----|-----|-----------|
| draft | terbit | Admin | Ready to issue |
| draft | batal | Admin | Cancelled before issue |
| terbit | diterima | Kitchen Head | Acknowledged receipt |
| diterima | ditanggapi | Kitchen Head | Submitted response |
| ditanggapi | selesai | Admin | Verified resolution |
| ditanggapi | batal | Admin | Withdrawn |
| terbit | batal | Admin | Withdrawn before receipt |

## Notifications

### FCM Notifications
```php
// On terbit (issue)
FcmService::notifyDapur($dapurId, 
    'Surat Peringatan Baru', 
    "SP {$level} telah diterbitkan: {$nomor_surat}"
);

// On overdue (scheduled job)
$overdue = SpWarning::overdue()->get();
foreach ($overdue as $warning) {
    FcmService::notifyAdmins(
        'SP Warning Melewati Tenggat',
        "SP {$warning->level} {$warning->nomor_surat} melewati tenggat"
    );
}
```

## Reports

### SP Warning Report (`resources/views/admin/sp-warnings/report.blade.php`)
- Filter by date range, level, status, dapur
- Summary cards: Total, Active, Overdue, Resolved
- Detailed table with export to Excel/PDF

### Monthly Recap
```php
SpWarning::selectRaw('level, status, count(*) as count')
    ->whereMonth('tanggal_terbit', now()->month)
    ->groupBy('level', 'status')
    ->get();
```

## Testing

### Feature Tests (`tests/Feature/SpWarningTest.php`)
```php
/** @test */
public function admin_can_create_sp_warning()
{
    $admin = User::factory()->admin()->create();
    $dapur = Dapur::factory()->create();
    $data = [
        'dapur_id' => $dapur->id,
        'level' => 'SP1',
        'pelanggaran' => 'Test violation',
        'tanggal_terbit' => today(),
    ];
    
    $this->actingAs($admin)
        ->post(route('admin.sp-warnings.store'), $data)
        ->assertRedirect();
    
    $this->assertDatabaseHas('sp_warnings', [
        'dapur_id' => $dapur->id,
        'level' => 'SP1',
    ]);
}

/** @test */
public function auto_generates_nomor_surat()
{
    $admin = User::factory()->admin()->create();
    $dapur = Dapur::factory()->create();
    
    $sp1 = SpWarning::factory()->create(['nomor_surat' => 'SP-2026-001']);
    $sp2 = SpWarning::factory()->create(['nomor_surat' => 'SP-2026-002']);
    
    $data = ['dapur_id' => $dapur->id, 'level' => 'SP1', 'pelanggaran' => 'Test', 'tanggal_terbit' => today()];
    
    $this->actingAs($admin)->post(route('admin.sp-warnings.store'), $data);
    
    $this->assertDatabaseHas('sp_warnings', ['nomor_surat' => 'SP-2026-003']);
}

/** @test */
public function only_draft_can_be_edited()
{
    $admin = User::factory()->admin()->create();
    $sp = SpWarning::factory()->create(['status' => 'terbit']);
    
    $this->actingAs($admin)
        ->get(route('admin.sp-warnings.edit', $sp))
        ->assertSessionHas('error');
}
```

## PDF Generation (Future)
```php
// Using dompdf or barryvdh/laravel-dompdf
public function pdf(SpWarning $warning)
{
    $pdf = Pdf::loadView('admin.sp-warnings.pdf', compact('warning'));
    return $pdf->download("SP-{$warning->nomor_surat}.pdf");
}
```

## Compliance Notes
- SP letters must reference specific regulations
- Deadline typically 14 days from issue date
- Response required within 7 days of receipt
- All communications logged for audit trail
- Digital signature integration planned