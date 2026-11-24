<?php

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dashboard shows real dapur camera progress and status', function () {
    $admin = User::factory()->admin()->create();

    $dapur = Dapur::factory()->create(['nama' => 'Dapur Uji']);
    Cctv::factory()->create(['dapur_id' => $dapur->id, 'status' => 'online']);
    Cctv::factory()->create(['dapur_id' => $dapur->id, 'status' => 'offline']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('50%')
        ->assertSee('Perlu_perhatian');
});

test('dapur with every camera online shows full progress', function () {
    $admin = User::factory()->admin()->create();

    $dapur = Dapur::factory()->create();
    Cctv::factory()->count(2)->create(['dapur_id' => $dapur->id, 'status' => 'online']);

    expect($dapur->fresh()->progress)->toBe(100)
        ->and($dapur->fresh()->status)->toBe('operasional');
});
