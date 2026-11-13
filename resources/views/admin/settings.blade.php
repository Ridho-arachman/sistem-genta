@extends('admin.layouts.app', ['pageTitle' => 'Pengaturan Admin'])

@section('page_content')
<x-admin::page-heading
    eyebrow="{{ settings('app_name_desktop') ?? 'GENTA Pangan Banten' }} · Admin Panel"
    title="Pengaturan Sistem"
    subtitle="Konfigurasi sistem, branding, dan lokasi kantor"
    titleClass="text-2xl font-extrabold text-[var(--color-secondary)]" />

<form action="{{ route('admin.settings.update') }}" method="POST" x-data="settingsForm()" @submit.prevent="submit()">
    @csrf
    @method('PUT')

    {{-- Branding Section --}}
    <x-admin::panel title="Branding & Identitas" icon="image" class="mb-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- App Name Desktop --}}
            <x-admin::form-field label="Nama Aplikasi (Desktop/App)" name="app_name_desktop" required hint="Nama yang tampil di aplikasi desktop & mobile app">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <input id="app_name_desktop" name="app_name_desktop" type="text" x-model="form.app_name_desktop" class="input input-bordered border border-[var(--color-border)] w-full pl-10" placeholder="Nama aplikasi untuk desktop & mobile app" required aria-required="true">
                </div>
            </x-admin::form-field>

            {{-- App Name Web --}}
            <x-admin::form-field label="Nama Instansi (Web)" name="app_name_web" required hint="Nama yang tampil di halaman web publik (title, header, footer)">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <input id="app_name_web" name="app_name_web" type="text" x-model="form.app_name_web" class="input input-bordered border border-[var(--color-border)] w-full pl-10" placeholder="Nama instansi untuk web publik" required aria-required="true">
                </div>
            </x-admin::form-field>

            {{-- Tagline --}}
            <div class="lg:col-span-2">
                <x-admin::form-field label="Tagline" name="app_tagline" hint="Deskripsi singkat yang tampil di footer dan meta tags">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <input id="app_tagline" name="app_tagline" type="text" x-model="form.app_tagline" class="input input-bordered border border-[var(--color-border)] w-full pl-10" placeholder="Tagline aplikasi">
                    </div>
                </x-admin::form-field>
            </div>
        </div>
    </x-admin::panel>

    {{-- Map / Office Location Section --}}
    <x-admin::panel title="Lokasi Kantor Pusat" icon="location" class="mb-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Map --}}
            <div class="lg:col-span-2">
                <x-admin::form-field label="Pilih Lokasi di Peta">
                    <div class="relative">
                        <input type="hidden" name="office_lat" x-model="form.office_lat">
                        <input type="hidden" name="office_lng" x-model="form.office_lng">
                        
                        <div id="settingsMap" class="rounded-xl border border-[var(--color-border)] overflow-hidden h-[400px] bg-[var(--color-surface)]" x-ref="mapContainer"></div>
                        
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button type="button" @click="locateMe()" class="btn btn-secondary inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Gunakan Lokasi Saya</span>
                            </button>
                            <button type="button" @click="resetMap()" class="btn btn-ghost inline-flex items-center gap-2">
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
                </x-admin::form-field>
            </div>

            {{-- Address --}}
            <div class="lg:col-span-2">
                <x-admin::form-field label="Alamat Lengkap Kantor" name="office_address" hint="Alamat yang tampil di halaman kontak dan informasi publik — diisi otomatis saat memilih lokasi di peta">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <textarea id="office_address" name="office_address" x-model="form.office_address" class="input input-bordered border border-[var(--color-border)] w-full pl-10 min-h-[100px] resize-y bg-[var(--color-surface)]" placeholder="Alamat lengkap kantor pusat (akan terisi otomatis dari peta)" readonly></textarea>
                    </div>
                </x-admin::form-field>
            </div>
        </div>
    </x-admin::panel>

    {{-- General Section --}}
    <x-admin::panel title="Umum" icon="clock" class="mb-6">

        <x-admin::form-field label="Zona Waktu" name="timezone" required>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <select id="timezone" name="timezone" x-model="form.timezone" class="input input-bordered border border-[var(--color-border)] w-full pr-10" required>
                    <option value="Asia/Jakarta">WIB — Asia/Jakarta (UTC+7)</option>
                    <option value="Asia/Makassar">WITA — Asia/Makassar (UTC+8)</option>
                    <option value="Asia/Jayapura">WIT — Asia/Jayapura (UTC+9)</option>
                </select>
            </div>
        </x-admin::form-field>
    </x-admin::panel>

    {{-- Form Actions --}}
    <div class="mt-6 flex gap-3">
        <button type="submit" :disabled="saving" class="btn btn-primary inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold opacity-100 disabled:opacity-75 disabled:cursor-not-allowed">
            <template x-if="saving">
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </template>
            <template x-if="!saving">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            </template>
            <span>Simpan Pengaturan</span>
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

        async reverseGeocode(lat, lng) {
            try {
                // Nominatim requires a User-Agent header
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`, {
                    headers: {
                        'User-Agent': 'Genta-Admin-Panel/1.0'
                    }
                });
                const data = await resp.json();
                if (data.display_name) {
                    this.form.office_address = data.display_name;
                } else if (data.address) {
                    // Build address from components if display_name not available
                    const parts = [];
                    if (data.address.road) parts.push(data.address.road);
                    if (data.address.neighbourhood) parts.push(data.address.neighbourhood);
                    if (data.address.suburb) parts.push(data.address.suburb);
                    if (data.address.city) parts.push(data.address.city);
                    if (data.address.state) parts.push(data.address.state);
                    if (data.address.country) parts.push(data.address.country);
                    if (parts.length) {
                        this.form.office_address = parts.join(', ');
                    }
                }
            } catch (err) {
                console.warn('Reverse geocode error:', err);
                // Fallback: show coordinates as address
                this.form.office_address = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)} (Geocoding gagal)`;
            }
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
                this.reverseGeocode(pos.lat, pos.lng);
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.form.office_lat = e.latlng.lat;
                this.form.office_lng = e.latlng.lng;
                this.reverseGeocode(e.latlng.lat, e.latlng.lng);
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
                    this.reverseGeocode(lat, lng);
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
            this.reverseGeocode(this.defaultLat, this.defaultLng);
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(this.$el.action, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    // Handle non-JSON response (e.g., HTML error page)
                    const text = await response.text();
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                }

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