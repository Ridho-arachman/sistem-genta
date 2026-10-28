@props([
    'latitude' => null,
    'longitude' => null,
    'nameLat' => 'latitude',
    'nameLng' => 'longitude',
    'readonly' => false,
    'defaultLat' => -6.2088,
    'defaultLng' => 106.8456,
    'height' => '300px',
    'label' => 'Pilih Lokasi di Peta',
    'helpText' => 'Klik peta untuk memilih lokasi, atau gunakan tombol "Gunakan Lokasi Saya"',
    'id' => null,
])

<?php
$id = $id ?? 'map-' . uniqid();
$latId = $id . '-lat';
$lngId = $id . '-lng';
$containerId = $id . '-container';
$alpineId = 'mapPicker' . uniqid();
?>

<div x-data="{{ $alpineId }}()"
     x-init="init()"
     class="form-field"
     :id="containerId">
    
    <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="{{ $latId }}">
        {{ $label }}
    </label>

    <div class="relative">
        <!-- Hidden inputs for form submission -->
        <input type="hidden" :name="nameLat" :id="latId" x-model="form.lat">
        <input type="hidden" :name="nameLng" :id="lngId" x-model="form.lng">

        <!-- Map Container -->
        <div :id="containerId" 
             class="rounded-xl border border-[var(--color-border)] overflow-hidden" 
             :style="'height: ' + height"
             x-ref="mapContainer"></div>

        @if (!$readonly)
        <!-- Action Buttons -->
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" 
                    @click="locateMe()" 
                    class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition opacity-100 disabled:opacity-75 disabled:cursor-not-allowed"
                    :disabled="locating">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-text="locating ? 'Mencari lokasi...' : 'Gunakan Lokasi Saya'"></span>
            </button>
            <button type="button" 
                    @click="resetMap()" 
                    class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-transparent border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface)] transition opacity-100 disabled:opacity-75 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Reset ke Default</span>
            </button>
        </div>
        @endif

        <!-- Coordinates Display -->
        <div class="mt-3 p-3 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-[var(--color-text-muted)] block">Latitude</span>
                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.lat || '—'"></span>
            </div>
            <div>
                <span class="text-[var(--color-text-muted)] block">Longitude</span>
                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.lng || '—'"></span>
            </div>
        </div>

        @if ($helpText && !$readonly)
        <p class="mt-1 text-xs text-[var(--color-text-muted)]">{{ $helpText }}</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('{{ $alpineId }}', () => ({
        form: {
            lat: @js($latitude ?? ''),
            lng: @js($longitude ?? ''),
        },
        map: null,
        marker: null,
        locating: false,
        defaultLat: {{ $defaultLat }},
        defaultLng: {{ $defaultLng }},
        height: '{{ $height }}',
        readonly: {{ $readonly ? 'true' : 'false' }},

        async init() {
            if (typeof L === 'undefined') {
                await this.loadLeaflet();
            }
            this.initMap();
        },

        async loadLeaflet() {
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

        initMap() {
            const lat = parseFloat(this.form.lat) || this.defaultLat;
            const lng = parseFloat(this.form.lng) || this.defaultLng;

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

            this.marker = L.marker([lat, lng], { 
                icon, 
                draggable: !this.readonly 
            }).addTo(this.map);

            if (!this.readonly) {
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.form.lat = pos.lat;
                    this.form.lng = pos.lng;
                });

                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.form.lat = e.latlng.lat;
                    this.form.lng = e.latlng.lng;
                });
            }
        },

        locateMe() {
            if (!navigator.geolocation) {
                this.showToast('Geolocation tidak didukung browser ini');
                return;
            }

            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    this.form.lat = lat;
                    this.form.lng = lng;
                    this.updateMapPosition(lat, lng);
                    this.locating = false;
                    this.showToast('Lokasi Anda digunakan');
                },
                (err) => {
                    this.locating = false;
                    this.showToast('Gagal mendapatkan lokasi: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        resetMap() {
            this.form.lat = this.defaultLat;
            this.form.lng = this.defaultLng;
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

        showToast(message) {
            // Simple toast - you can enhance this
            const existing = document.querySelector('.map-toast');
            if (existing) existing.remove();
            
            const toast = document.createElement('div');
            toast.className = 'map-toast fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg z-[9999]';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        },
    }));
});
</script>
@endpush