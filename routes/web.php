<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CctvAnomalyReportController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SpWarningController;
use App\Models\Absensi;
use App\Models\Cctv;
use App\Models\CctvAnomalyReport;
use App\Models\Dapur;
use App\Models\SpWarning;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'home'])->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - role based
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isPengawas()) {
            return redirect()->route('pengawas.dashboard');
        } elseif ($user->isManajerial()) {
            return redirect()->route('manajerial.dashboard');
        } elseif ($user->isKepalaDapur()) {
            return redirect()->route('kepala-dapur.dashboard');
        }

        abort(403);
    })->name('dashboard');

    // ==================== ADMIN ROUTES ====================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // SP Warnings
        Route::resource('sp-warnings', SpWarningController::class)->names('sp-warnings');
        Route::get('sp-warnings/export', [SpWarningController::class, 'export'])->name('sp-warnings.export');

        // CCTV Anomaly Reports
        Route::resource('cctv-anomalies', CctvAnomalyReportController::class)->names('cctv-anomalies');
        Route::get('cctv-anomalies/export', [CctvAnomalyReportController::class, 'export'])->name('cctv-anomalies.export');

        // Dapur (full CRUD)
        Route::resource('dapurs', DapurController::class)->names('dapurs');

        // CCTV (full CRUD)
        Route::resource('cctvs', CctvController::class)->names('cctvs');
        Route::get('cctvs/{dapur}/monitoring', [CctvController::class, 'monitoring'])->name('cctvs.monitoring');

        // Absensi (monitor & export)
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
    });

    // ==================== PENGAWAS ROUTES ====================
    Route::middleware(['role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
        Route::get('/dashboard', function () {
            return view('pengawas.dashboard');
        })->name('dashboard');

        // Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::post('/absensi/{absensi}/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
    });

    // ==================== MANAJERIAL ROUTES (READ ONLY) ====================
    Route::middleware(['role:manajerial'])->prefix('manajerial')->name('manajerial.')->group(function () {
        Route::get('/dashboard', function () {
            $stats = [
                'total_pengawas' => User::where('role', 'pengawas')->count(),
                'total_dapur' => Dapur::count(),
                'total_cctv' => Cctv::count(),
                'cctv_online' => Cctv::where('status', 'online')->count(),
                'cctv_offline' => Cctv::where('status', 'offline')->count(),
                'cctv_maintenance' => Cctv::where('status', 'maintenance')->count(),
                'absensi_hari_ini' => Absensi::whereDate('created_at', today())->count(),
                'sp_warnings_aktif' => SpWarning::where('status', 'aktif')->count(),
                'anomaly_reports_open' => CctvAnomalyReport::where('status', '!=', 'selesai')->count(),
            ];

            return view('manajerial.dashboard', compact('stats'));
        })->name('dashboard');

        // Dapur (read only)
        Route::get('/dapurs', [DapurController::class, 'index'])->name('dapurs.index');
        Route::get('/dapurs/{dapur}', [DapurController::class, 'show'])->name('dapurs.show');

        // CCTV (read only)
        Route::get('/cctvs', [CctvController::class, 'index'])->name('cctvs.index');
        Route::get('/cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');
        Route::get('/cctvs/{dapur}/monitoring', [CctvController::class, 'monitoring'])->name('cctvs.monitoring');

        // CCTV Anomaly Reports (read + receive notifications)
        Route::get('/cctv-anomalies', [CctvAnomalyReportController::class, 'index'])->name('cctv-anomalies.index');
        Route::get('/cctv-anomalies/{anomaly}', [CctvAnomalyReportController::class, 'show'])->name('cctv-anomalies.show');
        Route::get('/cctv-anomalies/export', [CctvAnomalyReportController::class, 'export'])->name('cctv-anomalies.export');

        // SP Warnings (read - receive copy)
        Route::get('/sp-warnings', [SpWarningController::class, 'index'])->name('sp-warnings.index');
        Route::get('/sp-warnings/{warning}', [SpWarningController::class, 'show'])->name('sp-warnings.show');

        // Absensi (read + export)
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
    });

    // ==================== KEPALA DAPUR ROUTES ====================
    Route::middleware(['role:kepala_dapur'])->prefix('kepala-dapur')->name('kepala-dapur.')->group(function () {
        Route::get('/dashboard', function () {
            $dapurs = Dapur::with('cctvs')->get();

            return view('kepala-dapur.dashboard', compact('dapurs'));
        })->name('dashboard');

        // CCTV Dashboard
        Route::get('/cctvs', [CctvController::class, 'index'])->name('cctvs.index');
        Route::get('/cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');
        Route::get('/cctvs/{dapur}/monitoring', [CctvController::class, 'monitoring'])->name('cctvs.monitoring');

        // CCTV Anomaly History (read only)
        Route::get('/cctv-anomalies', [CctvAnomalyReportController::class, 'index'])->name('cctv-anomalies.index');
        Route::get('/cctv-anomalies/{anomaly}', [CctvAnomalyReportController::class, 'show'])->name('cctv-anomalies.show');
    });
});

// API Routes for Mobile App
Route::prefix('api/mobile')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/fcm-token', [MobileController::class, 'storeFcmToken']);
    Route::post('/sync-locations', [MobileController::class, 'syncLocations']);
    Route::get('/dapurs/geofences', [MobileController::class, 'getGeofences']);
    Route::post('/absensi/auto-checkin', [MobileController::class, 'autoCheckin']);
});
