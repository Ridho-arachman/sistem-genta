@extends('kepala-dapur.layouts.app', ['pageTitle' => 'Dashboard Kepala Dapur'])

@section('content')
<div class="space-y-8">
    {{-- Welcome --}}
    <div class="card p-6 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-dark)] text-white">
        <h2 class="text-2xl font-bold">Dashboard Kepala Dapur</h2>
        <p class="text-[var(--color-primary-light)] mt-1">Monitoring CCTV Dapur & Riwayat Anomali</p>
    </div>

    {{-- Dapur Cards with CCTV --}}
    @php
        $dapursWithCctv = $dapurs->map(function ($dapur) {
            $dapur->cctvs_count = $dapur->cctvs->count();
            $dapur->online_count = $dapur->cctvs->where('status', 'online')->count();
            $dapur->offline_count = $dapur->cctvs->where('status', 'offline')->count();
            return $dapur;
        });
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($dapursWithCctv as $dapur)
        <div class="card">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--color-secondary)]">{{ $dapur->nama }}</h3>
                        <p class="text-sm text-[var(--color-text-muted)]">{{ $dapur->lokasi ?? 'Lokasi tidak diset' }}</p>
                    </div>
                    <span class="badge badge-primary">{{ $dapur->cctvs_count }} CCTV</span>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $dapur->online_count }}</p>
                        <p class="text-xs text-green-700">Online</p>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <p class="text-2xl font-bold text-red-600">{{ $dapur->offline_count }}</p>
                        <p class="text-xs text-red-700">Offline</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-600">{{ $dapur->cctvs_count - $dapur->online_count - $dapur->offline_count }}</p>
                        <p class="text-xs text-gray-700">Lainnya</p>
                    </div>
                </div>
                
                <a href="{{ route('kepala-dapur.cctvs.monitoring', $dapur) }}" class="btn-primary w-full text-center">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Monitoring CCTV
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full card p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Data Dapur</h3>
            <p class="text-[var(--color-text-muted)] mt-1">Hubungi admin untuk menambah data dapur</p>
        </div>
        @endforelse
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total CCTV</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $dapurs->sum('cctvs_count') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total Online</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $dapurs->sum('online_count') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total Offline</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $dapurs->sum('offline_count') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold">Navigasi Cepat</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('kepala-dapur.cctvs.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-[var(--color-primary)]">
                    <h4 class="font-medium text-[var(--color-secondary)]">Semua CCTV</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Daftar lengkap CCTV semua dapur</p>
                </a>
                <a href="{{ route('kepala-dapur.cctv-anomalies.index') }}" class="card p-4 hover:bg-[var(--color-surface)] transition-colors border-l-4 border-orange-500">
                    <h4 class="font-medium text-[var(--color-secondary)]">Riwayat Anomali</h4>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1">Laporan anomali CCTV yang sudah ditangani</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection