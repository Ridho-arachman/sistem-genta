<?php

use App\Models\Cctv;
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
