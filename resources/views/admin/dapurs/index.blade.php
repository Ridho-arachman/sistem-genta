@extends('admin.layouts.app', ['pageTitle' => 'Data Dapur'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Data Dapur</h1>
        <div class="muted">Kelola dapur pangan dan geofence absensi</div>
    </div>
    <a href="{{ route('admin.dapurs.create') }}" class="btn-action btn-action-primary">
        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Tambah Dapur</span>
    </a>
</div>

<div class="panel" x-data="dapurList()">
    {{-- Filter Wilayah --}}
    <div class="panel-head" style="flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full sm:w-auto">
            <div class="form-field">
                <label class="form-label">Provinsi</label>
                <input type="text" value="Banten" class="input-field" readonly>
                <input type="hidden" name="provinsi" value="Banten">
            </div>

            <div class="form-field">
                <label class="form-label" for="filterKabupaten">
                    Kabupaten / Kota <span class="required">*</span>
                </label>
                <select id="filterKabupaten" x-model="filter.kabupaten_kota" @change="onKabupatenChange($event.target.value)"
                        class="input-field select-enhanced text-sm">
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    @foreach ($filterOptions['kabupaten_kota'] as $kab)
                        <option value="{{ $kab }}">{{ $kab }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-field">
                <label class="form-label" for="filterKecamatan">
                    Kecamatan
                </label>
                <select id="filterKecamatan" x-model="filter.kecamatan" @change="applyFilters()"
                        class="input-field select-enhanced text-sm"
                        :disabled="kecamatanOptions.length === 0"
                        x-html="getKecamatanOptionsHtml()">
                </select>
            </div>
        </div>
        
        <div class="flex-1 min-w-[250px]">
            <h2 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Daftar Dapur</h2>
            <span class="text-sm text-[var(--color-text-muted)]">{{ $dapurs->total() }} dapur terdaftar</span>
        </div>

        <div class="relative max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="dapurSearch" placeholder="Cari nama dapur, lokasi, atau pengawas…"
                   class="w-full pl-10 pr-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                   x-model="searchQuery"
                   @input="filterDapurs()">
        </div>
    </div>

    @if ($dapurs->count() > 0)
        <div id="dapurList" class="space-y-3">
            <template x-for="dapur in filteredDapurs" :key="dapur.id">
                <article class="dapur-row group bg-white border border-[var(--color-border)] rounded-xl p-4 hover:border-[var(--color-primary)]/50 hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                         @click="goToDetail(dapur.id)">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-[var(--color-secondary)] truncate" x-text="dapur.nama"></h3>
                        <div class="flex flex-wrap gap-2 mt-1.5">
                            <template x-if="dapur.provinsi || dapur.kabupaten_kota || dapur.kecamatan">
                                <span class="text-xs px-2 py-0.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full text-[var(--color-text-muted)]" 
                                      x-text="dapur.provinsi + (dapur.kabupaten_kota ? ' · ' + dapur.kabupaten_kota : '') + (dapur.kecamatan ? ' · Kec. ' + dapur.kecamatan : '')"></span>
                            </template>
                            <template x-if="!dapur.provinsi && !dapur.kabupaten_kota && !dapur.kecamatan">
                                <span class="text-xs text-[var(--color-text-muted)]" x-text="dapur.lokasiPengawas"></span>
                            </template>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm text-[var(--color-text-muted)]">
                            <span x-text="dapur.cctvs_count + '/' + dapur.maxCams + ' kamera online'"></span>
                            <span class="font-medium text-[var(--color-secondary)]" x-text="dapur.progress + '%'"></span>
                        </div>
                        <div class="progress-bar h-1.5 bg-[var(--color-surface)] rounded-full overflow-hidden mt-2">
                            <div class="h-full bg-[var(--color-primary)] rounded-full transition-all duration-300"
                                 :style="'width: ' + dapur.progress + '%'"></div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 sm:ml-4">
                        <span class="status-badge" :class="dapur.statusClass" x-text="dapur.status"></span>
                    </div>
                </article>
            </template>

            <template x-if="filteredDapurs.length === 0">
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-[var(--color-text-muted)] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Tidak ada dapur yang cocok</h3>
                    <p class="text-[var(--color-text-muted)]">Coba ubah kata kunci pencarian atau filter wilayah</p>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t border-[var(--color-border)]" x-show="filteredDapurs.length > 0">
            {{ $dapurs->links() }}
        </div>

    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-[var(--color-surface)] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Belum Ada Data Dapur</h3>
            <p class="text-[var(--color-text-muted)] mb-4">Mulai dengan menambahkan dapur pertama</p>
            <a href="{{ route('admin.dapurs.create') }}" class="btn-action btn-action-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Dapur
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurList', () => ({
        searchQuery: '',
        filter: {
            kabupaten_kota: '',
            kecamatan: '',
        },
        allKecamatan: @json($allKecamatan ?? []),
        dapurs: @json($dapurData ?? []),
        
        get kecamatanOptions() {
            const kab = this.filter.kabupaten_kota;
            return kab ? (this.allKecamatan[kab] || []) : [];
        },
        
        getKecamatanOptionsHtml() {
            let html = '<option value="">-- Pilih Kecamatan --</option>';
            for (const kec of this.kecamatanOptions) {
                const selected = this.filter.kecamatan === kec ? 'selected' : '';
                html += `<option value="${kec}" ${selected}>${kec}</option>`;
            }
            return html;
        },
        
        onKabupatenChange(kab) {
            this.filter.kabupaten_kota = kab;
            this.filter.kecamatan = '';
        },
        
        get filteredDapurs() {
            let result = this.dapurs;
            
            // Search filter
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(d =>
                    d.nama.toLowerCase().includes(q) ||
                    (d.lokasiPengawas && d.lokasiPengawas.toLowerCase().includes(q)) ||
                    (d.lokasi && d.lokasi.toLowerCase().includes(q)) ||
                    (d.provinsi && d.provinsi.toLowerCase().includes(q)) ||
                    (d.kabupaten_kota && d.kabupaten_kota.toLowerCase().includes(q)) ||
                    (d.kecamatan && d.kecamatan.toLowerCase().includes(q)) ||
                    d.pengawas.some(p => p.name.toLowerCase().includes(q)) ||
                    (d.kepalaDapur && d.kepalaDapur.name.toLowerCase().includes(q))
                );
            }
            
            // Wilayah filters
            if (this.filter.kabupaten_kota) {
                result = result.filter(d => d.kabupaten_kota === this.filter.kabupaten_kota);
            }
            if (this.filter.kecamatan) {
                result = result.filter(d => d.kecamatan === this.filter.kecamatan);
            }
            
            return result;
        },
        
        goToDetail(id) {
            window.location.href = '{{ route('admin.dapurs.show', ':id') }}'.replace(':id', id);
        },
        
        filterDapurs() {
            // Reactive via getter
        },
        
        applyFilters() {
            // Reactive via getter - just trigger re-render
            this.filterDapurs();
        }
    }));
});
</script>
@endpush

@push('styles')
<style>
.dapur-row {
    position: relative;
}
.dapur-row:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.status-badge.ok {
    background: #dcfce7;
    color: #166534;
}
.status-badge.warn {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.off {
    background: #fee2e2;
    color: #991b1b;
}
.progress-bar {
    background: var(--color-surface);
}
</style>
@endpush