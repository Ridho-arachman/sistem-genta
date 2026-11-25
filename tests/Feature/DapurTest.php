<?php

use App\Models\Cctv;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->pengawas = User::factory()->create(['role' => 'pengawas']);
});

test('admin can view dapur index', function () {
    Dapur::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.index'))
        ->assertOk()
        ->assertSee('Data Dapur');
});

test('admin can view dapur create form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.create'))
        ->assertOk()
        ->assertSee('Tambah Dapur Baru');
});

test('admin can view dapur edit form', function () {
    $dapur = Dapur::factory()->create(['kecamatan' => 'Curug', 'kabupaten_kota' => 'Kota Serang', 'provinsi' => 'Banten']);

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.edit', $dapur))
        ->assertOk()
        ->assertSee('Edit Dapur: '.$dapur->nama);
});

test('pengawas cannot view dapur index via web', function () {
    Dapur::factory()->count(3)->create();

    $this->actingAs($this->pengawas)
        ->get(route('admin.dapurs.index'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});

test('admin can create dapur', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Dapur Utama',
            'alamat' => 'Jl. Raya Curug No. 1',
            'provinsi' => 'Banten',
            'kabupaten_kota' => 'Kota Serang',
            'kecamatan' => 'Curug',
        ])
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur berhasil ditambahkan.');

    $this->assertDatabaseHas('dapurs', [
        'nama' => 'Dapur Utama',
        'provinsi' => 'Banten',
        'kabupaten_kota' => 'Kota Serang',
    ]);
});

test('pengawas cannot create dapur via web', function () {
    $this->actingAs($this->pengawas)
        ->post(route('admin.dapurs.store'), [
            'nama' => 'Dapur Utama',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});

test('admin can update dapur', function () {
    $dapur = Dapur::factory()->create([
        'nama' => 'Dapur Lama',
        'provinsi' => 'Banten',
        'kabupaten_kota' => 'Kota Serang',
        'kecamatan' => 'Curug',
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.dapurs.update', $dapur), [
            'nama' => 'Dapur Baru',
            'alamat' => 'Jl. Raya Citangkil No. 5',
            'provinsi' => 'Banten',
            'kabupaten_kota' => 'Kota Cilegon',
            'kecamatan' => 'Citangkil',
        ])
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur berhasil diperbarui.');

    $this->assertDatabaseHas('dapurs', [
        'id' => $dapur->id,
        'nama' => 'Dapur Baru',
        'kabupaten_kota' => 'Kota Cilegon',
    ]);
});

test('pengawas cannot update dapur via web', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->put(route('admin.dapurs.update', $dapur), [
            'nama' => 'Dapur Baru',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});

test('admin can soft delete dapur', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.dapurs.destroy', $dapur))
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur dan CCTV terkait berhasil diarsipkan.');

    $this->assertSoftDeleted('dapurs', ['id' => $dapur->id]);
});

test('soft deleting dapur cascades soft delete to its cctvs', function () {
    $dapur = Dapur::factory()->create();
    $cctv1 = Cctv::factory()->create(['dapur_id' => $dapur->id]);
    $cctv2 = Cctv::factory()->create(['dapur_id' => $dapur->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.dapurs.destroy', $dapur))
        ->assertRedirect(route('admin.dapurs.index'))
        ->assertSessionHas('status', 'Dapur dan CCTV terkait berhasil diarsipkan.');

    $this->assertSoftDeleted('dapurs', ['id' => $dapur->id]);
    $this->assertSoftDeleted('cctvs', ['id' => $cctv1->id]);
    $this->assertSoftDeleted('cctvs', ['id' => $cctv2->id]);
});

test('admin can view dapur archive', function () {
    $dapur = Dapur::factory()->create();
    $dapur->delete();

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.archive'))
        ->assertOk()
        ->assertSee($dapur->nama);
});

test('admin can restore dapur from archive', function () {
    $dapur = Dapur::factory()->create();
    $dapur->delete();

    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.restore', $dapur->id))
        ->assertRedirect(route('admin.dapurs.archive'))
        ->assertSessionHas('status', 'Dapur berhasil dipulihkan.');

    $this->assertDatabaseHas('dapurs', ['id' => $dapur->id, 'deleted_at' => null]);
});

test('restoring dapur cascades restore to its cascaded cctvs', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);
    $dapur->delete(); // cascades soft delete to $cctv

    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.restore', $dapur->id))
        ->assertRedirect(route('admin.dapurs.archive'))
        ->assertSessionHas('status', 'Dapur berhasil dipulihkan.');

    $this->assertDatabaseHas('dapurs', ['id' => $dapur->id, 'deleted_at' => null]);
    $this->assertDatabaseHas('cctvs', ['id' => $cctv->id, 'deleted_at' => null]);
});

test('restoring dapur does not restore cctv archived manually before dapur', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);
    $cctv->delete(); // manually archived cctv earlier
    Cctv::withTrashed()->where('id', $cctv->id)->update(['deleted_at' => now()->subHour()]); // jelas lebih lama
    $dapur->delete(); // cascades soft delete to dapur only

    $this->actingAs($this->admin)
        ->post(route('admin.dapurs.restore', $dapur->id))
        ->assertRedirect(route('admin.dapurs.archive'));

    $this->assertDatabaseHas('dapurs', ['id' => $dapur->id, 'deleted_at' => null]);
    $this->assertSoftDeleted('cctvs', ['id' => $cctv->id]);
});

test('force delete dapur force deletes its cctvs', function () {
    config(['cctv.mediamtx.api' => 'http://mediamtx.test:9997']);
    Http::fake(['mediamtx.test:9997/*' => Http::response([], 200)]);

    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id, 'stream_key' => 'stream-key-1']);
    $dapur->delete(); // cascades soft delete

    $this->actingAs($this->admin)
        ->delete(route('admin.dapurs.force', $dapur->id))
        ->assertRedirect(route('admin.dapurs.archive'))
        ->assertSessionHas('status', 'Dapur berhasil dihapus permanen.');

    $this->assertDatabaseMissing('dapurs', ['id' => $dapur->id]);
    $this->assertDatabaseMissing('cctvs', ['id' => $cctv->id]);
    Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/config/paths/delete/'));
});

test('pengawas cannot delete dapur via web', function () {
    $dapur = Dapur::factory()->create();

    $this->actingAs($this->pengawas)
        ->delete(route('admin.dapurs.destroy', $dapur))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});

test('dapur show loads cctvs relationship', function () {
    $dapur = Dapur::factory()->create();
    $cctv = Cctv::factory()->create(['dapur_id' => $dapur->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.dapurs.show', $dapur))
        ->assertOk()
        ->assertSee($cctv->nama);
});
