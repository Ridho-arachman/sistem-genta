@extends('admin.layouts.app', ['pageTitle' => 'Dashboard Admin'])

@section('content')
<div class="space-y-8">
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
                    <p class="text-sm text-[var(--color-text-muted)]">Total Manajerial</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total_manajerial'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Total Kepala Dapur</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total_kepala_dapur'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
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
    </div>

    {{-- CCTV Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                    <p class="text-sm text-[var(--color-text-muted)]">Online</p>
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
                    <p class="text-sm text-[var(--color-text-muted)]">Offline</p>
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

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">SP Warning Aktif</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['sp_warnings_aktif'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)]">Anomali CCTV (Open)</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['anomaly_reports_open'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Absensi --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-[var(--color-border)] flex justify-between items-center">
                <h3 class="text-lg font-semibold">Absensi Terbaru</h3>
                <a href="{{ route('admin.absensi.index') }}" class="text-sm text-[var(--color-primary)] hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="table-th">Pengawas</th>
                            <th class="table-th">Dapur</th>
                            <th class="table-th">Waktu</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @forelse ($recentAbsensi as $absensi)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td">{{ $absensi->user->name }}</td>
                            <td class="table-td">{{ $absensi->dapur->nama }}</td>
                            <td class="table-td">{{ $absensi->created_at->format('d/m/Y H:i') }}</td>
                            <td class="table-td">
                                <span class="badge @if($absensi->status === 'hadir') badge-success @elseif($absensi->status === 'izin') badge-warning @elseif($absensi->status === 'cuti') badge-gray @else badge-danger @endif">
                                    {{ ucfirst($absensi->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="table-td text-center py-8 text-[var(--color-text-muted)]" colspan="4">Belum ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Anomalies --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-[var(--color-border)] flex justify-between items-center">
                <h3 class="text-lg font-semibold">Anomali CCTV Terbaru</h3>
                <a href="{{ route('admin.cctv-anomalies.index') }}" class="text-sm text-[var(--color-primary)] hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="table-th">CCTV</th>
                            <th class="table-th">Dapur</th>
                            <th class="table-th">Severity</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @forelse ($recentAnomalies as $anomaly)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td">{{ $anomaly->cctv->display_name }}</td>
                            <td class="table-td">{{ $anomaly->cctv->dapur->nama }}</td>
                            <td class="table-td">
                                <span class="badge @if($anomaly->severity === 'tinggi') badge-danger @elseif($anomaly->severity === 'sedang') badge-warning @else badge-gray @endif">
                                    {{ ucfirst($anomaly->severity) }}
                                </span>
                            </td>
                            <td class="table-td">
                                <span class="badge @if($anomaly->status === 'baru') badge-danger @elseif($anomaly->status === 'diproses') badge-warning @else badge-success @endif">
                                    {{ ucfirst($anomaly->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="table-td text-center py-8 text-[var(--color-text-muted)]" colspan="4">Belum ada laporan anomali</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection