@extends('admin.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Laporan Pengawasan</h1>
        <div class="muted">Rekap inspeksi dan tindak lanjut</div>
    </div>
    <a href="#" class="btn-light">⇩ Ekspor</a>
</div>

<div class="panel" x-data="laporanFilter()">
    <div class="panel-head flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-4">
        <h2>Daftar Laporan</h2>
        
        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="sr-only" for="search">Cari laporan</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        id="search" 
                        type="text" 
                        class="input-field w-full pl-10" 
                        placeholder="Cari lokasi, pengawas..." 
                        x-model="search"
                        @input="filterRows()"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div class="min-w-[160px]">
                <label class="sr-only" for="filterStatus">Filter status</label>
                <select 
                    id="filterStatus" 
                    class="input-field select-enhanced w-full" 
                    x-model="statusFilter"
                    @change="filterRows()"
                >
                    <option value="">Semua Status</option>
                    <template x-for="s in uniqueStatuses" :key="s">
                        <option :value="s" x-text="s"></option>
                    </template>
                </select>
            </div>

            <!-- Lokasi Filter -->
            <div class="min-w-[160px]">
                <label class="sr-only" for="filterLokasi">Filter lokasi</label>
                <select 
                    id="filterLokasi" 
                    class="input-field select-enhanced w-full" 
                    x-model="lokasiFilter"
                    @change="filterRows()"
                >
                    <option value="">Semua Lokasi</option>
                    <template x-for="l in uniqueLokasi" :key="l">
                        <option :value="l" x-text="l"></option>
                    </template>
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex gap-2 min-w-[320px]">
                <label class="sr-only" for="filterFrom">Tanggal mulai</label>
                <input 
                    id="filterFrom" 
                    type="date" 
                    class="input-field" 
                    x-model="dateFrom"
                    @change="filterRows()"
                >
                <label class="sr-only" for="filterTo">Tanggal akhir</label>
                <input 
                    id="filterTo" 
                    type="date" 
                    class="input-field" 
                    x-model="dateTo"
                    @change="filterRows()"
                >
            </div>

            <!-- Reset Button -->
            <button 
                type="button" 
                class="btn-action btn-action-secondary" 
                @click="resetFilters()"
                :disabled="!hasActiveFilters"
                :class="{ 'opacity-50 cursor-not-allowed': !hasActiveFilters }"
            >
                Reset
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-th">TANGGAL</th>
                    <th class="table-th">LOKASI</th>
                    <th class="table-th">PENGAWAS</th>
                    <th class="table-th">STATUS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
                <template x-for="row in filteredRows" :key="row.id">
                    <tr>
                        <td class="table-td" x-text="row.tanggal"></td>
                        <td class="table-td" x-text="row.lokasi"></td>
                        <td class="table-td" x-text="row.pengawas"></td>
                        <td class="table-td">
                            <span class="badge" :class="row.badgeClass" x-text="row.status"></span>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredRows.length === 0">
                    <td colspan="4" class="table-td text-center py-8 text-[var(--color-text-muted)]">
                        Tidak ada data yang cocok
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Results Info -->
    <div class="mt-3 text-sm text-[var(--color-text-muted)]" x-show="filteredRows.length !== allRows.length">
        Menampilkan <span x-text="filteredRows.length"></span> dari <span x-text="allRows.length"></span> laporan
    </div>
</div>
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
        
        // Demo data - same as original static rows
        allRows: [
            { id: 1, tanggal: '{{ now()->format("d M Y") }}', lokasi: 'Serang 01', pengawas: 'Siti Rahma', status: 'Diperiksa', badgeClass: 'badge-success', date: '{{ now()->format("Y-m-d") }}' },
            { id: 2, tanggal: '{{ now()->subDay()->format("d M Y") }}', lokasi: 'Cilegon 02', pengawas: 'Andi Saputra', status: 'Tindak lanjut', badgeClass: 'badge-warning', date: '{{ now()->subDay()->format("Y-m-d") }}' },
            { id: 3, tanggal: '{{ now()->subDay()->format("d M Y") }}', lokasi: 'Pandeglang 01', pengawas: 'Rina Putri', status: 'Selesai', badgeClass: 'badge-success', date: '{{ now()->subDay()->format("Y-m-d") }}' },
            { id: 4, tanggal: '{{ now()->subDays(2)->format("d M Y") }}', lokasi: 'Lebak 03', pengawas: 'Budi Santoso', status: 'Temuan', badgeClass: 'badge-danger', date: '{{ now()->subDays(2)->format("Y-m-d") }}' },
        ],
        
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