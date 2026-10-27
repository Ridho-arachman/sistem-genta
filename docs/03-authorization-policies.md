# Authorization Policies Documentation

## Overview
Laravel Policies implementation for granular access control across all user roles.

## Policies Created

### 1. DapurPolicy (`app/Policies/DapurPolicy.php`)
Controls access to kitchen management operations.

```php
class DapurPolicy
{
    use HandlesAuthorization;

    // Admin & Manajerial can view all
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manajerial']);
    }

    // Admin can create, Manajerial read-only
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Admin full access, Manajerial read-only, Pengawas own kitchen
    public function view(User $user, Dapur $dapur): bool
    {
        if ($user->role === 'admin' || $user->role === 'manajerial') {
            return true;
        }
        return $user->role === 'pengawas' && $user->dapur_id === $dapur->id;
    }

    public function update(User $user, Dapur $dapur): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Dapur $dapur): bool
    {
        return $user->role === 'admin';
    }
}
```

### 2. CctvPolicy (`app/Policies/CctvPolicy.php`)
Controls access to CCTV device management.

```php
class CctvPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manajerial', 'pengawas']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Cctv $cctv): bool
    {
        if (in_array($user->role, ['admin', 'manajerial'])) {
            return true;
        }
        return $user->role === 'pengawas' && $user->dapur_id === $cctv->dapur_id;
    }

    public function update(User $user, Cctv $cctv): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Cctv $cctv): bool
    {
        return $user->role === 'admin';
    }

    // Monitoring access for pengawas
    public function monitor(User $user, Cctv $cctv): bool
    {
        return $this->view($user, $cctv);
    }
}
```

### 3. AbsensiPolicy (`app/Policies/AbsensiPolicy.php`)
Controls access to attendance records.

```php
class AbsensiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manajerial', 'pengawas']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas']);
    }

    public function view(User $user, Absensi $absensi): bool
    {
        if (in_array($user->role, ['admin', 'manajerial'])) {
            return true;
        }
        return $user->role === 'pengawas' && $user->id === $absensi->user_id;
    }

    public function update(User $user, Absensi $absensi): bool
    {
        // Only admin or owner (within time window)
        if ($user->role === 'admin') return true;
        return $user->id === $absensi->user_id 
            && $absensi->check_in_at->diffInMinutes(now()) < 60;
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return $user->role === 'admin';
    }
}
```

### 4. CctvAnomalyReportPolicy (`app/Policies/CctvAnomalyReportPolicy.php`)
Controls access to CCTV anomaly reports.

```php
class CctvAnomalyReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'manajerial', 'pengawas']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas']);
    }

    public function view(User $user, CctvAnomalyReport $report): bool
    {
        if (in_array($user->role, ['admin', 'manajerial'])) {
            return true;
        }
        return $user->role === 'pengawas' && $user->dapur_id === $report->cctv->dapur_id;
    }

    public function update(User $user, CctvAnomalyReport $report): bool
    {
        // Admin can update status, pengawas can add notes
        if ($user->role === 'admin') return true;
        return $user->role === 'pengawas' 
            && $user->dapur_id === $report->cctv->dapur_id
            && $report->status === 'open';
    }

    public function delete(User $user, CctvAnomalyReport $report): bool
    {
        return $user->role === 'admin';
    }

    public function resolve(User $user, CctvAnomalyReport $report): bool
    {
        return $user->role === 'admin';
    }
}
```

## Policy Registration

### AppServiceProvider (`app/Providers/AppServiceProvider.php`)
```php
public function boot(): void
{
    Gate::policy(Dapur::class, DapurPolicy::class);
    Gate::policy(Cctv::class, CctvPolicy::class);
    Gate::policy(Absensi::class, AbsensiPolicy::class);
    Gate::policy(CctvAnomalyReport::class, CctvAnomalyReportPolicy::class);
}
```

## Usage in Controllers

### Controller Authorization
```php
class DapurController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Dapur::class);
        // ...
    }

    public function show(Dapur $dapur)
    {
        $this->authorize('view', $dapur);
        // ...
    }

    public function store(Request $request)
    {
        $this->authorize('create', Dapur::class);
        // ...
    }

    public function update(Request $request, Dapur $dapur)
    {
        $this->authorize('update', $dapur);
        // ...
    }

    public function destroy(Dapur $dapur)
    {
        $this->authorize('delete', $dapur);
        // ...
    }
}
```

### Blade Directives
```blade
@can('view', $dapur)
    <a href="{{ route('admin.dapurs.show', $dapur) }}">Lihat</a>
@endcan

@can('update', $dapur)
    <a href="{{ route('admin.dapurs.edit', $dapur) }}">Edit</a>
@endcan

@cannot('delete', $dapur)
    <button disabled>Hapus</button>
@endcannot
```

## Role Permission Matrix

| Action | Admin | Manajerial | Pengawas | Kepala Dapur |
|--------|-------|------------|----------|--------------|
| **Dapur** | | | | |
| View List | ✅ | ✅ | ❌ | ❌ |
| View Detail | ✅ | ✅ | Own only | ❌ |
| Create | ✅ | ❌ | ❌ | ❌ |
| Update | ✅ | ❌ | ❌ | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |
| **CCTV** | | | | |
| View List | ✅ | ✅ | ✅ | ❌ |
| View Detail | ✅ | ✅ | Own kitchen | ❌ |
| Create | ✅ | ❌ | ❌ | ❌ |
| Update | ✅ | ❌ | ❌ | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |
| Monitor | ✅ | ✅ | Own kitchen | ❌ |
| **Absensi** | | | | |
| View List | ✅ | ✅ | ✅ | ❌ |
| View Detail | ✅ | ✅ | Own only | ❌ |
| Create | ✅ | ❌ | ✅ | ❌ |
| Update | ✅ | ❌ | Own (1hr) | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |
| **CCTV Anomaly** | | | | |
| View List | ✅ | ✅ | ✅ | ❌ |
| View Detail | ✅ | ✅ | Own kitchen | ❌ |
| Create | ✅ | ❌ | ✅ | ❌ |
| Update | ✅ | ❌ | Own (open) | ❌ |
| Resolve | ✅ | ❌ | ❌ | ❌ |
| Delete | ✅ | ❌ | ❌ | ❌ |

## Testing Policies
```bash
php artisan test --filter=PolicyTest
```

### Test Examples
```php
// Test admin can create dapur
$user = User::factory()->admin()->create();
$this->actingAs($user)->post(route('admin.dapurs.store'), $data)->assertOk();

// Test pengawas cannot create dapur
$user = User::factory()->pengawas()->create();
$this->actingAs($user)->post(route('admin.dapurs.store'), $data)->assertForbidden();

// Test pengawas can only view own kitchen
$dapur = Dapur::factory()->create(['id' => $user->dapur_id]);
$this->actingAs($user)->get(route('admin.dapurs.show', $dapur))->assertOk();

$otherDapur = Dapur::factory()->create();
$this->actingAs($user)->get(route('admin.dapurs.show', $otherDapur))->assertForbidden();
```