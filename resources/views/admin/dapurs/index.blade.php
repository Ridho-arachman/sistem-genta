@extends('admin.layouts.app', ['pageTitle' => 'Data Dapur'])

@section('page_content')
<x-admin::page-heading title="Data Dapur" subtitle="Kelola dapur pangan dan geofence absensi">
    @if (auth()->user()->isAdmin())
    <x-slot:actions>
        <a href="{{ route('admin.dapurs.create') }}" class="btn btn-primary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Dapur</span>
        </a>
        <a href="{{ route('admin.dapurs.archive') }}"
           class="btn btn-secondary">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span>Arsip Dapur</span>
        </a>
    </x-slot:actions>
    @endif
</x-admin::page-heading>

@php $prefix = strtok(Route::currentRouteName(), '.'); @endphp
<div x-data="dapurList(@js($allKecamatanMap ?? []), @js($dapurData ?? []), @js(route($prefix.'.dapurs.show', ':id')))">
<x-admin::panel>
    <x-slot:head>
        {{-- Panel head: jumlah dapur + pencarian --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">{{ $dapurs->total() }} dapur terdaftar</h2>
            <div class="relative w-full sm:w-[240px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="dapurSearch" placeholder="Cari nama dapur, wilayah, atau pengawas…"
                       class="w-full pl-10 pr-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                       x-model="searchQuery"
                       @input="filterDapurs()">
            </div>
        </div>
    </x-slot:head>

        {{-- Filter Wilayah (hanya admin: controller hanya memfilter untuk admin) --}}
        @if (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b border-[var(--color-border)]">
            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]">Provinsi</label>
                <input type="text" value="{{ config('banten.provinsi') }}" class="input input-bordered" readonly>
                <input type="hidden" name="provinsi" value="{{ config('banten.provinsi') }}">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="filterKabupaten">
                    Kabupaten / Kota <span class="text-xs text-[var(--color-primary)]">*</span>
                </label>
                <select id="filterKabupaten" x-model="filter.kabupaten_kota" @change="onKabupatenChange($event.target.value)"
                        class="select select-bordered text-sm">
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    @foreach ($filterOptions['kabupaten_kota'] as $kab)
                        <option value="{{ $kab }}">{{ $kab }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="filterKecamatan">
                    Kecamatan
                </label>
                <select id="filterKecamatan" x-model="filter.kecamatan" @change="applyFilters()"
                        class="select select-bordered text-sm"
                        :disabled="kecamatanOptions.length === 0"
                        x-html="getKecamatanOptionsHtml()">
                </select>
            </div>
        </div>
        @endif

    @if ($dapurs->count() > 0)
        <div id="dapurList" class="space-y-3">
            <template x-for="dapur in filteredDapurs" :key="dapur.id">
                <article class="relative group bg-white focus-within:outline-2 focus-within:outline-[var(--color-primary)] focus-within:outline-offset-2 bg-white border border-[var(--color-border)] rounded-2xl p-[18px] hover:border-[var(--color-primary)]/50 hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                         @click="goToDetail(dapur.id)">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[13px] font-extrabold text-[var(--color-secondary)] truncate" x-text="dapur.nama"></h3>
                        <div class="text-[11px] text-[var(--color-text-muted)] mt-[5px] truncate" x-text="dapur.wilayahPengawas"></div>
                        <div class="w-full bg-[var(--color-surface)] rounded-full h-[5px] mt-[11px] overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="dapur.progress">
                            <div class="bg-[var(--color-primary)] h-full rounded-full transition-[width] duration-300" :style="`width: ${dapur.progress}%`"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-[var(--color-text-muted)] mt-[7px]">
                            <span x-text="dapur.cctvs_count_online + '/' + dapur.cctvs_count_total + ' kamera online'"></span>
                            <span class="font-medium text-[var(--color-secondary)]" x-text="dapur.progress + '%'"></span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 sm:ml-4">
                        <span class="badge" :class="{ 'badge-success': dapur.statusClass === 'ok', 'badge-warning': dapur.statusClass === 'warn', 'badge-error': dapur.statusClass === 'off' }" x-text="dapur.status"></span>
                        @if (auth()->user()->isAdmin())
                        <button type="button"
                                x-on:click.stop="$dispatch('open-delete-modal', { url: '/admin/dapurs/' + dapur.id, name: dapur.nama })"
                                class="w-8 h-8 grid place-items-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-colors"
                                title="Arsipkan Dapur">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
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
        <x-admin::empty-state title="Belum Ada Data Dapur" message="Mulai dengan menambahkan dapur pertama">
            <x-slot:icon>
                <div class="w-16 h-16 mx-auto rounded-full bg-[var(--color-surface)] flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </x-slot:icon>
            @if (auth()->user()->isAdmin())
            <x-slot:action>
                <a href="{{ route('admin.dapurs.create') }}" class="btn btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Dapur
                </a>
            </x-slot:action>
            @endif
        </x-admin::empty-state>
    @endif
</x-admin::panel>
</div>

<!-- Alpine Delete Modal -->
<div x-data="{ show: false, url: '', name: '' }"
     x-on:open-delete-modal.window="show = true; url = $event.detail.url; name = $event.detail.name"
     x-show="show"
     x-cloak
     class="modal z-[1001]"
     :class="show ? 'modal-open' : ''"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="modal-box max-w-md"
         x-show="show"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 grid place-items-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Arsipkan Dapur</h3>
        </div>
        <p class="text-gray-600 mb-6">Dapur <span x-text="name" class="font-semibold"></span> dan semua CCTV di dalamnya akan dipindahkan ke arsip. Tindakan ini bisa dibatalkan via restore.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" x-on:click="show = false" class="btn btn-secondary">Batal</button>
            <form :action="url" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary">Arsipkan</button>
            </form>
        </div>
    </div>
</div>
@endsection
