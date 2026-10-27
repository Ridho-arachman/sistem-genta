# User Roles & Permissions Documentation

## Overview
Role-based access control (RBAC) system with four distinct user roles, each with specific permissions and dashboard access.

## Roles

### 1. Admin (`admin`)
**Full system access**
- User management (create, edit, delete, assign roles)
- All CRUD operations on all entities
- System settings
- View all dashboards
- Policy management

### 2. Manajerial (`manajerial`)
**Read-only management dashboard**
- Dashboard with system-wide statistics
- View all dapur, CCTV, absensi
- View SP warnings and anomaly reports
- Export reports
- **Cannot** create/edit/delete

### 3. Pengawas (`pengawas`)
**Field supervisor operations**
- Assigned to one dapur
- Dashboard with own dapur status
- Check-in/check-out absensi
- Report CCTV anomalies
- View own absensi history
- View assigned dapur CCTV status

### 4. Kepala Dapur (`kepala_dapur`)
**Kitchen head operations (Legacy Layout)**
- Kitchen operations dashboard
- View own dapur details
- Respond to SP warnings
- **Excluded from layout upgrade**

## Role Assignment

### User Model (`app/Models/User.php`)
```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'dapur_id',
        'fcm_token', 'fcm_platform'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Roles constant
    const ROLES = ['admin', 'manajerial', 'pengawas', 'kepala_dapur'];

    // Scopes
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeManajerial($query)
    {
        return $query->where('role', 'manajerial');
    }

    public function scopePengawas($query)
    {
        return $query->where('role', 'pengawas');
    }

    public function scopeKepalaDapur($query)
    {
        return $query->where('role', 'kepala_dapur');
    }

    // Relationships
    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManajerial(): bool
    {
        return $this->role === 'manajerial';
    }

    public function isPengawas(): bool
    {
        return $this->role === 'pengawas';
    }

    public function isKepalaDapur(): bool
    {
        return $this->role === 'kepala_dapur';
    }

    public function canAccessPanel(string $panel): bool
    {
        return match ($panel) {
            'admin' => $this->isAdmin(),
            'manajerial' => in_array($this->role, ['admin', 'manajerial']),
            'pengawas' => in_array($this->role, ['admin', 'pengawas']),
            'kepala-dapur' => in_array($this->role, ['admin', 'kepala_dapur']),
            default => false,
        };
    }
}
```

## Middleware

### Role Middleware (`app/Http/Middleware/RoleMiddleware.php`)
```php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized. Required roles: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
```

### Registration (`app/Http/Kernel.php`)
```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

## Route Protection

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('dapurs', DapurController::class);
    Route::resource('cctvs', CctvController::class);
    Route::resource('cctv-anomalies', CctvAnomalyReportController::class);
    Route::resource('sp-warnings', SpWarningController::class);
    Route::resource('absensi', AbsensiController::class)->only(['index', 'show']);
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

### Manajerial Routes
```php
Route::prefix('manajerial')->name('manajerial.')->middleware(['auth', 'role:admin,manajerial'])->group(function () {
    Route::resource('dapurs', DapurController::class)->only(['index', 'show']);
    Route::resource('cctvs', CctvController::class)->only(['index', 'show']);
    Route::resource('cctv-anomalies', CctvAnomalyReportController::class)->only(['index', 'show']);
    Route::resource('sp-warnings', SpWarningController::class)->only(['index', 'show']);
    Route::resource('absensi', AbsensiController::class)->only(['index', 'show']);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

### Pengawas Routes
```php
Route::prefix('pengawas')->name('pengawas.')->middleware(['auth', 'role:admin,pengawas'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('dapurs', DapurController::class)->only(['index', 'show']);
    Route::resource('absensi', AbsensiController::class)->only(['index', 'create', 'store']);
    Route::post('absensi/{absensi}/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.check-out');
    Route::get('cctvs', [CctvController::class, 'index'])->name('cctvs.index');
    Route::get('cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');
});
```

### Kepala Dapur Routes
```php
Route::prefix('kepala-dapur')->name('kepala-dapur.')->middleware(['auth', 'role:admin,kepala_dapur'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('sp-warnings', [SpWarningController::class, 'index'])->name('sp-warnings.index');
    Route::get('sp-warnings/{warning}', [SpWarningController::class, 'show'])->name('sp-warnings.show');
    Route::post('sp-warnings/{warning}/respond', [SpWarningController::class, 'respond'])->name('sp-warnings.respond');
});
```

## Dashboard Redirection

### AuthController Login Redirect
```php
protected function redirectToDashboard(User $user)
{
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'manajerial' => redirect()->route('manajerial.dashboard'),
        'pengawas' => redirect()->route('pengawas.dashboard'),
        'kepala_dapur' => redirect()->route('kepala-dapur.dashboard'),
        default => redirect()->route('dashboard'),
    };
}
```

## Permission Matrix

| Feature | Admin | Manajerial | Pengawas | Kepala Dapur |
|---------|-------|------------|----------|--------------|
| **User Management** | | | | |
| View Users | ✅ | ❌ | ❌ | ❌ |
| Create Users | ✅ | ❌ | ❌ | ❌ |
| Edit Users | ✅ | ❌ | ❌ | ❌ |
| Delete Users | ✅ | ❌ | ❌ | ❌ |
| Assign Roles | ✅ | ❌ | ❌ | ❌ |
| **Dapur Management** | | | | |
| View All | ✅ | ✅ | Own only | Own only |
| Create | ✅ | ❌ | ❌ | ❌ |
| Edit | ✅ | ❌ | ❌ | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |
| **CCTV Management** | | | | |
| View All | ✅ | ✅ | Own dapur | ❌ |
| Create | ✅ | ❌ | ❌ | ❌ |
| Edit | ✅ | ❌ | ❌ | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |
| Monitor | ✅ | ✅ | Own dapur | ❌ |
| **Absensi** | | | | |
| View All | ✅ | ✅ | Own | ❌ |
| Create (Check-in) | ✅ | ❌ | ✅ | ❌ |
| Check-out | ✅ | ❌ | ✅ | ❌ |
| Edit | ✅ | ❌ | Own (1hr) | ❌ |
| **SP Warning** | | | | |
| View All | ✅ | ✅ | ❌ | Own dapur |
| Create | ✅ | ❌ | ❌ | ❌ |
| Issue (Terbit) | ✅ | ❌ | ❌ | ❌ |
| Verify/Resolve | ✅ | ❌ | ❌ | ❌ |
| Respond | ❌ | ❌ | ❌ | ✅ |
| **CCTV Anomaly** | | | | |
| View All | ✅ | ✅ | Own dapur | ❌ |
| Create | ✅ | ❌ | ✅ | ❌ |
| Update | ✅ | ❌ | Own (open) | ❌ |
| Resolve | ✅ | ❌ | ❌ | ❌ |
| **Reports/Export** | | | | |
| Dashboard Stats | ✅ | ✅ | Own dapur | Own dapur |
| Export Data | ✅ | ✅ | ❌ | ❌ |
| **System Settings** | | | | |
| View | ✅ | ✅ | ❌ | ❌ |
| Edit | ✅ | ❌ | ❌ | ❌ |

## Blade Directives

### Role Checks
```blade
@role('admin')
    <a href="{{ route('admin.users.create') }}">Tambah User</a>
@endrole

@role('admin,manajerial')
    <a href="{{ route('admin.dapurs.index') }}">Data Dapur</a>
@endrole

@hasrole('pengawas')
    <a href="{{ route('pengawas.absensi.create') }}">Check-in</a>
@endhasrole
```

### Custom Directives (if registered)
```php
// In AppServiceProvider
Blade::if('role', function ($roles) {
    $roles = is_array($roles) ? $roles : explode(',', $roles);
    return auth()->check() && in_array(auth()->user()->role, $roles);
});

Blade::if('hasrole', function ($role) {
    return auth()->check() && auth()->user()->role === $role;
});
```

## Factory States

### UserFactory (`database/factories/UserFactory.php`)
```php
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => 'pengawas',
            'dapur_id' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role' => 'admin']);
    }

    public function manajerial(): static
    {
        return $this->state(fn() => ['role' => 'manajerial']);
    }

    public function pengawas(): static
    {
        return $this->state(fn() => ['role' => 'pengawas']);
    }

    public function kepalaDapur(): static
    {
        return $this->state(fn() => ['role' => 'kepala_dapur']);
    }
}
```

## Testing Role Access

### Feature Tests
```php
/** @test */
public function admin_can_access_all_panels()
{
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('manajerial.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('pengawas.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('kepala-dapur.dashboard'))->assertOk();
}

/** @test */
public function manajerial_cannot_access_admin_panel()
{
    $manajerial = User::factory()->manajerial()->create();
    
    $this->actingAs($manajerial)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($manajerial)->get(route('manajerial.dashboard'))->assertOk();
}

/** @test */
public function pengawas_can_only_access_own_dapur()
{
    $pengawas = User::factory()->pengawas()->create();
    $ownDapur = Dapur::factory()->create(['user_id' => $pengawas->id]);
    $otherDapur = Dapur::factory()->create();
    
    $this->actingAs($pengawas)->get(route('admin.dapurs.show', $ownDapur))->assertOk();
    $this->actingAs($pengawas)->get(route('admin.dapurs.show', $otherDapur))->assertForbidden();
}

/** @test */
public function kepala_dapur_can_respond_to_sp_warning()
{
    $kepala = User::factory()->kepalaDapur()->create();
    $dapur = Dapur::factory()->create(['user_id' => $kepala->id]);
    $sp = SpWarning::factory()->create(['dapur_id' => $dapur->id, 'status' => 'terbit']);
    
    $this->actingAs($kepala)
        ->post(route('kepala-dapur.sp-warnings.respond', $sp), ['tanggapan' => 'Test'])
        ->assertRedirect();
}
```

## API Role Checks

### Mobile API Controller
```php
// app/Http/Controllers/Api/MobileController.php
public function checkIn(Request $request)
{
    $user = $request->user();
    
    // Only pengawas can check-in
    if (!$user->isPengawas()) {
        return response()->json(['message' => 'Only pengawas can check-in'], 403);
    }
    
    // Must have assigned dapur
    if (!$user->dapur_id) {
        return response()->json(['message' => 'No dapur assigned'], 400);
    }
    
    // ... check-in logic
}
```

## Role Seeder

### DatabaseSeeder
```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    // Create admin
    User::factory()->admin()->create([
        'name' => 'Admin Genta',
        'email' => 'admin@genta-banten.go.id',
        'password' => bcrypt('password'),
    ]);

    // Create manajerial
    User::factory()->manajerial()->create([
        'name' => 'Manajerial Genta',
        'email' => 'manajerial@genta-banten.go.id',
        'password' => bcrypt('password'),
    ]);

    // Create dapurs with pengawas
    $dapurs = Dapur::factory(4)->create();
    
    foreach ($dapurs as $dapur) {
        User::factory()->pengawas()->create([
            'dapur_id' => $dapur->id,
        ]);
        
        User::factory()->kepalaDapur()->create([
            'dapur_id' => $dapur->id,
        ]);
    }
}
```