<?php

use App\Models\Absensi;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
    Storage::fake('public');
});

test('pengawas can view absensi index', function () {
    $dapur = Dapur::factory()->create();
    // Create 3 records with different dapur to avoid unique constraint
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'created_at' => now()->subMinutes(3),
    ]);
    $dapur2 = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur2->id,
        'created_at' => now()->subMinutes(2),
    ]);
    $dapur3 = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur3->id,
        'created_at' => now()->subMinutes(1),
    ]);

    $this->actingAs($this->pengawas)
        ->get(route('pengawas.absensi.index'))
        ->assertOk()
        ->assertSee('Absensi Saya');
});

test('admin can view absensi index', function () {
    $dapur = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'created_at' => now()->subMinutes(3),
    ]);
    $dapur2 = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur2->id,
        'created_at' => now()->subMinutes(2),
    ]);
    $dapur3 = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur3->id,
        'created_at' => now()->subMinutes(1),
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.absensi.index'))
        ->assertOk();
});

test('pengawas can create absensi check-in', function () {
    $dapur = Dapur::factory()->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 10000,
    ]);
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.store'), [
            'dapur_id' => $dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
            'selfie_path' => $selfie,
        ])
        ->assertRedirect(route('pengawas.absensi.index'))
        ->assertSessionHas('status', 'Absensi check-in berhasil.');

    $this->assertDatabaseHas('absensis', [
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'status' => 'hadir',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);
});

test('absensi check-in stores check_in_at timestamp', function () {
    $dapur = Dapur::factory()->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 10000,
    ]);
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.store'), [
            'dapur_id' => $dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
            'selfie_path' => $selfie,
        ]);

    $absensi = Absensi::latest()->first();
    $this->assertNotNull($absensi->check_in_at);
    $this->assertNull($absensi->check_out_at);
});

test('absensi validation requires dapur_id', function () {
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.store'), [
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
            'selfie_path' => $selfie,
        ])
        ->assertSessionHasErrors('dapur_id');
});

test('absensi validation requires valid lat/lng', function () {
    $dapur = Dapur::factory()->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 10000,
    ]);
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.store'), [
            'dapur_id' => $dapur->id,
            'lat' => 999,
            'lng' => 999,
            'status' => 'hadir',
            'selfie_path' => $selfie,
        ])
        ->assertSessionHasErrors(['lat', 'lng']);
});

test('absensi validation requires valid status', function () {
    $dapur = Dapur::factory()->create([
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'geofence_radius' => 10000,
    ]);
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.store'), [
            'dapur_id' => $dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'invalid',
            'selfie_path' => $selfie,
        ])
        ->assertSessionHasErrors('status');
});

test('user can check-out own absensi', function () {
    $dapur = Dapur::factory()->create();
    $absensi = Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'check_in_at' => now()->subHours(2),
    ]);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.checkout', $absensi))
        ->assertRedirect()
        ->assertSessionHas('status', 'Check-out berhasil dicatat.');

    $this->assertDatabaseHas('absensis', [
        'id' => $absensi->id,
        'check_out_at' => $absensi->fresh()->check_out_at,
    ]);
});

test('user cannot check-out other user absensi', function () {
    $dapur = Dapur::factory()->create();
    $otherUser = User::factory()->create(['role' => 'pengawas']);
    $absensi = Absensi::factory()->create([
        'user_id' => $otherUser->id,
        'dapur_id' => $dapur->id,
    ]);

    $this->actingAs($this->pengawas)
        ->post(route('pengawas.absensi.checkout', $absensi))
        ->assertForbidden();
});

test('admin can check-out any absensi', function () {
    $dapur = Dapur::factory()->create();
    $absensi = Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'check_in_at' => now()->subHours(2),
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.absensi.checkout', $absensi))
        ->assertRedirect()
        ->assertSessionHas('status', 'Check-out berhasil dicatat.');
});

test('absensi export csv', function () {
    $dapur = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.absensi.export', ['format' => 'csv']));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
        ->assertHeader('Content-Disposition', 'attachment; filename="laporan-absensi-'.now()->format('Ymd_His').'.csv"');
});

test('absensi export xlsx', function () {
    $dapur = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.absensi.export', ['format' => 'xlsx']));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('absensi export pdf view', function () {
    $dapur = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.absensi.export', ['format' => 'pdf']));

    $response->assertOk();
});

test('absensi filter by dapur', function () {
    $dapur1 = Dapur::factory()->create();
    $dapur2 = Dapur::factory()->create(['nama' => 'Dapur Kedua']);
    Absensi::factory()->create(['user_id' => $this->pengawas->id, 'dapur_id' => $dapur1->id]);
    Absensi::factory()->create(['user_id' => $this->pengawas->id, 'dapur_id' => $dapur2->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.absensi.index', ['dapur_id' => $dapur1->id]));

    $response->assertOk();
    $absensis = $response->viewData('absensis');
    $this->assertEquals(1, $absensis->total());
});

test('absensi filter by date range', function () {
    $dapur = Dapur::factory()->create();
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'created_at' => now()->subDays(10),
    ]);
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $dapur->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.absensi.index', [
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));

    $response->assertOk();
    $absensis = $response->viewData('absensis');
    $this->assertEquals(1, $absensis->total());
});

test('absensi status enum values', function () {
    foreach (['hadir', 'izin', 'cuti', 'tidak_hadir'] as $status) {
        $dapur = Dapur::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'geofence_radius' => 10000,
        ]);
        $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

        $this->actingAs($this->pengawas)
            ->post(route('pengawas.absensi.store'), [
                'dapur_id' => $dapur->id,
                'lat' => -6.2088,
                'lng' => 106.8456,
                'status' => $status,
                'selfie_path' => $selfie,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('absensis', ['status' => $status]);
    }
});
