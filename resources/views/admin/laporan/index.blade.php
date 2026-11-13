@extends('admin.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('page_content')
<x-admin::page-heading title="Laporan Pengawasan" subtitle="Rekap inspeksi dan tindak lanjut">
    <x-slot:actions>
        <a href="?format=xlsx" class="btn btn-primary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Excel
        </a>
        <a href="?format=pdf" class="btn btn-secondary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11h6m-6 0v6m-6-3h6v-3m0 0l3-3m-3 3l-3-3"></path></svg>
            PDF
        </a>
    </x-slot:actions>
</x-admin::page-heading>

<x-admin::panel x-data="laporanFilter()">
    <x-slot:head>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5 pb-4 border-b border-[var(--color-border)]">
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Daftar Laporan</h2>
        
        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="sr-only" for="search">Cari laporan</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        id="search" 
                        type="text" 
                        class="input input-bordered border border-[var(--color-border)] w-full pl-10" 
                        placeholder="Cari lokasi, pengawas..." 
                        x-model="search"
                        @input="filterRows()"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div class="min-w-[160px]">
                <label class="sr-only" for="filterStatus">Filter status</label>
                <div class="relative">
                    <select
                        id="filterStatus"
                        class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none"
                        x-model="statusFilter"
                        @change="filterRows()"
                    >
                        <option value="">Semua Status</option>
                        <template x-for="s in uniqueStatuses" :key="s">
                            <option :value="s" x-text="s"></option>
                        </template>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Lokasi Filter -->
            <div class="min-w-[160px]">
                <label class="sr-only" for="filterLokasi">Filter lokasi</label>
                <div class="relative">
                    <select
                        id="filterLokasi"
                        class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none"
                        x-model="lokasiFilter"
                        @change="filterRows()"
                    >
                        <option value="">Semua Lokasi</option>
                        <template x-for="l in uniqueLokasi" :key="l">
                            <option :value="l" x-text="l"></option>
                        </template>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Date Range -->
            <div class="flex gap-2 min-w-[320px]">
                <label class="sr-only" for="filterFrom">Tanggal mulai</label>
                <input
                    id="filterFrom"
                    type="date"
                    class="input input-bordered border border-[var(--color-border)] w-full"
                    x-model="dateFrom"
                    @change="filterRows()"
                >
                <label class="sr-only" for="filterTo">Tanggal akhir</label>
                <input
                    id="filterTo"
                    type="date"
                    class="input input-bordered border border-[var(--color-border)] w-full"
                    x-model="dateTo"
                    @change="filterRows()"
                >
            </div>

            <!-- Reset Button -->
            <button 
                type="button" 
                class="btn btn-secondary" 
                @click="resetFilters()"
                :disabled="!hasActiveFilters"
                :class="{ 'opacity-50 cursor-not-allowed': !hasActiveFilters }"
            >
                Reset
            </button>
        </div>
    </x-slot:head>

    <div class="overflow-x-auto">
        <x-admin::table>
            <x-slot:head>
                <th>TANGGAL</th>
                <th>LOKASI</th>
                <th>PENGAWAS</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </x-slot:head>
                <template x-for="row in filteredRows" :key="row.id">
                    <tr>
                        <td x-text="row.tanggal"></td>
                        <td x-text="row.lokasi"></td>
                        <td x-text="row.pengawas"></td>
                        <td>
                            <span class="badge" :class="row.badgeClass" x-text="row.status"></span>
                        </td>
                        <td>
                            <a :href="'/admin/laporan/' + row.id" class="text-[var(--color-primary)] hover:underline text-sm font-medium">Lihat</a>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredRows.length === 0">
                    <td colspan="5" class="text-center py-8 text-[var(--color-text-muted)]">
                        Tidak ada data yang cocok
                    </td>
                </tr>
            </x-admin::table>
    </div>

    <!-- Results Info -->
    <div class="mt-3 text-sm text-[var(--color-text-muted)]" x-show="filteredRows.length !== allRows.length">
        Menampilkan <span x-text="filteredRows.length"></span> dari <span x-text="allRows.length"></span> laporan
    </div>
</x-admin::panel>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanFilter', () => ({
        search: '',
        statusFilter: '',
        lokasiFilter: '',
        dateFrom: '',
        dateTo: '',
        
        // Data dari server (di-inject via Blade)
        allRows: @json($laporanRows),
        
        filteredRows: [],
        
        init() {
            this.filteredRows = [...this.allRows];
        },
        
        get uniqueStatuses() {
            return [...new Set(this.allRows.map(r => r.status))];
        },
        
        get uniqueLokasi() {
            return [...new Set(this.allRows.map(r => r.lokasi))];
        },
        
        get hasActiveFilters() {
            return this.search || this.statusFilter || this.lokasiFilter || this.dateFrom || this.dateTo;
        },
        
        filterRows() {
            let rows = [...this.allRows];
            
            // Search filter (lokasi, pengawas)
            if (this.search) {
                const q = this.search.toLowerCase();
                rows = rows.filter(r => 
                    r.lokasi.toLowerCase().includes(q) || 
                    r.pengawas.toLowerCase().includes(q)
                );
            }
            
            // Status filter
            if (this.statusFilter) {
                rows = rows.filter(r => r.status === this.statusFilter);
            }
            
            // Lokasi filter
            if (this.lokasiFilter) {
                rows = rows.filter(r => r.lokasi === this.lokasiFilter);
            }
            
            // Date range filter
            if (this.dateFrom) {
                rows = rows.filter(r => r.date >= this.dateFrom);
            }
            if (this.dateTo) {
                rows = rows.filter(r => r.date <= this.dateTo);
            }
            
            this.filteredRows = rows;
        },
        
        resetFilters() {
            this.search = '';
            this.statusFilter = '';
            this.lokasiFilter = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.filteredRows = [...this.allRows];
        }
    }));
});
</script>
@endpush