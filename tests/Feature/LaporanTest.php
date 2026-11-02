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
        ])
        ->assertRedirect(route('pengawas.laporan.index'))
        ->assertSessionHas('success');

    $laporan = Laporan::latest()->first();
    $this->assertNotNull($laporan);
    $this->assertEquals($dapur->id, $laporan->dapur_id);
    $this->assertEquals(6, $laporan->photos->count());
    $this->assertEquals(-6.208800, $laporan->photos->first()->latitude);
    $this->assertEquals(106.845600, $laporan->photos->first()->longitude);
    $this->assertEquals('Jl. A', $laporan->photos->first()->address);
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
        ])
        ->assertSessionHasErrors(['photo_indikator_1', 'photo_selfie', 'latitude', 'longitude', 'address']);
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
        ])
        ->assertSessionHasErrors(['photo_indikator_5']);
});

test('admin can view laporan show with multiple photos', function () {
    $dapur = Dapur::factory()->create();
    $laporan = Laporan::factory()->create([
        'dapur_id' => $dapur->id,
        'status' => 'diperiksa',
        'catatan' => 'Kondisi baik.',
    ]);
    LaporanPhoto::create([
        'laporan_id' => $laporan->id,
        'photo_path' => 'laporans/'.$laporan->id.'/photos/photo-1.jpg',
        'indikator' => 'Kebersihan dan sanitasi',
        'latitude' => -6.208800,
        'longitude' => 106.845600,
        'address' => 'Jl. A',
    ]);
    LaporanPhoto::create([
        'laporan_id' => $laporan->id,
        'photo_path' => 'laporans/'.$laporan->id.'/photos/photo-2.jpg',
        'indikator' => 'Selfie di depan dapur',
        'latitude' => -6.208900,
        'longitude' => 106.845700,
        'address' => 'Jl. B',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.laporan.show', $laporan->id))
        ->assertOk();

    $response->assertSee($laporan->photos->first()->photo_path);
    $response->assertSee(-6.208800);
    $response->assertSee(106.845600);
    $response->assertSee('Jl. A');
});
