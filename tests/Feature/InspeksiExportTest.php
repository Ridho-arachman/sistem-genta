<?php

use App\Exports\LaporanXlsxExport;
use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('xlsx export contains every laporan for admin', function () {
    $admin = User::factory()->admin()->create();
    $dapur = Dapur::factory()->create();

    Laporan::factory()->count(2)->create(['dapur_id' => $dapur->id]);

    $response = $this->actingAs($admin)
        ->get(route('admin.inspeksi.export', ['format' => 'xlsx']))
        ->assertOk();

    expect($response->headers->get('content-type'))->toContain('spreadsheetml');
    expect(substr_count(sheetFromZip($response->getContent()), '<row>'))->toBe(3); // header + 2 laporan
});

test('xlsx export scoped to one creator only contains their laporan', function () {
    $pengawas = User::factory()->pengawas()->create(['name' => 'Pengawas Sendiri']);
    $other = User::factory()->pengawas()->create(['name' => 'Pengawas Lain']);
    $dapur = Dapur::factory()->create();

    Laporan::factory()->create(['created_by' => $pengawas->id, 'dapur_id' => $dapur->id, 'status' => 'diperiksa']);
    Laporan::factory()->create(['created_by' => $other->id, 'dapur_id' => $dapur->id, 'status' => 'temuan']);

    $sheet = sheetFromZip((new LaporanXlsxExport(null, null, null, $pengawas->id))->generate());

    expect($sheet)->toContain('Pengawas Sendiri')
        ->and($sheet)->not->toContain('Pengawas Lain');
});

test('unscoped xlsx export contains every creator', function () {
    $pengawas = User::factory()->pengawas()->create(['name' => 'Pengawas Sendiri']);
    $other = User::factory()->pengawas()->create(['name' => 'Pengawas Lain']);
    $dapur = Dapur::factory()->create();

    Laporan::factory()->create(['created_by' => $pengawas->id, 'dapur_id' => $dapur->id, 'status' => 'diperiksa']);
    Laporan::factory()->create(['created_by' => $other->id, 'dapur_id' => $dapur->id, 'status' => 'temuan']);

    $sheet = sheetFromZip((new LaporanXlsxExport)->generate());

    expect($sheet)->toContain('Pengawas Sendiri')
        ->and($sheet)->toContain('Pengawas Lain');
});

function sheetFromZip(string $zipContent): string
{
    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
    file_put_contents($tempFile, $zipContent);

    $zip = new ZipArchive;
    $zip->open($tempFile);
    $sheet = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    unlink($tempFile);

    return $sheet;
}
