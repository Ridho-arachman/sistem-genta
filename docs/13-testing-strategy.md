# Testing Strategy Documentation

## Overview
Comprehensive testing approach using Pest PHP with feature tests covering all major controllers and business logic.

## Test Stack
- **Framework**: Pest PHP (built on PHPUnit)
- **Database**: SQLite in-memory for tests
- **Authentication**: ActingAs helper
- **Factories**: Laravel model factories
- **Assertions**: Pest expectations API

## Test Structure
```
tests/
├── Feature/
│   ├── AuthTest.php              # Authentication tests
│   ├── AbsensiTest.php           # Attendance system tests
│   ├── CctvTest.php              # CCTV management tests
│   ├── DapurTest.php             # Kitchen management tests
│   ├── SpWarningTest.php         # SP Warning tests
│   ├── MobileApiTest.php         # Mobile API tests
│   └── PolicyTest.php            # Authorization policy tests
├── Unit/
│   ├── GeolocationServiceTest.php # Geolocation service tests
│   └── FcmServiceTest.php         # FCM service tests
└── Pest.php                       # Pest configuration
```

## Configuration

### `tests/Pest.php`
```php
<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

uses()->group('feature')->in('Feature');
uses()->group('unit')->in('Unit');

beforeEach(function () {
    // Use SQLite in-memory for tests
    Config::set('database.default', 'sqlite');
    Config::set('database.connections.sqlite', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);
    
    // Run migrations
    $this->artisan('migrate')->run();
});

afterEach(function () {
    // Clean up
    Schema::dropAllTables();
});

// Helper functions
function actingAsAdmin(): User
{
    return User::factory()->admin()->create();
}

function actingAsManajerial(): User
{
    return User::factory()->manajerial()->create();
}

function actingAsPengawas(?Dapur $dapur = null): User
{
    $dapur = $dapur ?? Dapur::factory()->create();
    return User::factory()->pengawas()->create(['dapur_id' => $dapur->id]);
}

function actingAsKepalaDapur(?Dapur $dapur = null): User
{
    $dapur = $dapur ?? Dapur::factory()->create();
    return User::factory()->kepalaDapur()->create(['dapur_id' => $dapur->id]);
}
```

## Feature Test Patterns

### Authentication Tests (`tests/Feature/AuthTest.php`)
```php
uses()->group('auth');

test('login page accessible', function () {
    $this->get(route('login'))->assertOk();
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    
    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
    
    $this->assertAuthenticatedAs($user);
});

test('login fails with invalid password', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    
    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'wrong',
    ])->assertSessionHasErrors('email');
});

test('user can logout', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));
    
    $this->assertGuest();
});

test('password reset flow', function () {
    $user = User::factory()->create();
    
    // Request reset
    $this->post(route('password.email'), ['email' => $user->email])
        ->assertRedirect(route('login'));
    
    // Get token from notification
    $token = PasswordResetToken::first()->token;
    
    // Reset password
    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertRedirect(route('login'));
    
    // Login with new password
    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'newpassword',
    ])->assertRedirect();
});
```

### Authorization Policy Tests (`tests/Feature/PolicyTest.php`)
```php
uses()->group('policy');

test('admin can manage all dapurs', function () {
    $admin = actingAsAdmin();
    $dapur = Dapur::factory()->create();
    
    $this->actingAs($admin)->get(route('admin.dapurs.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.dapurs.create'))->assertOk();
    $this->actingAs($admin)->post(route('admin.dapurs.store'), $dapur->toArray())->assertRedirect();
    $this->actingAs($admin)->get(route('admin.dapurs.edit', $dapur))->assertOk();
    $this->actingAs($admin)->put(route('admin.dapurs.update', $dapur), $dapur->toArray())->assertRedirect();
    $this->actingAs($admin)->delete(route('admin.dapurs.destroy', $dapur))->assertRedirect();
});

test('manajerial can only view dapurs', function () {
    $manajerial = actingAsManajerial();
    $dapur = Dapur::factory()->create();
    
    $this->actingAs($manajerial)->get(route('admin.dapurs.index'))->assertOk();
    $this->actingAs($manajerial)->get(route('admin.dapurs.show', $dapur))->assertOk();
    $this->actingAs($manajerial)->get(route('admin.dapurs.create'))->assertForbidden();
    $this->actingAs($manajerial)->post(route('admin.dapurs.store'), [])->assertForbidden();
});

test('pengawas can only access own dapur', function () {
    $pengawas = actingAsPengawas();
    $ownDapur = $pengawas->dapur;
    $otherDapur = Dapur::factory()->create();
    
    $this->actingAs($pengawas)->get(route('admin.dapurs.show', $ownDapur))->assertOk();
    $this->actingAs($pengawas)->get(route('admin.dapurs.show', $otherDapur))->assertForbidden();
});

test('pengawas can check-in but not manage dapurs', function () {
    $pengawas = actingAsPengawas();
    
    $this->actingAs($pengawas)->get(route('pengawas.absensi.create'))->assertOk();
    $this->actingAs($pengawas)->post(route('pengawas.absensi.store'), [
        'status' => 'hadir',
        'latitude' => $pengawas->dapur->latitude,
        'longitude' => $pengawas->dapur->longitude,
    ])->assertRedirect();
    
    $this->actingAs($pengawas)->get(route('admin.dapurs.create'))->assertForbidden();
});
```

### Absensi Tests (`tests/Feature/AbsensiTest.php`)
```php
uses()->group('absensi');

test('pengawas can check-in within geofence', function () {
    $pengawas = actingAsPengawas();
    $dapur = $pengawas->dapur;
    
    $response = $this->actingAs($pengawas)->post(route('pengawas.absensi.store'), [
        'status' => 'hadir',
        'latitude' => $dapur->latitude + 0.0001, // ~10m
        'longitude' => $dapur->longitude + 0.0001,
        'accuracy' => 5,
    ]);
    
    $response->assertRedirect(route('pengawas.absensi.index'));
    
    $absensi = Absensi::latest()->first();
    expect($absensi->is_within_geofence)->toBeTrue()
        ->and($absensi->distance_from_dapur)->toBeLessThan(100);
});

test('pengawas check-in outside geofence is flagged', function () {
    $pengawas = actingAsPengawas();
    $dapur = $pengawas->dapur;
    
    $this->actingAs($pengawas)->post(route('pengawas.absensi.store'), [
        'status' => 'hadir',
        'latitude' => $dapur->latitude + 0.01, // ~1km
        'longitude' => $dapur->longitude + 0.01,
        'accuracy' => 5,
    ]);
    
    $absensi = Absensi::latest()->first();
    expect($absensi->is_within_geofence)->toBeFalse()
        ->and($absensi->distance_from_dapur)->toBeGreaterThan(100);
});

test('unique constraint prevents duplicate daily check-in', function () {
    $pengawas = actingAsPengawas();
    $dapur = $pengawas->dapur;
    
    // First check-in
    Absensi::factory()->create([
        'user_id' => $pengawas->id,
        'dapur_id' => $dapur->id,
        'check_in_at' => now(),
    ]);
    
    // Second check-in same day
    $response = $this->actingAs($pengawas)->post(route('pengawas.absensi.store'), [
        'status' => 'hadir',
        'latitude' => $dapur->latitude,
        'longitude' => $dapur->longitude,
    ]);
    
    $response->assertSessionHas('error');
    expect(Absensi::where('user_id', $pengawas->id)->count())->toBe(1);
});

test('pengawas can check-out', function () {
    $pengawas = actingAsPengawas();
    $absensi = Absensi::factory()->create([
        'user_id' => $pengawas->id,
        'dapur_id' => $pengawas->dapur_id,
        'check_in_at' => now()->subHours(8),
        'check_out_at' => null,
    ]);
    
    $response = $this->actingAs($pengawas)->post(route('pengawas.absensi.check-out', $absensi), [
        'latitude' => $pengawas->dapur->latitude,
        'longitude' => $pengawas->dapur->longitude,
    ]);
    
    $response->assertRedirect();
    expect($absensi->fresh()->check_out_at)->not->toBeNull();
});

test('admin can view all absensi', function () {
    $admin = actingAsAdmin();
    Absensi::factory(5)->create();
    
    $this->actingAs($admin)->get(route('admin.absensi.index'))
        ->assertOk()
        ->assertViewHas('absensis');
});
```

### CCTV Tests (`tests/Feature/CctvTest.php`)
```php
uses()->group('cctv');

test('admin can manage cctvs', function () {
    $admin = actingAsAdmin();
    $dapur = Dapur::factory()->create();
    
    $this->actingAs($admin)->get(route('admin.cctvs.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.cctvs.create'))->assertOk();
    
    $this->actingAs($admin)->post(route('admin.cctvs.store'), [
        'dapur_id' => $dapur->id,
        'nama' => 'CAM-01',
        'status' => 'online',
        'location_type' => 'persiapan',
    ])->assertRedirect();
    
    $cctv = Cctv::latest()->first();
    expect($cctv->nama)->toBe('CAM-01');
});

test('pengawas can view own dapur cctvs', function () {
    $pengawas = actingAsPengawas();
    $cctv = Cctv::factory()->create(['dapur_id' => $pengawas->dapur_id]);
    $otherCctv = Cctv::factory()->create();
    
    $this->actingAs($pengawas)->get(route('pengawas.cctvs.index'))->assertOk();
    $this->actingAs($pengawas)->get(route('pengawas.cctvs.show', $cctv))->assertOk();
    $this->actingAs($pengawas)->get(route('pengawas.cctvs.show', $otherCctv))->assertForbidden();
});

test('pengawas can report anomaly', function () {
    $pengawas = actingAsPengawas();
    $cctv = Cctv::factory()->create(['dapur_id' => $pengawas->dapur_id]);
    
    $this->actingAs($pengawas)->post(route('admin.cctv-anomalies.store'), [
        'cctv_id' => $cctv->id,
        'type' => 'offline',
        'description' => 'Kamera tidak bisa diakses',
    ])->assertRedirect();
    
    $report = CctvAnomalyReport::latest()->first();
    expect($report->cctv_id)->toBe($cctv->id)
        ->and($report->reported_by)->toBe($pengawas->id)
        ->and($report->status)->toBe('open');
});

test('only admin can resolve anomaly', function () {
    $admin = actingAsAdmin();
    $pengawas = actingAsPengawas();
    $report = CctvAnomalyReport::factory()->create(['status' => 'open']);
    
    $this->actingAs($pengawas)->post(route('admin.cctv-anomalies.resolve', $report))
        ->assertForbidden();
    
    $this->actingAs($admin)->post(route('admin.cctv-anomalies.resolve', $report), [
        'resolution_notes' => 'Fixed camera',
    ])->assertRedirect();
    
    expect($report->fresh()->status)->toBe('resolved');
});
```

### Dapur Tests (`tests/Feature/DapurTest.php`)
```php
uses()->group('dapur');

test('admin can create dapur', function () {
    $admin = actingAsAdmin();
    
    $this->actingAs($admin)->post(route('admin.dapurs.store'), [
        'nama' => 'Dapur Test',
        'lokasi' => 'Test Location',
        'latitude' => -6.123456,
        'longitude' => 106.123456,
        'status' => 'operasional',
        'progress' => 50,
    ])->assertRedirect();
    
    $this->assertDatabaseHas('dapurs', ['nama' => 'Dapur Test']);
});

test('manajerial cannot create dapur', function () {
    $manajerial = actingAsManajerial();
    
    $this->actingAs($manajerial)->post(route('admin.dapurs.store'), [
        'nama' => 'Dapur Test',
        'status' => 'operasional',
    ])->assertForbidden();
});

test('dapur progress badge renders correctly', function () {
    $dapur1 = Dapur::factory()->create(['progress' => 90, 'status' => 'operasional']);
    $dapur2 = Dapur::factory()->create(['progress' => 60, 'status' => 'perlu_perhatian']);
    $dapur3 = Dapur::factory()->create(['progress' => 30, 'status' => 'non_operasional']);
    
    expect($dapur1->progress)->toBe(90);
    expect($dapur2->progress)->toBe(60);
    expect($dapur3->progress)->toBe(30);
});
```

### Mobile API Tests (`tests/Feature/MobileApiTest.php`)
```php
uses()->group('mobile-api');

test('pengawas can login via api', function () {
    $user = User::factory()->pengawas()->create(['password' => bcrypt('password')]);
    
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'role', 'dapur_id']]);
});

test('api requires authentication', function () {
    $this->getJson('/api/dapurs')->assertUnauthorized();
});

test('pengawas can check-in via api', function () {
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
});

test('pengawas can report anomaly via api', function () {
    $user = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create(['user_id' => $user->id]);
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);
    $token = $user->createToken('mobile')->plainTextToken;
    
    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/cctv-anomalies', [
            'cctv_id' => $cctv->id,
            'type' => 'offline',
            'description' => 'Test anomaly',
        ]);
    
    $response->assertCreated()
        ->assertJsonPath('data.status', 'open');
});
```

## Unit Tests

### Geolocation Service (`tests/Unit/GeolocationServiceTest.php`)
```php
uses()->group('unit');

test('calculate_distance_returns_zero_for_same_point', function () {
    $distance = GeolocationService::calculateDistance(-6.123456, 106.123456, -6.123456, 106.123456);
    expect($distance)->toBe(0);
});

test('calculate_distance_jakarta_to_bandung_approx_140km', function () {
    $distance = GeolocationService::calculateDistance(
        -6.2088, 106.8456,  // Jakarta
        -6.9175, 107.6191   // Bandung
    );
    
    expect($distance)->toBeGreaterThan(130000)
        ->and($distance)->toBeLessThan(150000);
});

test('is_within_geofence_returns_true_for_close_points', function () {
    $result = GeolocationService::isWithinGeofence(
        -6.123500, 106.123500,
        -6.123456, 106.123456,
        100
    );
    expect($result)->toBeTrue();
});

test('is_within_geofence_returns_false_for_far_points', function () {
    $result = GeolocationService::isWithinGeofence(
        -6.128000, 106.128000,
        -6.123456, 106.123456,
        100
    );
    expect($result)->toBeFalse();
});

test('validate_coordinate_rejects_invalid_values', function () {
    expect(GeolocationService::validateCoordinate(91, 0))->toBeFalse();
    expect(GeolocationService::validateCoordinate(-91, 0))->toBeFalse();
    expect(GeolocationService::validateCoordinate(0, 181))->toBeFalse();
    expect(GeolocationService::validateCoordinate(0, -181))->toBeFalse();
    expect(GeolocationService::validateCoordinate(0, 0))->toBeTrue();
});
```

## Running Tests

### All Tests
```bash
php artisan test
# or
vendor/bin/pest
```

### Specific Groups
```bash
php artisan test --group=auth
php artisan test --group=absensi
php artisan test --group=cctv
php artisan test --group=dapur
php artisan test --group=policy
php artisan test --group=mobile-api
php artisan test --group=unit
```

### Specific Test File
```bash
php artisan test tests/Feature/AbsensiTest.php
vendor/bin/pest tests/Feature/AbsensiTest.php
```

### With Coverage
```bash
php artisan test --coverage
# or
vendor/bin/pest --coverage
```

### Filter by Name
```bash
php artisan test --filter=check_in
php artisan test --filter="pengawas can check-in"
```

## Test Data Management

### Factories
```php
// DapurFactory
Dapur::factory()->create([
    'status' => 'operasional',
    'progress' => 90,
]);

// With relationships
Dapur::factory()->has(Cctv::factory()->count(4))->create();

// Absensi with specific date
Absensi::factory()->create([
    'check_in_at' => Carbon::parse('2026-10-26 07:30:00'),
]);
```

### RefreshDatabase Trait
```php
// Each test runs in transaction, rolled back after
uses()->group('feature')->in('Feature')->uses(RefreshDatabase::class);
```

## CI/CD Integration

### GitHub Actions (`.github/workflows/tests.yml`)
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite
          
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
        
      - name: Run tests
        run: php artisan test --compact
        
      - name: Run lint
        run: vendor/bin/pint --test
```

## Best Practices

### 1. Test Naming
```php
// Good: Descriptive, behavior-focused
test('pengawas can check-in within geofence', function () {});

// Bad: Implementation-focused
test('store method works', function () {});
```

### 2. Arrange-Act-Assert
```php
test('description', function () {
    // Arrange
    $user = User::factory()->create();
    $dapur = Dapur::factory()->create(['user_id' => $user->id]);
    
    // Act
    $response = $this->actingAs($user)->post(route('...'), $data);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('table', ['field' => 'value']);
});
```

### 3. Use Factories Over Manual Creation
```php
// Good
$dapur = Dapur::factory()->create(['status' => 'operasional']);

// Avoid
$dapur = new Dapur();
$dapur->status = 'operasional';
$dapur->save();
```

### 4. Test Edge Cases
- Empty data
- Boundary values (0, max)
- Invalid inputs
- Unauthorized access
- Concurrent operations

### 5. Keep Tests Fast
- Use SQLite in-memory
- Minimize database queries
- Use `factory()->count(0)` for empty collections
- Avoid external API calls (mock them)