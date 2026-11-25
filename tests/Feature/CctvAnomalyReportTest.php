<?php

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin create anomaly report form preselects cctv from query string', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);

    $this->actingAs($admin)
        ->get(route('admin.cctv-anomalies.create', ['cctv_id' => (string) $cctv->id]))
        ->assertOk()
        ->assertSee((string) $cctv->id, false);
});

test('admin create anomaly report form without query string does not preselect', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.cctv-anomalies.create'))
        ->assertOk();
});

test('non-admin cannot open anomaly report create form', function () {
    $manajerial = User::factory()->create(['role' => 'manajerial']);

    $this->actingAs($manajerial)
        ->get(route('admin.cctv-anomalies.create'))
        ->assertForbidden();
});

test('admin tidak bisa membuat laporan anomali ganda kamera+deskripsi+severity sama hari itu (idempoten)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);

    $payload = [
        'cctv_id' => $cctv->id,
        'description' => 'Gambar kabur, kemungkinan lens kotor.',
        'severity' => 'rendah',
    ];

    $this->actingAs($admin)
        ->post(route('admin.cctv-anomalies.store'), $payload)
        ->assertRedirect(route('admin.cctv-anomalies.index'))
        ->assertSessionHas('status', 'Laporan anomali CCTV berhasil dibuat.');

    // Double submit dengan data yang sama harus tidak membuat duplikat.
    $this->actingAs($admin)
        ->post(route('admin.cctv-anomalies.store'), $payload)
        ->assertRedirect(route('admin.cctv-anomalies.index'))
        ->assertSessionHas('status', 'Laporan anomali untuk kamera ini sudah tersimpan hari ini.');

    expect(CctvAnomalyReport::where('cctv_id', $cctv->id)
        ->where('admin_id', $admin->id)
        ->where('description', 'Gambar kabur, kemungkinan lens kotor.')
        ->where('severity', 'rendah')
        ->where('status', 'baru')
        ->whereDate('created_at', today())
        ->count())->toBe(1);
});
