@extends('admin.layouts.app', ['pageTitle' => 'Pengaturan Admin'])

@section('content')
<div class="mb-8">
    <div>
        <div class="text-[var(--color-primary)] text-xs font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
        <h1 class="text-2xl font-extrabold text-[var(--color-secondary)] mt-1">Pengaturan Sistem</h1>
        <div class="text-[var(--color-text-muted)] text-sm mt-1">Konfigurasi sistem dan parameter</div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
        <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">⚙</span>
        <h2 class="text-sm font-bold text-[var(--color-secondary)]">Konfigurasi Umum</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column: App Identity --}}
        <div class="space-y-6">
            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="app_name">Nama Aplikasi</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <input id="app_name" type="text" value="Genta Pengawasan Dapur MBG" class="input-field w-full pl-10" placeholder="Nama aplikasi">
                </div>
            </div>

            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="instansi">Instansi</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <input id="instansi" type="text" value="Genta Provinsi Banten" class="input-field w-full pl-10" placeholder="Nama instansi">
                </div>
            </div>

            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2">Zona Waktu</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <select class="input-field select-enhanced w-full pr-10">
                        <option selected>WIB — Asia/Jakarta</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Right Column: Office Location Map --}}
        <div>
            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2">Lokasi Kantor</label>
                <div class="relative">
                    {{-- Hidden inputs for form submission --}}
                    <input type="hidden" id="office_lat" name="office_lat" value="-6.2088">
                    <input type="hidden" id="office_lng" name="office_lng" value="106.8456">

                    {{-- Map Container --}}
                    <div id="geofenceMap" class="rounded-xl border border-[var(--color-border)] overflow-hidden" style="height: 380px; background: var(--color-surface);"></div>

                    {{-- Map Controls --}}
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button type="button" class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition" id="btnLocateMe">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Gunakan Lokasi Saya</span>
                        </button>
                        <button type="button" class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-transparent border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface)] transition" id="btnResetMap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Reset ke Default</span>
                        </button>
                    </div>

                    {{-- Coordinates Display --}}
                    <div class="mt-4 p-3 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-[var(--color-text-muted)] block">Latitude</span>
                            <span id="displayLat" class="font-mono font-medium text-[var(--color-secondary)]">-6.2088</span>
                        </div>
                        <div>
                            <span class="text-[var(--color-text-muted)] block">Longitude</span>
                            <span id="displayLng" class="font-mono font-medium text-[var(--color-secondary)]">106.8456</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-actions mt-6">
    <button type="button" 
            class="btn-action inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--color-primary)] text-white rounded-lg text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition"
            @click="$dispatch('toast', { message: 'Pengaturan hanya contoh, belum tersimpan ke server.' })">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        <span>Simpan pengaturan (demo)</span>
    </button>
</div>
@endsection

@push('scripts')
<div x-data="officeMap()" x-init="$nextTick(() => init())" id="map-controller" style="display: none;"></div>
@endpush