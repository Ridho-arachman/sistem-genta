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

<div class="panel">
    @if ($dapurs->count() > 0)
        <div class="panel-head">
            <h2>Daftar Dapur</h2>
            <span class="text-sm text-[var(--color-text-muted)]">{{ $dapurs->total() }} dapur terdaftar</span>
            <div class="relative max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="dapurSearch" placeholder="Cari nama dapur, lokasi, atau pengawas…"
                       class="w-full pl-10 pr-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                       x-model="searchQuery"
                       @input="filterDapurs()">
            </div>
        </div>

        <div id="dapurCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-data="dapurCards()">
            <template x-for="dapur in filteredDapurs" :key="dapur.id">
                <article class="kitchen-card group bg-white border border-[var(--color-border)] rounded-xl p-5 hover:border-[var(--color-primary)]/50 hover:shadow-lg transition-all duration-200 cursor-pointer"
                         @click="goToDetail(dapur.id)">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-[var(--color-secondary)] truncate" x-text="dapur.nama"></h3>
                            <p class="text-sm text-[var(--color-text-muted)] mt-1 truncate" x-text="dapur.lokasi || '—'"></p>
                        </div>
                        <span class="status-badge flex-shrink-0" :class="dapur.statusClass" x-text="dapur.status"></span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-[var(--color-text-muted)] mb-3">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>
                        <span class="truncate" x-text="dapur.alamat || 'Alamat tidak tersedia'"></span>
                    </div>

                    <!-- Pengawas -->
                    <div class="mb-3" x-show="dapur.pengawas.length > 0">
                        <p class="text-xs text-[var(--color-text-muted)] mb-1">Pengawas</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="p in dapur.pengawas.slice(0, 3)" :key="p.id">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)] truncate max-w-[100px]"
                                      x-text="p.name"></span>
                            </template>
                            <template x-if="dapur.pengawas.length > 3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)]"
                                      x-text="'+' + (dapur.pengawas.length - 3)"></span>
                            </template>
                        </div>
                    </div>
                    <div class="mb-3" x-show="dapur.pengawas.length === 0">
                        <p class="text-xs text-[var(--color-text-muted)] mb-1">Pengawas</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)]">
                            Belum diassign
                        </span>
                    </div>

                    <!-- Kepala Dapur -->
                    <div class="mb-3" x-show="dapur.kepalaDapur">
                        <p class="text-xs text-[var(--color-text-muted)] mb-1">Kepala Dapur</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#22C55E15] text-[#166534] truncate max-w-[180px]"
                              x-text="dapur.kepalaDapur.name"></span>
                    </div>

                    <div class="border-t border-[var(--color-border)] pt-3">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-[var(--color-text-muted)]">Kamera</span>
                            <span class="font-medium text-[var(--color-secondary)]" x-text="dapur.cctvs_count + ' kamera'"></span>
                        </div>

                        <!-- Progress bar -->
                        <div class="progress-bar h-1.5 bg-[var(--color-surface)] rounded-full overflow-hidden">
                            <div class="h-full bg-[var(--color-primary)] rounded-full transition-all duration-300"
                                 :style="'width: ' + dapur.progress + '%'"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-[var(--color-text-muted)] mt-1">
                            <span x-text="dapur.cctvs_count + '/' + dapur.maxCams + ' kamera online'"></span>
                            <span x-text="dapur.progress + '%'"></span>
                        </div>
                    </div>

                    <!-- Actions dropdown -->
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="dropdown relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1.5 rounded-lg bg-[var(--color-surface)] hover:bg-[var(--color-primary)]/10 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors"
                                    aria-label="Aksi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 top-full mt-1 w-40 bg-white border border-[var(--color-border)] rounded-lg shadow-lg py-1 z-10">
                                <a href="#" @click.prevent="goToDetail(dapur.id)" class="block px-3 py-2 text-sm text-[var(--color-secondary)] hover:bg-[var(--color-surface)]">
                                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail
                                </a>
                                <a :href="'{{ route('admin.dapurs.edit', ':id') }}'.replace(':id', dapur.id)" class="block px-3 py-2 text-sm text-blue-600 hover:bg-[var(--color-surface)]">
                                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>
                                <form :action="'{{ route('admin.dapurs.destroy', ':id') }}'.replace(':id', dapur.id)" method="POST" class="p-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="event.stopPropagation(); return confirm('Yakin hapus dapur ' + dapur.nama + '?')"
                                            class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">
                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            </template>

            <template x-if="filteredDapurs.length === 0">
                <div class="col-span-full p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-[var(--color-text-muted)] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Tidak ada dapur yang cocok</h3>
                    <p class="text-[var(--color-text-muted)]">Coba ubah kata kunci pencarian</p>
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
    Alpine.data('dapurCards', () => ({
        searchQuery: '',
        dapurs: @json($dapurData ?? []),
        get filteredDapurs() {
            if (!this.searchQuery) return this.dapurs;
            const q = this.searchQuery.toLowerCase();
            return this.dapurs.filter(d =>
                d.nama.toLowerCase().includes(q) ||
                (d.lokasi && d.lokasi.toLowerCase().includes(q)) ||
                d.pengawas.some(p => p.name.toLowerCase().includes(q)) ||
                (d.kepalaDapur && d.kepalaDapur.name.toLowerCase().includes(q))
            );
        },
        goToDetail(id) {
            window.location.href = '{{ route('admin.dapurs.show', ':id') }}'.replace(':id', id);
        },
        filterDapurs() {
            // Reactive via getter
        }
    }));
});
</script>
@endpush

@push('styles')
<style>
.kitchen-card {
    position: relative;
}
.kitchen-card:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
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