<?php

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Halaman yang dibaca manajerial/kepala-dapur memakai view admin yang sama,
 * jadi link & aksi di dalamnya harus memakai prefix rute aktif — bukan `admin.*`
 * yang hanya bisa dibuka role admin.
 */
function normalizeHtml(string $html): string
{
    // Blade/JSON escape forward slash di dalam atribut Alpine (@js), bandingkan wajar.
    return str_replace('\\/', '/', $html);
}

function assertNoAdminUrl(string $html): void
{
    $html = normalizeHtml($html);

    expect($html)
        ->not->toContain('href="/admin/')
        ->not->toContain('action="/admin/')
        ->not->toContain('route(\'admin.');
}

test('manajerial dapur list links to manajerial dapur detail', function () {
    $dapur = Dapur::factory()->create();
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.dapurs.index'));

    $response->assertOk()->assertSee($dapur->nama);
    expect(normalizeHtml($response->getContent()))
        ->toContain(route('manajerial.dapurs.show', ':id', false));
    assertNoAdminUrl($response->getContent());
});

test('manajerial cctv-anomalies index shows no admin links or update form', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id, 'nama' => 'Kamera Depan']);
    CctvAnomalyReport::factory()->create(['cctv_id' => $cctv->id]);
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.cctv-anomalies.index'));

    $response->assertOk()
        ->assertSee('Kamera Depan')
        ->assertDontSee('Buat Laporan');
    assertNoAdminUrl($response->getContent());
});

test('manajerial cctv-anomalies show renders content without update form', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id, 'nama' => 'Kamera Belakang']);
    $anomaly = CctvAnomalyReport::factory()->create(['cctv_id' => $cctv->id, 'description' => 'Kabel terputus']);
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.cctv-anomalies.show', $anomaly));

    $response->assertOk()
        ->assertSee('Kabel terputus')
        ->assertSee(route('manajerial.cctv-anomalies.index', [], false), false)
        ->assertDontSee('method="POST"');
    assertNoAdminUrl($response->getContent());
});

test('kepala-dapur read-only anomaly page hides admin-only actions', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id, 'nama' => 'Kamera Dapur']);
    CctvAnomalyReport::factory()->create(['cctv_id' => $cctv->id]);
    $kepalaDapur = User::factory()->create(['role' => 'kepala_dapur']);

    $response = $this->actingAs($kepalaDapur)->get(route('kepala-dapur.cctv-anomalies.index'));

    $response->assertOk()
        ->assertSee('Kamera Dapur')
        ->assertDontSee('Buat Laporan')
        ->assertDontSee('>PDF<');
    assertNoAdminUrl($response->getContent());
});

test('read-only anomaly empty state hides admin create link', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.cctv-anomalies.index'));

    $response->assertOk()
        ->assertSee('Belum Ada Laporan Anomali')
        ->assertDontSee('Buat Laporan Pertama');
    assertNoAdminUrl($response->getContent());
});

test('manajerial cctv list links to manajerial cctv detail', function () {
    $dapur = Dapur::factory()->create();
    Cctv::factory()->create(['dapur_id' => $dapur->id, 'nama' => 'Kamera Sisi']);
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.cctvs.index'));

    $response->assertOk()
        ->assertSee('Kamera Sisi')
        ->assertDontSee('Laporkan anomali');
    assertNoAdminUrl($response->getContent());
});

test('kepala-dapur cctv list only shows assigned dapur', function () {
    $dapur = Dapur::factory()->create();
    $lain = Dapur::factory()->create(['nama' => 'Dapur Lain']);
    Cctv::factory()->create(['dapur_id' => $dapur->id, 'nama' => 'Kamera Depan']);
    Cctv::factory()->create(['dapur_id' => $lain->id, 'nama' => 'Kamera Dapur Lain']);
    $kepalaDapur = User::factory()->create(['role' => 'kepala_dapur', 'dapur_id' => $dapur->id]);

    $response = $this->actingAs($kepalaDapur)->get(route('kepala-dapur.cctvs.index'));

    $response->assertOk()
        ->assertSee('Kamera Depan')
        ->assertDontSee('Kamera Dapur Lain');
    assertNoAdminUrl($response->getContent());
});

test('manajerial inspeksi list hides laporan detail link', function () {
    Dapur::factory()->create();
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $response = $this->actingAs($manajerial)->get(route('manajerial.inspeksi.index'));

    $response->assertOk()->assertSee('Inspeksi Dapur');
    assertNoAdminUrl($response->getContent());
});
