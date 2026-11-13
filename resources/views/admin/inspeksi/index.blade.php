@extends('admin.layouts.app', ['pageTitle' => 'Inspeksi Dapur'])

@section('page_content')
<x-admin::page-heading title="Inspeksi Dapur" subtitle="Rekapitulasi inspeksi dapur">
    <x-slot:actions>
        <a href="{{ route($exportRoute, array_merge(['format' => 'xlsx'], request()->query())) }}" class="btn btn-primary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span>Excel</span>
        </a>
        <a href="{{ route($exportRoute, array_merge(['format' => 'csv'], request()->query())) }}" class="btn btn-secondary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span>CSV</span>
        </a>
    </x-slot:actions>
</x-admin::page-heading>

<x-admin::panel>
    <x-slot:head>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5 pb-4 border-b border-[var(--color-border)]">
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Daftar Inspeksi</h2>
            <span class="text-sm text-[var(--color-text-muted)]">{{ $laporans->total() }} laporan terdaftar</span>
        </div>
    </x-slot:head>

    <form method="GET" action="{{ route($indexRoute) }}" class="px-4 py-3 border-b border-[var(--color-border)] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-[var(--color-text-muted)] uppercase" for="dapur_id">Dapur</label>
            <select id="dapur_id" name="dapur_id" class="input input-bordered w-full text-sm">
                <option value="">Semua Dapur</option>
                @foreach ($dapurs as $d)
                    <option value="{{ $d->id }}" {{ request('dapur_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-[var(--color-text-muted)] uppercase" for="start_date">Dari Tanggal</label>
            <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="input input-bordered w-full text-sm">
        </div>
        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-[var(--color-text-muted)] uppercase" for="end_date">Sampai Tanggal</label>
            <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="input input-bordered w-full text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route($indexRoute) }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if ($laporans->count())
        <div class="overflow-x-auto">
            <x-admin::table>
                <x-slot:head>
                    <th>TANGGAL</th>
                    <th>DAPUR</th>
                    <th>PENGAWAS</th>
                    <th>STATUS</th>
                    <th>LOKASI</th>
                    <th>AKSI</th>
                </x-slot:head>
                @foreach ($laporans as $l)
                    @php
                        $statusBadge = match ($l->status) {
                            'selesai' => 'ok',
                            'temuan' => 'off',
                            default => 'warn',
                        };
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $l->dapur->nama ?? '-' }}</td>
                        <td>{{ $l->createdBy->name ?? '-' }}</td>
                        <td><x-status-badge :status="$statusBadge" :label="$l->status" /></td>
                        <td class="text-sm text-[var(--color-text-muted)]">
                            {{ $l->latitude && $l->longitude ? number_format((float) $l->latitude, 5).', '.number_format((float) $l->longitude, 5) : '-' }}
                        </td>
                        <td>
                            {{-- shortcut: link detail hanya valid untuk admin, role lain memakai view berbeda --}}
                            <a href="{{ route('admin.laporan.show', $l) }}" class="text-[var(--color-primary)] hover:underline text-sm font-medium">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </x-admin::table>
        </div>
        <div class="px-4 py-4 border-t border-[var(--color-border)]">
            {{ $laporans->links() }}
        </div>
    @else
        <x-admin::empty-state title="Belum Ada Data Inspeksi" message="Tidak ada laporan inspeksi untuk filter ini." />
    @endif
</x-admin::panel>
@endsection