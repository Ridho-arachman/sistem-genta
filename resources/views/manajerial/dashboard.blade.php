@extends('manajerial.layouts.app', ['pageTitle' => 'Dashboard Manajerial'])

@section('content')
<div class="space-y-8">
    {{-- Welcome --}}
    <div class="card p-6 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-dark)] text-white">
        <h2 class="text-2xl font-bold">Dashboard Monitoring Manajerial</h2>
        <p class="text-[var(--color-primary-light)] mt-1">Monitoring sistem CCTV & Absensi - Read Only</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total Pengawas</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total_pengawas'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total Dapur</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total_dapur'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total CCTV</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total_cctv'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Absensi Hari Ini</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['absensi_hari_ini'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- CCTV Status --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">CCTV Online</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['cctv_online'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">CCTV Offline</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['cctv_offline'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Maintenance</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['cctv_maintenance'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">SP Warning Aktif</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['sp_warnings_aktif'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Anomali CCTV (Open)</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats['anomaly_reports_open'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold">Akses Cepat Monitoring</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('manajerial.dapurs.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-blue-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">Data Dapur</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Lihat semua dapur & detail</p>
                </a>
                <a href="{{ route('manajerial.cctvs.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-[var(--color-primary)]">
                    <h4 class="font-medium text-[var(--color-secondary)]">Monitoring CCTV</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Status CCTV per dapur</p>
                </a>
                <a href="{{ route('manajerial.cctv-anomalies.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-orange-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">Anomali CCTV</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Laporan anomali & status</p>
                </a>
                <a href="{{ route('manajerial.sp-warnings.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-red-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">SP Warning</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Surat peringatan pengawas</p>
                </a>
                <a href="{{ route('manajerial.absensi.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-green-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">Absensi Pengawas</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Monitoring kehadiran</p>
                </a>
                <a href="{{ route('manajerial.cctvs.monitoring', \App\Models\Dapur::first()->id ?? 1) }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-purple-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">Live Monitoring</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Streaming CCTV real-time</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection