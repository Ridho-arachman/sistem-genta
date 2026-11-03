<?php

use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\LaporanHasil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
});

test('pengawas can create laporan with single photo and GPS via mobile app', function () {
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
            'selfie_photo' => $photoBase64,
            'latitude' => -6.208800,
            'longitude' => 106.845600,
            'address' => 'Jl. A',
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
    $this->assertNotNull($laporan->selfie_photo);
    $this->assertEquals(5, $laporan->hasil->count());
    // Verify each indikator has a hasil record with 'diperiksa' value
    $this->assertEquals('diperiksa', $laporan->hasil->first()->hasil);
});

test('pengawas cannot create laporan without photo', function () {
    $dapur = Dapur::factory()->create();

    // Assign dapur to pengawas via dapur_user pivot
    $this->pengawas->dapurs()->attach($dapur->id);

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'selfie_photo' => '',
            'latitude' => '',
            'longitude' => '',
            'address' => '',
        ])
        ->assertSessionHasErrors(['selfie_photo', 'latitude', 'longitude', 'address']);
});

test('pengawas cannot create laporan with incomplete hasil', function () {
    $dapur = Dapur::factory()->create();

    // Assign dapur to pengawas via dapur_user pivot
    $this->pengawas->dapurs()->attach($dapur->id);

    $photoBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'selfie_photo' => $photoBase64,
            'latitude' => -6.208800,
            'longitude' => 106.845600,
            'address' => 'Jl. A',
            'hasil' => ['diperiksa', 'diperiksa', 'diperiksa', 'diperiksa'], // only 4, missing 1
        ])
        ->assertSessionHasErrors(['hasil']);
});

test('admin can view laporan show with single photo', function () {
    $dapur = Dapur::factory()->create();
    $laporan = Laporan::factory()->create([
        'dapur_id' => $dapur->id,
        'status' => 'diperiksa',
        'catatan' => 'Kondisi baik.',
        'latitude' => -6.208800,
        'longitude' => 106.845600,
        'address' => 'Jl. A',
        'selfie_photo' => 'laporans/1/photos/photo-1.jpg',
    ]);
    
    $indikators = [
        'Kebersihan dan sanitasi',
        'Penyimpanan bahan',
        'Proses pengolahan',
        'Distribusi makanan',
        'Peralatan dan fasilitas',
    ];
    
    foreach ($indikators as $indikator) {
        LaporanHasil::create([
            'laporan_id' => $laporan->id,
            'indikator' => $indikator,
            'hasil' => 'diperiksa',
        ]);
    }

    $response = $this->actingAs($this->admin)
        ->get(route('admin.laporan.show', $laporan->id))
        ->assertOk();

    $response->assertSee($laporan->selfie_photo);
    $this->assertEquals(-6.2088, $laporan->latitude);
    $this->assertEquals(106.8456, $laporan->longitude);
});