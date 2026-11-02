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

test('pengawas can create laporan with multiple photos and GPS via mobile app', function () {
    $dapur = Dapur::factory()->create();

    $photo1 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    $photo2 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $response = $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'kategori' => 'Kebersihan dan sanitasi',
            'status' => 'diperiksa',
            'catatan' => 'Kondisi dapur baik.',
            'selfie_photo' => [$photo1, $photo2],
            'latitude' => [-6.208800, -6.208900],
            'longitude' => [106.845600, 106.845700],
        ])
        ->assertRedirect(route('pengawas.laporan.index'))
        ->assertSessionHas('success');

    $laporan = Laporan::latest()->first();
    $this->assertNotNull($laporan);
    $this->assertEquals($dapur->id, $laporan->dapur_id);
    $this->assertEquals(2, $laporan->photos->count());
    $this->assertEquals(-6.208800, $laporan->photos->first()->latitude);
    $this->assertEquals(106.845600, $laporan->photos->first()->longitude);
    $this->assertEquals(-6.208900, $laporan->photos->last()->latitude);
    $this->assertEquals(106.845700, $laporan->photos->last()->longitude);
    $this->assertNotNull($laporan->photos->first()->photo_path);
});

test('pengawas cannot create laporan without photos', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'selfie_photo' => [],
            'latitude' => [],
            'longitude' => [],
        ])
        ->assertSessionHasErrors('selfie_photo');
});

test('pengawas cannot create laporan with more than 6 photos', function () {
    $dapur = Dapur::factory()->create();

    $photos = [];
    for ($i = 0; $i < 7; $i++) {
        $photos[] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }

    $this->actingAs($this->pengawas)
        ->withCookie('capacitor_app', '1')
        ->post(route('pengawas.laporan.store'), [
            'dapur_id' => $dapur->id,
            'status' => 'diperiksa',
            'selfie_photo' => $photos,
            'latitude' => array_fill(0, 7, -6.208800),
            'longitude' => array_fill(0, 7, 106.845600),
        ])
        ->assertSessionHasErrors('selfie_photo');
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
        'latitude' => -6.208800,
        'longitude' => 106.845600,
    ]);
    LaporanPhoto::create([
        'laporan_id' => $laporan->id,
        'photo_path' => 'laporans/'.$laporan->id.'/photos/photo-2.jpg',
        'latitude' => -6.208900,
        'longitude' => 106.845700,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.laporan.show', $laporan->id))
        ->assertOk();

    $response->assertSee($laporan->photos->first()->photo_path);
    $response->assertSee(-6.208800);
    $response->assertSee(106.845600);
});
