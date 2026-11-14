@extends('admin.layouts.app', ['pageTitle' => 'Dashboard Admin'])

@section('page_content')
<x-admin::page-heading title="Dashboard Admin" subtitle="Ringkasan kondisi sistem dan pengawasan">
    <x-slot:actions>
        <a href="{{ route('admin.dapurs.create') }}" class="btn btn-primary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Dapur</span>
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-secondary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Tambah User</span>
        </a>
    </x-slot:actions>
</x-admin-page-heading>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <x-admin::stat-card label="Total Dapur" :value="$stats['total_dapur']" note="Lokasi terdaftar dalam sistem" icon="⌂" />
    <x-admin::stat-card label="Kamera Online" :value="$stats['cctv_online'] . '/' . $stats['total_cctv']" icon="▣" value-class="text-green-800"
        :note="$stats['total_cctv'] > 0 ? number_format($stats['cctv_online'] / $stats['total_cctv'] * 100, 1, ',', '.') . '% status konektivitas' : '0% status konektivitas'" />
    <x-admin::stat-card label="Laporan Hari Ini" :value="$stats['laporan_hari_ini']" icon="▤" icon-class="bg-blue-100 text-blue-800"
        :note="$stats['laporan_hari_ini_selesai'] . ' selesai · ' . ($stats['laporan_hari_ini'] - $stats['laporan_hari_ini_selesai']) . ' perlu tindak lanjut'" />
    <x-admin::stat-card label="Temuan Terbuka" :value="$stats['anomaly_reports_open']" note="Perlu ditinjau oleh admin" icon="⚑" value-class="text-red-800" icon-class="bg-red-100 text-red-800" />
</div>

