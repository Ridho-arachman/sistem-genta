@extends('pengawas.layouts.app', ['pageTitle' => 'Detail Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Standard Header --}}
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>{{ $dapur->nama }}</h1>
            <div class="muted">{{ $dapur->lokasi ?? 'Lokasi tidak diset' }}</div>
        </div>
        <a href="{{ route('pengawas.dashboard') }}" class="btn-secondary">Kembali ke Beranda</a>
    </div>

    <div class="card p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Alamat</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->alamat ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Kepala Dapur</p>
                @if ($dapur->kepalaDapur)
                    <p class="font-semibold text-[var(--color-secondary)]">
                        {{ $dapur->kepalaDapur->name }} ({{ $dapur->kepalaDapur->email }})
                    </p>
                @else
                    <span class="text-[var(--color-text-muted)] text-sm">-</span>
                @endif
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Jumlah CCTV</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->cctvs->count() }}</p>
            </div>
        </div>

        {{-- Map Embed --}}
        <x-pengawas.components.peta-embed dapur="$dapur" />

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
                                <x-pengawas.components.status-badge status="{{ $cctv->status }}" />
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