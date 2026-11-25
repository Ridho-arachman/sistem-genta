<?php

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Database\Eloquent\LazyLoadingViolationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * @return array{admin: User, manajerial: User, kepala: User, pengawas: User, dapur: Dapur}
 */
function auditUsersAndDapur(): array
{
    $dapur = Dapur::factory()->create();

    $pengawas = User::factory()->create(['role' => 'pengawas']);
    $pengawas->dapurs()->sync([(string) $dapur->id]);

    return [
        'admin' => User::factory()->create(['role' => 'admin']),
        'manajerial' => User::factory()->create(['role' => 'manajerial']),
        'kepala' => User::factory()->create(['role' => 'kepala_dapur', 'dapur_id' => (string) $dapur->id]),
        'pengawas' => $pengawas,
        'dapur' => $dapur,
    ];
}

function seedAuditRows(Dapur $dapur, User $pengawas): array
{
    $cctv = Cctv::factory()->create(['dapur_id' => (string) $dapur->id, 'status' => 'online']);
    Cctv::factory()->create(['dapur_id' => (string) $dapur->id, 'status' => 'offline']);
    $laporan = Laporan::factory()->create(['dapur_id' => (string) $dapur->id, 'created_by' => (string) $pengawas->id]);
    Laporan::factory()->create(['dapur_id' => (string) $dapur->id, 'created_by' => (string) $pengawas->id]);
    $anomaly = CctvAnomalyReport::factory()->count(2)->create(['cctv_id' => (string) $cctv->id])->first();
    $spWarning = SpWarning::factory()->count(2)->create([
        'user_id' => (string) $pengawas->id,
        'admin_id' => (string) User::factory()->create(['role' => 'admin'])->id,
    ])->first();

    return compact('cctv', 'laporan', 'anomaly', 'spWarning');
}

test('halaman daftar & dashboard tidak memicu lazy loading', function () {
    ['admin' => $admin, 'manajerial' => $manajerial, 'kepala' => $kepala, 'pengawas' => $pengawas, 'dapur' => $dapur]
        = auditUsersAndDapur();
    $seed = seedAuditRows($dapur, $pengawas);
    $cctv = $seed['cctv'];
    $laporan = $seed['laporan'];
    $anomaly = $seed['anomaly'];
    $spWarning = $seed['spWarning'];

    // Satu klaim tes: jika view masih membaca relasi yang tidak di-eager load,
    // halaman itu melempar LazyLoadingViolationException alih-alih menambah query per baris.
    Model::preventLazyLoading(true);

    $visit = function (string $url, User $user, ?string $userAgent = null) {
        try {
            $request = $this->actingAs($user);
            if ($userAgent !== null) {
                $request = $request->withHeaders(['User-Agent' => $userAgent]);
            }

            $request->get($url)->assertOk();
        } catch (LazyLoadingViolationException $e) {
            throw new RuntimeException($url.' mem lazy loading: '.$e->getMessage(), 0, $e);
        }
    };

    $visit(route('admin.dashboard'), $admin);
    $visit(route('admin.users'), $admin);
    $visit(route('admin.users.archive'), $admin);
    $visit(route('admin.users.edit', $pengawas), $admin);
    $visit(route('admin.cctvs.index'), $admin);
    $visit(route('admin.cctvs.show', $cctv), $admin);
    $visit(route('admin.cctvs.edit', $cctv), $admin);
    $visit(route('admin.cctv-anomalies.index'), $admin);
    $visit(route('admin.cctv-anomalies.show', $anomaly->id), $admin);
    $visit(route('admin.dapurs.index'), $admin);
    $visit(route('admin.dapurs.show', $dapur), $admin);
    $visit(route('admin.dapurs.edit', $dapur), $admin);
    $visit(route('admin.laporan.index'), $admin);
    $visit(route('admin.laporan.show', $laporan), $admin);
    $visit(route('admin.inspeksi.index'), $admin);
    $visit(route('admin.sp-warnings.index'), $admin);
    $visit(route('admin.sp-warnings.show', $spWarning), $admin);
    $visit(route('admin.sp-warnings.export'), $admin);
    $visit(route('manajerial.dashboard'), $manajerial);
    $visit(route('manajerial.cctvs.index'), $manajerial);
    $visit(route('manajerial.cctvs.show', $cctv), $manajerial);
    $visit(route('manajerial.cctv-anomalies.index'), $manajerial);
    $visit(route('manajerial.cctv-anomalies.show', $anomaly), $manajerial);
    $visit(route('manajerial.dapurs.index'), $manajerial);
    $visit(route('manajerial.dapurs.show', $dapur), $manajerial);
    $visit(route('manajerial.sp-warnings.index'), $manajerial);
    $visit(route('manajerial.sp-warnings.show', $spWarning), $manajerial);
    $visit(route('manajerial.inspeksi.index'), $manajerial);
    $visit(route('kepala-dapur.dashboard'), $kepala);
    $visit(route('kepala-dapur.cctvs.index'), $kepala);
    $visit(route('kepala-dapur.cctvs.show', $cctv), $kepala);
    $visit(route('kepala-dapur.cctv-anomalies.index'), $kepala);
    $visit(route('kepala-dapur.cctv-anomalies.show', $anomaly), $kepala);
    $visit(route('kepala-dapur.dapurs.index'), $kepala);
    $visit(route('kepala-dapur.dapurs.show', $dapur), $kepala);
    $visit(route('pengawas.dashboard'), $pengawas, 'GentaCapacitor');
    $visit(route('pengawas.laporan.index'), $pengawas, 'GentaCapacitor');
    $visit(route('pengawas.laporan.show', $laporan->id), $pengawas, 'GentaCapacitor');
    $visit(route('pengawas.dapurs.show', $dapur), $pengawas, 'GentaCapacitor');
    $visit(route('pengawas.inspeksi.index'), $pengawas, 'GentaCapacitor');
});

afterEach(function () {
    Model::preventLazyLoading(false);
});