<x-admin::panel title="Ekspor Data Inspeksi" subtitle="Filter dengan range tanggal, pilih format" :divider="false" class="mb-4 border-l-3 border-l-[var(--color-primary)]">
    <form method="GET" action="{{ route('admin.inspeksi.export') }}" class="space-y-4" id="exportForm" x-data="exportForm(@js(request('start_date')), @js(request('end_date')))" @submit="if (dateError) { $event.preventDefault(); } else { loading = true; }">
        {{-- Date Presets --}}
        <div class="flex gap-2 flex-wrap mb-4 pb-4 border-b border-[var(--color-border)]" role="group" aria-label="Pilihan cepat rentang tanggal">
            <button type="button" :class="presetClass('today')" @click="applyPreset('today')" title="Hari ini">Hari ini</button>
            <button type="button" :class="presetClass('week')" @click="applyPreset('week')" title="7 hari terakhir">Minggu ini</button>
            <button type="button" :class="presetClass('month')" @click="applyPreset('month')" title="Bulan ini">Bulan ini</button>
            <button type="button" :class="presetClass('last_month')" @click="applyPreset('last_month')" title="Bulan lalu">Bulan lalu</button>
            <button type="button" :class="presetClass('custom')" @click="setCustom()" title="Rentang kustom">Kustom</button>
        </div>

        {{-- Date Inputs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="dateFields">
            <div class="flex flex-col gap-1.5 date-field">
                <label for="start_date" class="inline-flex items-center gap-1.5 text-[12px] font-medium text-[var(--color-secondary)] uppercase tracking-[0.5px]">
                    <svg class="w-3.5 h-3.5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Tanggal Mulai
                </label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="start_date" id="start_date" x-model="startDate" class="input input-bordered pl-[44px] w-full placeholder-[var(--color-text-muted)]" placeholder="Pilih tanggal" aria-describedby="startDateHelp">
                </div>
                <span id="startDateHelp" class="text-[11px] text-[var(--color-text-muted)] mt-0.5" x-text="startHint"></span>
            </div>

            <div class="flex flex-col gap-1.5 date-field">
                <label for="end_date" class="inline-flex items-center gap-1.5 text-[12px] font-medium text-[var(--color-secondary)] uppercase tracking-[0.5px]">
                    <svg class="w-3.5 h-3.5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                    Tanggal Akhir
                </label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="end_date" id="end_date" x-model="endDate" class="input input-bordered pl-[44px] w-full placeholder-[var(--color-text-muted)]" placeholder="Pilih tanggal" aria-describedby="endDateHelp" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                </div>
                <span id="endDateHelp" class="text-[11px] text-[var(--color-text-muted)] mt-0.5" x-text="endHint"></span>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="format" class="inline-flex items-center gap-1.5 text-[12px] font-medium text-[var(--color-secondary)] uppercase tracking-[0.5px]">
                    <svg class="w-3.5 h-3.5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Format
                </label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <select name="format" id="format" class="input input-bordered border border-[var(--color-border)] w-full pl-[44px] pr-10 appearance-none">
                        <option value="xlsx" {{ request('format') === 'xlsx' ? 'selected' : '' }}>Excel (.xlsx)</option>
                        <option value="pdf" {{ request('format') === 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="csv" {{ request('format') === 'csv' ? 'selected' : '' }}>CSV</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        {{-- Validation Error --}}
        <div class="inline-flex items-center gap-2 px-3 py-3.5 rounded-[8px] border border-[#FECACA] bg-[#FEF2F2] text-[#991B1B] text-xs font-medium mt-1" id="dateError" role="alert" aria-live="polite" x-show="dateError" x-cloak>
            <svg class="w-3.5 h-3.5 text-[#EF4444] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>Tanggal mulai tidak boleh melebihi tanggal akhir</span>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 flex-wrap mt-4 pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="btn btn-primary" id="exportBtn" :disabled="loading || dateError">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span x-show="!loading">Ekspor Data</span>
                <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                    <svg class="h-[18px] w-[18px] animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014-12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Menyiapkan...
                </span>
            </button>
            <a href="{{ route('admin.inspeksi.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-secondary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Excel Cepat</span>
            </a>
            <button type="button" class="btn btn-ghost" id="clearFilters" title="Bersihkan filter" @click="clearFilters()">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>Bersihkan</span>
            </button>
        </div>
    </form>
</x-admin-panel>

<div class="grid gap-4 grid-cols-1 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,1fr)]">
    {{-- Left Column: Dapur Status + Pengawas Assignment --}}
    <div>
        <x-admin::panel class="h-full flex flex-col">
            <x-slot:head>
                <div class="flex items-center justify-between gap-2.5 mb-5">
                    <h2 class="text-sm font-bold text-[var(--color-secondary)]">Status Dapur</h2>
                    <x-kitchen-filter list-id="kitchenList" search-id="kitchenSearch" placeholder="Cari nama dapur…" />
                </div>
            </x-slot:head>
    <div id="kitchenList" class="space-y-2 flex-1">
                @forelse ($dapurs->take(5) as $dapur)
                    <div data-kitchen="card" class="flex items-center justify-between gap-4 p-4 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[12px] transition-shadow hover:shadow-sm">
                        <div class="flex-1 min-w-0">
                            <div data-kitchen="name" class="font-bold text-sm text-[var(--color-secondary)] truncate">{{ $dapur->nama }}</div>
                            <div data-kitchen="meta" class="text-[11px] text-[var(--color-text-muted)] mt-0.5 truncate">{{ $dapur->lokasi ?? '-' }}</div>
                            <div class="h-[6px] bg-[var(--color-border)] rounded-[3px] overflow-hidden mt-2">
                                <i class="h-full rounded-[3px] transition-all duration-300 {{ $dapur->status === 'operasional' ? 'bg-green-500' : ($dapur->status === 'perlu_perhatian' ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $dapur->progress ?? 0 }}%"></i>
                            </div>
                            <div class="flex gap-4 text-[11px] text-[var(--color-text-muted)] mt-1">
                                <span>{{ $dapur->cctvs_count ?? 0 }} kamera</span>
                                <span>{{ $dapur->progress ?? 0 }}%</span>
                            </div>
                        </div>
                        <x-status-badge :status="$dapur->status === 'operasional' ? 'ok' : ($dapur->status === 'perlu_perhatian' ? 'warn' : 'off')" :label="ucfirst($dapur->status)" />
                    </div>
                @empty
                    <div class="text-center py-8 text-[var(--color-text-muted)]">
                        Belum ada dapur terdaftar
                    </div>
                @endforelse
            </div>
            <div class="mt-4 text-right">
                <a href="{{ route('admin.dapurs.index') }}" class="btn btn-ghost">Lihat semua dapur →</a>
            </div>
        </x-admin-panel>

    </div>

    {{-- Right Column: CCTV + Activity --}}
    <div class="grid gap-4 max-w-full overflow-hidden">
        {{-- Camera Panel --}}
        <x-admin::panel title="Pratinjau CCTV" subtitle="Status terkini perangkat" :divider="false" class="max-w-full overflow-hidden">
            <div class="flex flex-col gap-3">
                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse mr-2"></span> LIVE
                </span>
                <div class="bg-[#1a1a1a] rounded-lg p-6 text-center min-h-[160px] flex flex-col justify-center items-center gap-3">
                    <div class="text-[48px] text-[#444]">▥</div>
                    <strong class="text-white">Pratinjau Kamera Dapur</strong>
                    <small class="text-[#888]">Stream CCTV akan tampil setelah integrasi perangkat</small>
                </div>
                <span class="text-[11px] text-[var(--color-text-muted)] text-center">CAM-01 · {{ $dapurs->first()->nama ?? 'Dapur' }}</span>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="min-w-0 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">▣</div>
                        <div><b>Area persiapan</b><div class="min-w-0 text-[11px] text-[var(--color-text-muted)]">{{ $dapurs->first()->nama ?? 'Dapur' }}</div></div>
                    </div>
                    <x-status-badge :status="'ok'" :label="'Online'" />
                </div>
                <div class="flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="min-w-0 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">▣</div>
                        <div><b>Area memasak</b><div class="min-w-0 text-[11px] text-[var(--color-text-muted)]">{{ $dapurs->skip(1)->first()->nama ?? 'Dapur' }}</div></div>
                    </div>
                    <x-status-badge :status="'warn'" :label="'Periksa'" />
                </div>
            </div>
        </x-admin-panel>

        {{-- Activity Feed --}}
        <x-admin::panel title="Aktivitas Terbaru" subtitle="Hari ini" :divider="false" class="max-w-full overflow-hidden">
            <div class="space-y-4">
                <div class="flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">✓</div>
                    <div class="flex-1 min-w-0"><strong>Laporan inspeksi baru</strong><p class="text-sm text-[var(--color-text-muted)]">{{ $recentLaporans->first()->user->name ?? 'Pengawas' }} · {{ $dapurs->first()->nama ?? 'Dapur' }}</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->format('H:i') }}</time>
                </div>
                <div class="flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600">⚑</div>
                    <div class="flex-1 min-w-0"><strong>Temuan perlu tindak lanjut</strong><p class="text-sm text-[var(--color-text-muted)]">Area penyimpanan</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subMinutes(30)->format('H:i') }}</time>
                </div>
                <div class="flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">▣</div>
                    <div class="flex-1 min-w-0"><strong>Status kamera berubah</strong><p class="text-sm text-[var(--color-text-muted)]">Kamera CAM-03</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subHour()->format('H:i') }}</time>
                </div>
                <div class="flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">▤</div>
                    <div class="flex-1 min-w-0"><strong>Laporan inspeksi diperiksa</strong><p class="text-sm text-[var(--color-text-muted)]">Admin pusat</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subHours(2)->format('H:i') }}</time>
                </div>
            </div>
        </x-admin-panel>
    </div>
</div>
@endsection
