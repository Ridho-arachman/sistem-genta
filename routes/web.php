<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CctvAnomalyReportController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspeksiController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SpWarningController;
use Illuminate\Support\Facades\Route;

// ==================== LANDING & AUTH ====================
Route::get('/', [LandingController::class, 'home'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

// ==================== PROTECTED ROUTES ====================
Route::middleware(['auth', 'restrict.pengawas'])->group(function () {

    // Dashboard - redirect per peran
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================== ADMIN ====================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Users
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::get('users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Archived Users (soft deleted) - pakai ID manual, bukan route model binding
        Route::get('users/archive', [AdminController::class, 'archivedUsers'])->name('users.archive');
        Route::post('users/{id}/restore', [AdminController::class, 'restoreUser'])->name('users.restore');
        Route::delete('users/{id}/force', [AdminController::class, 'forceDeleteUser'])->name('users.force');

        // SP Warnings
        // export WAJIB sebelum resource: rute {sp_warning} akan menelan '/export' kalau urutannya sesudah
        Route::get('sp-warnings/export', [SpWarningController::class, 'export'])->name('sp-warnings.export');
        // edit tidak ada: controller hanya mengubah status lewat update
        Route::resource('sp-warnings', SpWarningController::class)->names('sp-warnings')->except(['edit']);

        // CCTV Anomaly Reports
        Route::get('cctv-anomalies/export', [CctvAnomalyReportController::class, 'export'])->name('cctv-anomalies.export');
        // edit & destroy tidak ada di controller
        Route::resource('cctv-anomalies', CctvAnomalyReportController::class)->names('cctv-anomalies')->except(['edit', 'destroy']);

        // Dapur (full CRUD)
        Route::resource('dapurs', DapurController::class)->names('dapurs');

        // CCTV
        Route::get('cctvs/monitoring', function () {
            return redirect()->route('admin.cctvs.index');
        })->name('cctvs.monitoring');
        Route::resource('cctvs', CctvController::class)->names('cctvs');

        // Laporan Pengawasan
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
        Route::put('laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');

        // Inspeksi (monitor & export)
        Route::get('inspeksi', [InspeksiController::class, 'index'])->name('inspeksi.index');
        Route::get('inspeksi/create', [InspeksiController::class, 'create'])->name('inspeksi.create');
        Route::get('inspeksi/export', [InspeksiController::class, 'export'])->name('inspeksi.export');

        // Settings
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('settings/profile', [AdminController::class, 'editProfile'])->name('settings.profile');
        Route::put('settings/profile', [AdminController::class, 'updateProfile'])->name('settings.profile.update');
    });

    // ==================== PENGAWAS ====================
    Route::middleware(['role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'pengawas'])->name('dashboard');

        // Dapur (detail only)
        Route::get('dapurs/{dapur}', [DapurController::class, 'show'])->name('dapurs.show');

        // Inspeksi
        Route::get('inspeksi', [InspeksiController::class, 'index'])->name('inspeksi.index');
        Route::get('inspeksi/create', [InspeksiController::class, 'create'])->name('inspeksi.create');

        // Laporan Pengawasan
        Route::get('laporan', [LaporanController::class, 'indexPengawas'])->name('laporan.index');
        Route::get('laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
        Route::post('laporan', [LaporanController::class, 'store'])->name('laporan.store');

        // Pengaturan
        Route::get('settings', function () {
            return view('pengawas.settings.index');
        })->name('settings');
        Route::put('settings/profile', [AuthController::class, 'updatePassword'])->name('settings.profile.update');
    });

    // ==================== MANAJERIAL (READ ONLY) ====================
    Route::middleware(['role:manajerial'])->prefix('manajerial')->name('manajerial.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'manajerial'])->name('dashboard');

        // Dapur (read only)
        Route::get('dapurs', [DapurController::class, 'index'])->name('dapurs.index');
        Route::get('dapurs/{dapur}', [DapurController::class, 'show'])->name('dapurs.show');

        // CCTV (read only)
        Route::get('cctvs', [CctvController::class, 'index'])->name('cctvs.index');
        Route::get('cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');

        // CCTV Anomaly Reports (read + receive notifications)
        // export WAJIB sebelum rute {anomaly} supaya '/export' tidak tertelan binding
        Route::get('cctv-anomalies/export', [CctvAnomalyReportController::class, 'export'])->name('cctv-anomalies.export');
        Route::get('cctv-anomalies', [CctvAnomalyReportController::class, 'index'])->name('cctv-anomalies.index');
        Route::get('cctv-anomalies/{anomaly}', [CctvAnomalyReportController::class, 'show'])->name('cctv-anomalies.show');

        // SP Warnings (read - receive copy)
        Route::get('sp-warnings', [SpWarningController::class, 'index'])->name('sp-warnings.index');
        Route::get('sp-warnings/{sp_warning}', [SpWarningController::class, 'show'])->name('sp-warnings.show');

        // Inspeksi (read + export)
        Route::get('inspeksi', [InspeksiController::class, 'index'])->name('inspeksi.index');
        Route::get('inspeksi/create', [InspeksiController::class, 'create'])->name('inspeksi.create');
        Route::get('inspeksi/export', [InspeksiController::class, 'export'])->name('inspeksi.export');

        // Settings (read only)
        Route::get('settings', function () {
            return view('manajerial.settings');
        })->name('settings');
    });

    // ==================== KEPALA DAPUR ====================
    Route::middleware(['role:kepala_dapur'])->prefix('kepala-dapur')->name('kepala-dapur.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'kepalaDapur'])->name('dashboard');

        // Dapur (read only)
        Route::get('dapurs', [DapurController::class, 'index'])->name('dapurs.index');
        Route::get('dapurs/{dapur}', [DapurController::class, 'show'])->name('dapurs.show');

        // CCTV (read only)
        Route::get('cctvs', [CctvController::class, 'index'])->name('cctvs.index');
        Route::get('cctvs/{cctv}', [CctvController::class, 'show'])->name('cctvs.show');

        // CCTV Anomaly History (read only)
        Route::get('cctv-anomalies', [CctvAnomalyReportController::class, 'index'])->name('cctv-anomalies.index');
        Route::get('cctv-anomalies/{anomaly}', [CctvAnomalyReportController::class, 'show'])->name('cctv-anomalies.show');

        // Settings (read only)
        Route::get('settings', function () {
            return view('kepala-dapur.settings');
        })->name('settings');
    });
});

// ==================== MOBILE API (Sanctum) ====================
Route::prefix('api/mobile')->middleware(['auth:sanctum'])->group(function () {
    Route::post('fcm-token', [MobileController::class, 'storeFcmToken']);
    Route::post('sync-locations', [MobileController::class, 'syncLocations']);
    Route::get('dapurs/geofences', [MobileController::class, 'getGeofences']);
    Route::post('absensi/auto-checkin', [MobileController::class, 'autoCheckin']);
});
