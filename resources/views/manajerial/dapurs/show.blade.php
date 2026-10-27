@extends('manajerial.layouts.app', ['pageTitle' => 'Detail Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-secondary)]">{{ $dapur->nama }}</h2>
                <p class="text-[var(--color-text-muted)] mt-1">{{ $dapur->lokasi ?? 'Lokasi tidak diset' }}</p>
            </div>
            <a href="{{ route('manajerial.dapurs.index') }}" class="btn-secondary">Kembali</a>
        </div>

        {{-- Assignment Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)]">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <h3 class="text-lg font-semibold text-[var(--color-secondary)]">Pengawas Terassign</h3>
                </div>
                @if ($dapur->pengawas->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($dapur->pengawas as $p)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                {{ $p->name }}
                                @if ($p->email)
                                    <span class="text-[var(--color-text-muted)] ml-2">({{ $p->email }})</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-[var(--color-text-muted)] mt-2">{{ $dapur->pengawas->count() }} pengawas</p>
                @else
                    <p class="text-[var(--color-text-muted)]">Belum ada pengawas yang diassign</p>
                @endif
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)]">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <h3 class="text-lg font-semibold text-[var(--color-secondary)]">Kepala Dapur</h3>
                </div>
                @if ($dapur->kepalaDapur)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#22C55E15] text-[#166534] grid place-items-center font-bold text-lg">
                            {{ strtoupper($dapur->kepalaDapur->name[0]) }}
                        </div>
                        <div>
                            <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->kepalaDapur->name }}</p>
                            <p class="text-sm text-[var(--color-text-muted)]">{{ $dapur->kepalaDapur->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-[var(--color-text-muted)]">Belum diassign kepala dapur</p>
                @endif
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Alamat</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->alamat ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Koordinat</p>
                <p class="font-semibold text-[var(--color-secondary)] font-mono text-sm">
                    @if ($dapur->latitude && $dapur->longitude)
                        {{ $dapur->latitude }}, {{ $dapur->longitude }}
                    @else
                        Belum diset
                    @endif
                </p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Radius Geofence</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->geofence_radius ?? 100 }} meter</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Jumlah CCTV</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->cctvs->count() }}</p>
            </div>
        </div>

        {{-- Map Preview --}}
        @if ($dapur->latitude && $dapur->longitude)
        <div class="border-t border-[var(--color-border)] pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Lokasi di Peta</h3>
            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden relative">
                <iframe width="100%" height="100%" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=&q={{ $dapur->latitude }},{{ $dapur->longitude }}&zoom=16" allowfullscreen></iframe>
            </div>
        </div>
        @endif

        {{-- CCTV List --}}
        @if ($dapur->cctvs->count() > 0)
        <div class="border-t border-[var(--color-border)] pt-6">
            <h3 class="text-lg font-semibold mb-4">Daftar CCTV</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th">Nama</th>
                            <th class="table-th">Area</th>
                            <th class="table-th">Cam #</th>
                            <th class="table-th">IP Address</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($dapur->cctvs as $cctv)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td font-medium">{{ $cctv->nama }}</td>
                            <td class="table-td">{{ $cctv->section ?? '-' }}</td>
                            <td class="table-td">{{ $cctv->camera_number ?? '-' }}</td>
                            <td class="table-td text-[var(--color-text-muted)] font-mono text-sm">{{ $cctv->ip_address ?? '-' }}</td>
                            <td class="table-td">
                                <span class="badge @if($cctv->status === 'online') badge-success @elseif($cctv->status === 'offline') badge-danger @else badge-warning @endif">
                                    {{ ucfirst($cctv->status) }}
                                </span>
                            </td>
                            <td class="table-td">
                                <a href="{{ route('manajerial.cctvs.show', $cctv) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection