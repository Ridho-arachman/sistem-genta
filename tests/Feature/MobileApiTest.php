<?php

use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
    $this->dapur = Dapur::factory()->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 100,
    ]);
});

test('authenticated user can store fcm token', function () {
    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/fcm-token', [
            'token' => 'test_fcm_token_123',
            'platform' => 'android',
        ]);

    $response->assertOk()
        ->assertJson(['status' => 'ok']);

    $this->assertDatabaseHas('fcm_tokens', [
        'user_id' => $this->pengawas->id,
        'token' => 'test_fcm_token_123',
        'platform' => 'android',
    ]);
});

test('fcm token validation requires token and platform', function () {
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/fcm-token', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['token', 'platform']);
});

test('fcm token platform must be valid', function () {
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/fcm-token', [
            'token' => 'test_token',
            'platform' => 'invalid',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['platform']);
});

test('duplicate fcm token for same user creates new token', function () {
    FcmToken::create([
        'user_id' => $this->pengawas->id,
        'token' => 'old_token',
        'platform' => 'android',
    ]);

    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/fcm-token', [
            'token' => 'new_token',
            'platform' => 'ios',
        ])
        ->assertOk();

    $this->assertDatabaseHas('fcm_tokens', [
        'user_id' => $this->pengawas->id,
        'token' => 'new_token',
        'platform' => 'ios',
    ]);
    // Old token should still exist (cleanup keeps max 3 per user)
    $this->assertDatabaseHas('fcm_tokens', ['token' => 'old_token']);
});

test('guest cannot access mobile api', function () {
    $this->postJson('/api/mobile/fcm-token', [
        'token' => 'test_token',
        'platform' => 'android',
    ])->assertUnauthorized();
});

test('mobile can get geofences for dapurs', function () {
    Dapur::factory()->count(3)->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 100,
    ]);

    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->getJson('/api/mobile/dapurs/geofences');

    $response->assertOk()
        ->assertJsonStructure(['geofences' => ['*' => ['id', 'identifier', 'latitude', 'longitude', 'radius', 'notifyOnEntry', 'notifyOnExit']]]);

    $geofences = $response->json('geofences');
    $this->assertCount(4, $geofences); // 3 factory + 1 beforeEach
});

test('mobile can sync locations and auto check-in', function () {
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/sync-locations', [
            'locations' => [
                [
                    'lat' => -6.2088,
                    'lng' => 106.8456,
                    'accuracy' => 10,
                    'timestamp' => now()->timestamp,
                    'speed' => 0,
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonStructure(['synced', 'errors']);

    // Should have auto checked in
    $this->assertDatabaseHas('absensis', [
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'status' => 'hadir',
    ]);
});

test('mobile auto checkin via geofence entry', function () {
    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'entry',
        ]);

    $response->assertOk()
        ->assertJson(['status' => 'checked_in'])
        ->assertJsonStructure(['absensi_id']);

    $this->assertDatabaseHas('absensis', [
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'status' => 'hadir',
    ]);
});

test('mobile auto checkin prevents duplicate', function () {
    // First check-in
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'entry',
        ])->assertJson(['status' => 'checked_in']);

    // Second check-in same day
    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'entry',
        ]);

    $response->assertOk()
        ->assertJson(['status' => 'already_checked_in']);
});

test('mobile auto checkout via geofence exit', function () {
    // First check in
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'entry',
        ]);

    // Then exit
    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'exit',
        ]);

    $response->assertOk()
        ->assertJson(['status' => 'checked_out'])
        ->assertJsonStructure(['absensi_id']);

    $this->assertDatabaseHas('absensis', [
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
    ]);

    $absensi = Absensi::latest()->first();
    $this->assertNotNull($absensi->check_out_at);
});

test('mobile auto checkout ignores if no active checkin', function () {
    $response = $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'trigger' => 'exit',
        ]);

    $response->assertOk()
        ->assertJson(['status' => 'no_active_checkin']);
});

test('mobile sync locations validation', function () {
    $this->actingAs($this->pengawas, 'sanctum')
        ->postJson('/api/mobile/sync-locations', [
            'locations' => [
                ['lat' => 999, 'lng' => 999, 'timestamp' => now()->timestamp],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['locations.0.lat', 'locations.0.lng']);
});

test('dapur geofence radius validation', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Test Dapur',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'geofence_radius' => 5, // too small (min 10)
        ])
        ->assertSessionHasErrors('geofence_radius');

    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Test Dapur',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'geofence_radius' => 10000, // too large (max 5000)
        ])
        ->assertSessionHasErrors('geofence_radius');

    // Valid radius
    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Test Dapur Valid',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'geofence_radius' => 500,
        ])
        ->assertRedirect(route('admin.dapurs.index'));
});
