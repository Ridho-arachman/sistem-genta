@extends('pengawas.layouts.app', ['pageTitle' => 'Dashboard Pengawas'])

@section('content')
<div class="space-y-8">
    {{-- Welcome Card --}}
    <div class="card p-6 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-dark)] text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}!</h2>
                <p class="text-[var(--color-primary-light)] mt-1">Silakan melakukan absensi kehadiran Anda hari ini</p>
            </div>
            <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('pengawas.absensi.create') }}" class="card p-6 hover:shadow-lg transition-shadow border-l-4 border-[var(--color-primary)]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[var(--color-secondary)]">Absensi Masuk</h3>
                    <p class="text-sm text-[var(--color-text-muted)]">Check-in dengan foto & GPS</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('pengawas.absensi.index') }}" class="card p-6 hover:shadow-lg transition-shadow border-l-4 border-green-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[var(--color-secondary)]">Riwayat Absensi</h3>
                    <p class="text-sm text-[var(--color-text-muted)]">Lihat riwayat kehadiran Anda</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Today's Absensi Status --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold">Status Absensi Hari Ini</h3>
        </div>
        <div class="p-6">
            @php
                $todayAbsensi = auth()->user()->absensis()->whereDate('created_at', today())->first();
            @endphp
            @if ($todayAbsensi)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                        <p class="text-sm text-green-700">Status</p>
                        <p class="text-xl font-semibold text-green-800">{{ ucfirst($todayAbsensi->status) }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-blue-700">Check-in</p>
                        <p class="text-xl font-semibold text-blue-800">{{ $todayAbsensi->check_in_at ? $todayAbsensi->check_in_at->format('H:i') : '-' }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                        <p class="text-sm text-purple-700">Check-out</p>
                        <p class="text-xl font-semibold text-purple-800">{{ $todayAbsensi->check_out_at ? $todayAbsensi->check_out_at->format('H:i') : 'Belum check-out' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm text-gray-700">Dapur</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $todayAbsensi->dapur->nama }}</p>
                    </div>
                </div>
                
                @if (!$todayAbsensi->check_out_at)
                <div class="mt-6">
                    <form action="{{ route('pengawas.absensi.checkout', $todayAbsensi) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Check-out Sekarang
                        </button>
                    </form>
                </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Absensi Hari Ini</h3>
                    <p class="text-[var(--color-text-muted)] mt-1">Silakan lakukan absensi check-in</p>
                    <a href="{{ route('pengawas.absensi.create') }}" class="btn-primary mt-4 inline-block">Absensi Sekarang</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent History --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-[var(--color-border)] flex justify-between items-center">
            <h3 class="text-lg font-semibold">Riwayat Absensi Terbaru</h3>
            <a href="{{ route('pengawas.absensi.index') }}" class="text-sm text-[var(--color-primary)] hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-th">Tanggal</th>
                        <th class="table-th">Dapur</th>
                        <th class="table-th">Check-in</th>
                        <th class="table-th">Check-out</th>
                        <th class="table-th">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @php
                        $recentAbsensi = auth()->user()->absensis()->latest()->take(5)->get();
                    @endphp
                    @forelse ($recentAbsensi as $absensi)
                    <tr class="hover:bg-[var(--color-surface)]">
                        <td class="table-td">{{ $absensi->created_at->format('d/m/Y') }}</td>
                        <td class="table-td">{{ $absensi->dapur->nama }}</td>
                        <td class="table-td">{{ $absensi->check_in_at ? $absensi->check_in_at->format('H:i') : '-' }}</td>
                        <td class="table-td">{{ $absensi->check_out_at ? $absensi->check_out_at->format('H:i') : '-' }}</td>
                        <td class="table-td">
                            <span class="badge @if($absensi->status === 'hadir') badge-success @elseif($absensi->status === 'izin') badge-warning @elseif($absensi->status === 'cuti') badge-gray @else badge-danger @endif">
                                {{ ucfirst($absensi->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="table-td text-center py-8 text-[var(--color-text-muted)]" colspan="5">Belum ada riwayat absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection