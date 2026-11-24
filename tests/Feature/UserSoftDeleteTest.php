<?php

use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('restored pengawas keeps their dapur assignment', function () {
    $admin = User::factory()->admin()->create();
    $pengawas = User::factory()->pengawas()->create();
    $dapur = Dapur::factory()->create();
    $pengawas->dapurs()->attach($dapur->id);

    $this->actingAs($admin)->delete(route('admin.users.destroy', $pengawas));
    $this->actingAs($admin)->post(route('admin.users.restore', $pengawas->id));

    expect($pengawas->fresh()->dapurs->pluck('id')->map(fn ($id) => (string) $id)->all())->toBe([(string) $dapur->id]);
});

test('admin dashboard does not count soft-deleted pengawas as without dapur', function () {
    $admin = User::factory()->admin()->create();

    $assigned = User::factory()->pengawas()->create();
    $assigned->dapurs()->attach(Dapur::factory()->create());

    $deleted = User::factory()->pengawas()->create();
    $deleted->delete();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', fn (array $stats) => $stats['pengawas_without_dapur'] === 0
            && $stats['total_pengawas'] === 1);
});
