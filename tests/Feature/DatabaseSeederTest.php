<?php

use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\Laporan;
use App\Models\LaporanHasil;
use App\Models\Settings;
use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates linked demo data and can run again', function () {
    $this->seed();

    expect(Dapur::query()->count())->toBe(8)
        ->and(Cctv::query()->count())->toBe(24)
        ->and(User::query()->count())->toBe(6)
        ->and(Settings::query()->count())->toBe(6)
        ->and(Laporan::query()->count())->toBe(12)
        ->and(LaporanHasil::query()->count())->toBe(60)
        ->and(SpWarning::query()->count())->toBe(3)
        ->and(CctvAnomalyReport::query()->count())->toBe(4);

    expect(User::query()->where('email', 'pengawas@test.com')->firstOrFail()->dapurs()->count())->toBe(2)
        ->and(Laporan::query()->firstOrFail()->hasil()->count())->toBe(5);

    $this->seed();

    expect(Dapur::query()->count())->toBe(8)
        ->and(LaporanHasil::query()->count())->toBe(60);
});
