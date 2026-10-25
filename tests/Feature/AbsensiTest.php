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
    $this->dapur = Dapur::factory()->create();
    Storage::fake('public');
});

test('pengawas can view absensi index', function () {
    Absensi::factory()->count(3)->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
    ]);

    $this->actingAs($this->pengawas)
        ->get(route('absensi.index'))
        ->assertOk()
        ->assertSee('Laporan Absensi');
});

test('admin can view absensi index', function () {
    Absensi::factory()->count(3)->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('absensi.index'))
        ->assertOk();
});

test('pengawas can create absensi check-in', function () {
    $selfie = UploadedFile::fake()->image('selfie.jpg', 800, 600);

    $this->actingAs($this->pengawas)
        ->post(route('absensi.store'), [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
            'selfie_path' => $selfie,
        ])
        ->assertRedirect(route('absensi.index'))
        ->assertSessionHas('status', 'Absensi check-in berhasil.');

    $this->assertDatabaseHas('absensis', [
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'status' => 'hadir',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);
});

test('absensi check-in stores check_in_at timestamp', function () {
    $this->actingAs($this->pengawas)
        ->post(route('absensi.store'), [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
        ]);

    $absensi = Absensi::latest()->first();
    $this->assertNotNull($absensi->check_in_at);
    $this->assertNull($absensi->check_out_at);
});

test('absensi validation requires dapur_id', function () {
    $this->actingAs($this->pengawas)
        ->post(route('absensi.store'), [
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'hadir',
        ])
        ->assertSessionHasErrors('dapur_id');
});

test('absensi validation requires valid lat/lng', function () {
    $this->actingAs($this->pengawas)
        ->post(route('absensi.store'), [
            'dapur_id' => $this->dapur->id,
            'lat' => 999,
            'lng' => 999,
            'status' => 'hadir',
        ])
        ->assertSessionHasErrors(['lat', 'lng']);
});

test('absensi validation requires valid status', function () {
    $this->actingAs($this->pengawas)
        ->post(route('absensi.store'), [
            'dapur_id' => $this->dapur->id,
            'lat' => -6.2088,
            'lng' => 106.8456,
            'status' => 'invalid',
        ])
        ->assertSessionHasErrors('status');
});

test('user can check-out own absensi', function () {
    $absensi = Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'check_in_at' => now()->subHours(2),
    ]);

    $this->actingAs($this->pengawas)
        ->post(route('absensi.checkout', $absensi))
        ->assertRedirect()
        ->assertSessionHas('status', 'Check-out berhasil dicatat.');

    $this->assertDatabaseHas('absensis', [
        'id' => $absensi->id,
        'check_out_at' => $absensi->fresh()->check_out_at,
    ]);
});

test('user cannot check-out other user absensi', function () {
    $otherUser = User::factory()->create(['role' => 'pengawas']);
    $absensi = Absensi::factory()->create([
        'user_id' => $otherUser->id,
        'dapur_id' => $this->dapur->id,
    ]);

    $this->actingAs($this->pengawas)
        ->post(route('absensi.checkout', $absensi))
        ->assertForbidden();
});

test('admin can check-out any absensi', function () {
    $absensi = Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'check_in_at' => now()->subHours(2),
    ]);

    $this->actingAs($this->admin)
        ->post(route('absensi.checkout', $absensi))
        ->assertRedirect()
        ->assertSessionHas('status', 'Check-out berhasil dicatat.');
});

test('absensi export csv', function () {
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('absensi.export', ['format' => 'csv']));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
        ->assertHeader('Content-Disposition', 'attachment; filename="laporan-absensi-'.now()->format('Ymd_His').'.csv"');
});

test('absensi export xlsx', function () {
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('absensi.export', ['format' => 'xlsx']));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('absensi export pdf view', function () {
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('absensi.export', ['format' => 'pdf']));

    $response->assertOk();
});

test('absensi filter by dapur', function () {
    $dapur2 = Dapur::factory()->create(['nama' => 'Dapur Kedua']);
    Absensi::factory()->create(['user_id' => $this->pengawas->id, 'dapur_id' => $this->dapur->id]);
    Absensi::factory()->create(['user_id' => $this->pengawas->id, 'dapur_id' => $dapur2->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('absensi.index', ['dapur_id' => $this->dapur->id]));

    $response->assertOk();
    $absensis = $response->viewData('absensis');
    $this->assertEquals(1, $absensis->total());
});

test('absensi filter by date range', function () {
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'created_at' => now()->subDays(10),
    ]);
    Absensi::factory()->create([
        'user_id' => $this->pengawas->id,
        'dapur_id' => $this->dapur->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('absensi.index', [
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));

    $response->assertOk();
    $absensis = $response->viewData('absensis');
    $this->assertEquals(1, $absensis->total());
});

test('absensi status enum values', function () {
    foreach (['hadir', 'izin', 'cuti', 'tidak_hadir'] as $status) {
        $this->actingAs($this->pengawas)
            ->post(route('absensi.store'), [
                'dapur_id' => $this->dapur->id,
                'lat' => -6.2088,
                'lng' => 106.8456,
                'status' => $status,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('absensis', ['status' => $status]);

        // Clean up for next iteration
        Absensi::where('status', $status)->delete();
    }
});
