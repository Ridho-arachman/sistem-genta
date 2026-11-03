<?php

use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\LaporanPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
});

test('pengawas can create laporan with 6 photos and GPS via mobile app', function () {
    $dapur = Dapur::factory()->create();

    // Assign dapur to pengawas via dapur_user pivot
    $this->pengawas->dapurs()->attach($dapur->id);

    $photoBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'kategori' => 'Kebersihan dan sanitasi',
            'status' => 'diperiksa',
            'catatan' => 'Kondisi dapur baik.',
            'photo_indikator_1' => $photoBase64,
            'photo_indikator_2' => $photoBase64,
            'photo_indikator_3' => $photoBase64,
            'photo_indikator_4' => $photoBase64,
            'photo_indikator_5' => $photoBase64,
            'photo_selfie' => $photoBase64,
            'latitude' => [-6.208800, -6.208900, -6.209000, -6.209100, -6.209200, -6.209300],
            'longitude' => [106.845600, 106.845700, 106.845800, 106.845900, 106.846000, 106.846100],
            'address' => ['Jl. A', 'Jl. B', 'Jl. C', 'Jl. D', 'Jl. E', 'Jl. F'],
            'hasil' => ['diperiksa', 'diperiksa', 'diperiksa', 'diperiksa', 'diperiksa'],
        ])
        ->assertRedirect(route('pengawas.laporan.index'))
        ->assertSessionHas('success');

    $laporan = Laporan::latest()->first();
    $this->assertNotNull($laporan);
    $this->assertEquals($dapur->id, $laporan->dapur_id);
    $this->assertEquals(-6.208800, $laporan->latitude);
    $this->assertEquals(106.845600, $laporan->longitude);
    $this->assertEquals('Jl. A', $laporan->address);
    $this->assertEquals(6, $laporan->photos->count());
    $this->assertNotNull($laporan->photos->first()->photo_path);
    $this->assertNotNull($laporan->photos->first()->indikator);
});

test('pengawas cannot create laporan without photos', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'photo_indikator_1' => '',
            'photo_indikator_2' => '',
            'photo_indikator_3' => '',
            'photo_indikator_4' => '',
            'photo_indikator_5' => '',
            'photo_selfie' => '',
            'latitude' => [],
            'longitude' => [],
            'address' => [],
            'hasil' => ['', '', '', '', ''],
        ])
        ->assertSessionHasErrors(['photo_indikator_1', 'photo_selfie', 'latitude', 'longitude', 'address', 'hasil.0', 'hasil.1', 'hasil.2', 'hasil.3', 'hasil.4']);
});

test('pengawas cannot create laporan with incomplete photos', function () {
    $dapur = Dapur::factory()->create();

    $photoBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'photo_indikator_1' => $photoBase64,
            'photo_indikator_2' => $photoBase64,
            'photo_indikator_3' => $photoBase64,
            'photo_indikator_4' => $photoBase64,
            'photo_indikator_5' => '',
            'photo_selfie' => $photoBase64,
            'latitude' => [-6.208800, -6.208900, -6.209000, -6.209100, -6.209200, -6.209300],
            'longitude' => [106.845600, 106.845700, 106.845800, 106.845900, 106.846000, 106.846100],
            'address' => ['Jl. A', 'Jl. B', 'Jl. C', 'Jl. D', 'Jl. E', 'Jl. F'],
            'hasil' => ['diperiksa', 'diperiksa', 'diperiksa', 'diperiksa', 'diperiksa'],
        ])
        ->assertSessionHasErrors(['photo_indikator_5']);
});

test('admin can view laporan show with multiple photos', function () {
    $dapur = Dapur::factory()->create();
    $laporan = Laporan::factory()->create([
        'dapur_id' => $dapur->id,
        'status' => 'diperiksa',
        'catatan' => 'Kondisi baik.',
        'latitude' => -6.208800,
        'longitude' => 106.845600,
        'address' => 'Jl. A',
    ]);
    LaporanPhoto::create([
        'laporan_id' => $laporan->id,
        'photo_path' => 'laporans/'.$laporan->id.'/photos/photo-1.jpg',
        'indikator' => 'Kebersihan dan sanitasi',
    ]);
    LaporanPhoto::create([
        'laporan_id' => $laporan->id,
        'photo_path' => 'laporans/'.$laporan->id.'/photos/photo-2.jpg',
        'indikator' => 'Selfie di depan dapur',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.laporan.show', $laporan->id))
        ->assertOk();

    $response->assertSee($laporan->photos->first()->photo_path);
    $this->assertEquals(-6.2088, $laporan->latitude);
    $this->assertEquals(106.8456, $laporan->longitude);
});
