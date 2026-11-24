<?php

use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengawas can auto check-in to an assigned dapur', function () {
    $pengawas = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create();
    $pengawas->dapurs()->attach($dapur->id);

    $this->actingAs($pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $dapur->id,
            'lat' => -6.2,
            'lng' => 106.8,
            'trigger' => 'entry',
        ])
        ->assertOk()
        ->assertJson(['status' => 'checked_in']);

    $this->assertDatabaseHas('laporans', [
        'dapur_id' => $dapur->id,
        'created_by' => $pengawas->id,
    ]);
});

test('pengawas cannot auto check-in to a dapur they are not assigned to', function () {
    $pengawas = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create(); // not attached

    $this->actingAs($pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', [
            'dapur_id' => $dapur->id,
            'lat' => -6.2,
            'lng' => 106.8,
            'trigger' => 'entry',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('laporans', 0);
});

test('repeated geofence entry does not create a second laporan', function () {
    $pengawas = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create();
    $pengawas->dapurs()->attach($dapur->id);

    $payload = [
        'dapur_id' => $dapur->id,
        'lat' => -6.2,
        'lng' => 106.8,
        'trigger' => 'entry',
    ];

    $this->actingAs($pengawas, 'sanctum')->postJson('/api/mobile/absensi/auto-checkin', $payload)->assertOk();

    $this->actingAs($pengawas, 'sanctum')
        ->postJson('/api/mobile/absensi/auto-checkin', $payload)
        ->assertOk()
        ->assertJson(['status' => 'already_checked_in']);

    $this->assertDatabaseCount('laporans', 1);
});

test('admin can store an fcm token', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/mobile/fcm-token', [
            'token' => 'token-abc',
            'platform' => 'android',
        ])
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    $this->assertDatabaseHas('fcm_tokens', ['user_id' => $admin->id, 'token' => 'token-abc']);
});
