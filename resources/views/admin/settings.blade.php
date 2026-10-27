@extends('admin.layouts.app', ['pageTitle' => 'Pengaturan Admin'])

@section('content')
<div class="mb-8">
    <div>
        <div class="text-[var(--color-primary)] text-xs font-extrabold uppercase tracking-[1.4px]">{{ settings('app_name_desktop') ?? 'GENTA Pangan Banten' }} &middot; Admin Panel</div>
        <h1 class="text-2xl font-extrabold text-[var(--color-secondary)] mt-1">Pengaturan Sistem</h1>
        <div class="text-[var(--color-text-muted)] text-sm mt-1">Konfigurasi sistem, branding, dan lokasi kantor</div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" x-data="settingsForm()" @submit.prevent="submit()">
    @csrf
    @method('PUT')

    {{-- Branding Section --}}
    <div class="form-section mb-6">
        <div class="form-section-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
            <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
            </span>
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Branding & Identitas</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- App Name Desktop --}}
            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="app_name_desktop">Nama Aplikasi (Desktop/App)</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <input id="app_name_desktop" name="app_name_desktop" type="text" x-model="form.app_name_desktop" class="input-field w-full pl-10" placeholder="Nama aplikasi untuk desktop & mobile app" required aria-required="true">
                </div>
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Nama yang tampil di aplikasi desktop & mobile app</p>
            </div>

            {{-- App Name Web --}}
            <div class="form-field">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="app_name_web">Nama Instansi (Web)</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <input id="app_name_web" name="app_name_web" type="text" x-model="form.app_name_web" class="input-field w-full pl-10" placeholder="Nama instansi untuk web publik" required aria-required="true">
                </div>
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Nama yang tampil di halaman web publik (title, header, footer)</p>
            </div>

            {{-- Tagline --}}
            <div class="form-field lg:col-span-2">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="app_tagline">Tagline</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <input id="app_tagline" name="app_tagline" type="text" x-model="form.app_tagline" class="input-field w-full pl-10" placeholder="Tagline aplikasi">
                </div>
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Deskripsi singkat yang tampil di footer dan meta tags</p>
            </div>
        </div>
    </div>

    {{-- Map / Office Location Section --}}
    <div class="form-section mb-6">
        <div class="form-section-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
            <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </span>
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Lokasi Kantor Pusat</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Map --}}
            <div class="lg:col-span-2">
                <div class="form-field">
                    <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2">Pilih Lokasi di Peta</label>
                    <div class="relative">
                        <input type="hidden" name="office_lat" x-model="form.office_lat">
                        <input type="hidden" name="office_lng" x-model="form.office_lng">
                        
                        <div id="settingsMap" class="rounded-xl border border-[var(--color-border)] overflow-hidden" style="height: 400px; background: var(--color-surface);" x-ref="mapContainer"></div>
                        
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button type="button" @click="locateMe()" class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Gunakan Lokasi Saya</span>
                            </button>
                            <button type="button" @click="resetMap()" class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-transparent border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface)] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Reset ke Default</span>
                            </button>
                        </div>

                        {{-- Coordinates Display --}}
                        <div class="mt-4 p-3 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-[var(--color-text-muted)] block">Latitude</span>
                                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.office_lat"></span>
                            </div>
                            <div>
                                <span class="text-[var(--color-text-muted)] block">Longitude</span>
                                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.office_lng"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="form-field lg:col-span-2">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="office_address">Alamat Lengkap Kantor</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <textarea id="office_address" name="office_address" x-model="form.office_address" class="input-field w-full pl-10 min-h-[100px] resize-y" placeholder="Alamat lengkap kantor pusat"></textarea>
                </div>
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Alamat yang tampil di halaman kontak dan informasi publik</p>
            </div>
        </div>
    </div>

    {{-- General Section --}}
    <div class="form-section mb-6">
        <div class="form-section-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
            <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Umum</h2>
        </div>

        <div class="form-field">
            <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="timezone">Zona Waktu</label>
            <div class="input-with-icon relative">
                <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <select id="timezone" name="timezone" x-model="form.timezone" class="input-field select-enhanced w-full pr-10" required>
                    <option value="Asia/Jakarta">WIB — Asia/Jakarta (UTC+7)</option>
                    <option value="Asia/Makassar">WITA — Asia/Makassar (UTC+8)</option>
                    <option value="Asia/Jayapura">WIT — Asia/Jayapura (UTC+9)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="form-actions mt-6 flex gap-3">
        <button type="submit" :disabled="saving" class="btn-action inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--color-primary)] text-white rounded-lg text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
            <template x-if="saving">
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Menyimpan...</span>
            </template>
            <template x-if="!saving">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan Pengaturan</span>
            </template>
        </button>
    </div>

    {{-- Toast Success --}}
    <div x-show="toast.show" x-transition class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg z-[9999]" x-cloak>
        <span x-text="toast.message"></span>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('settingsForm', () => ({
        form: {
            app_name_desktop: @js($branding['app_name_desktop'] ?? ''),
            app_name_web: @js($branding['app_name_web'] ?? ''),
            app_tagline: @js($branding['app_tagline'] ?? ''),
            office_lat: @js($map['office_lat'] ?? ''),
            office_lng: @js($map['office_lng'] ?? ''),
            office_address: @js($map['office_address'] ?? ''),
            timezone: @js($general['timezone'] ?? 'Asia/Jakarta'),
        },
        saving: false,
        toast: { show: false, message: '' },
        map: null,
        marker: null,
        defaultLat: -6.2088,
        defaultLng: 106.8456,

        async init() {
            await this.initMap();
        },

        async initMap() {
            // Load Leaflet CSS/JS if not present
            if (typeof L === 'undefined') {
                await this.loadLeaflet();
            }

            const lat = parseFloat(this.form.office_lat) || this.defaultLat;
            const lng = parseFloat(this.form.office_lng) || this.defaultLng;

            this.map = L.map(this.$refs.mapContainer, {
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true,
                doubleClickZoom: true,
                boxZoom: true,
                keyboard: true,
                tap: true,
                touchZoom: true,
            }).setView([lat, lng], 16);

            // OSM Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

            // Custom marker icon
            const icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="width: 28px; height: 28px; background: #E50000; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><div style="width: 12px; height: 12px; background: white; border-radius: 50%; margin: 5px auto 0; transform: rotate(45deg);"></div></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -28]
            });

            this.marker = L.marker([lat, lng], { icon, draggable: true }).addTo(this.map);

            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.form.office_lat = pos.lat;
                this.form.office_lng = pos.lng;
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.form.office_lat = e.latlng.lat;
                this.form.office_lng = e.latlng.lng;
            });
        },

        loadLeaflet() {
            return new Promise((resolve) => {
                if (typeof L !== 'undefined') {
                    resolve();
                    return;
                }
                // Load Leaflet CSS
                if (!document.querySelector('link[href*="leaflet.css"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                    link.crossOrigin = '';
                    document.head.appendChild(link);
                }
                // Load Leaflet JS
                if (!document.querySelector('script[src*="leaflet.js"]')) {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                    script.crossOrigin = '';
                    script.onload = () => resolve();
                    document.head.appendChild(script);
                } else {
                    resolve();
                }
            });
        },

        locateMe() {
            if (!navigator.geolocation) {
                this.showToast('Geolocation tidak didukung browser ini');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    this.form.office_lat = lat;
                    this.form.office_lng = lng;
                    this.updateMapPosition(lat, lng);
                    this.showToast('Lokasi Anda digunakan');
                },
                (err) => {
                    this.showToast('Gagal mendapatkan lokasi: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        resetMap() {
            this.form.office_lat = this.defaultLat;
            this.form.office_lng = this.defaultLng;
            this.updateMapPosition(this.defaultLat, this.defaultLng);
            this.showToast('Direset ke lokasi default');
        },

        updateMapPosition(lat, lng) {
            if (this.map && this.marker) {
                const pos = [lat, lng];
                this.marker.setLatLng(pos);
                this.map.panTo(pos);
            }
        },

        async submit() {
            this.saving = true;
            try {
                const formData = new FormData(this.$el);
                const response = await fetch(this.$el.action, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                if (response.ok) {
                    this.showToast(data.message || 'Pengaturan berhasil disimpan');
                } else {
                    this.showToast(data.message || 'Gagal menyimpan pengaturan');
                }
            } catch (e) {
                this.showToast('Terjadi kesalahan: ' + e.message);
            } finally {
                this.saving = false;
            }
        },

        showToast(message) {
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },
    }));
});
</script>
@endpush