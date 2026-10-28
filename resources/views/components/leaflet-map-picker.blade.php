@props([
    'latitude' => null,
    'longitude' => null,
    'nameLat' => 'latitude',
    'nameLng' => 'longitude',
    'readonly' => false,
    'defaultLat' => -6.2088,
    'defaultLng' => 106.8456,
    'height' => '350px',
    'label' => 'Pilih Lokasi di Peta',
    'helpText' => 'Klik peta untuk memilih lokasi, atau gunakan tombol "Gunakan Lokasi Saya". Geser marker untuk penyesuaian.',
    'id' => null,
    // Radius props
    'radius' => 100,
    'nameRadius' => 'geofence_radius',
    'minRadius' => 10,
    'maxRadius' => 5000,
    'editableRadius' => true,
    // Address auto-fill props
    'nameAddress' => 'alamat',
    'reverseGeocode' => true,
    'geocodeUserAgent' => 'GENTA-Pangan-Banten/1.0',
    'addressElementId' => null, // ID of visible textarea/input to sync address with
])

<?php
$id = $id ?? 'map-' . uniqid();
$latId = $id . '-lat';
$lngId = $id . '-lng';
$radiusId = $id . '-radius';
$addressId = $id . '-address';
$containerId = $id . '-container';
$alpineId = 'mapPicker' . uniqid();
?>

<div x-data="{{ $alpineId }}()"
     x-init="init()"
     class="form-field"
     :id="containerId"
     style="position: relative;">
   
    <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="{{ $latId }}">
        {{ $label }}
    </label>

    <div class="relative">
        <!-- Hidden inputs for form submission -->
        <input type="hidden" :name="nameLat" :id="latId" x-model="form.lat">
        <input type="hidden" :name="nameLng" :id="lngId" x-model="form.lng">
        <input type="hidden" :name="nameRadius" :id="radiusId" x-model="form.radius">
        @if ($reverseGeocode)
        <input type="hidden" :name="nameAddress" :id="addressId" x-model="form.address">
        @endif

        <!-- Map Container -->
        <div :id="containerId" 
             class="rounded-xl border border-[var(--color-border)] overflow-hidden" 
             :style="'height: ' + height"
             x-ref="mapContainer"
             style="position: relative; width: 100%;"></div>

        @if (!$readonly)
        <!-- Action Buttons -->
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" 
                    @click="locateMe()" 
                    class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition opacity-100 disabled:opacity-75 disabled:cursor-not-allowed"
                    :disabled="locating || geocoding">
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
            @if ($editableRadius)
            <button type="button" 
                    @click="resetRadius()" 
                    class="btn-action inline-flex items-center gap-2 px-4 py-2 bg-transparent border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface)] transition opacity-100 disabled:opacity-75 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Reset Radius</span>
            </button>
            @endif
        </div>
        @endif

        @if ($editableRadius && !$readonly)
        <!-- Radius Slider -->
        <div class="mt-3 p-3 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-[var(--color-secondary)]">Radius Geofence</span>
                <span class="text-sm font-mono text-[var(--color-primary)]" x-text="form.radius + ' m'"></span>
            </div>
            <input type="range" 
                   :min="minRadius" 
                   :max="maxRadius" 
                   :step="10"
                   x-model="form.radius"
                   @input="onRadiusChange()"
                   class="w-full h-2 bg-[var(--color-border)] rounded-lg appearance-none accent-[var(--color-primary)] cursor-pointer">
            <div class="flex justify-between text-xs text-[var(--color-text-muted)] mt-1">
                <span x-text="minRadius + ' m'"></span>
                <span x-text="maxRadius + ' m'"></span>
            </div>
        </div>
        @endif

        <!-- Coordinates Display -->
        <div class="mt-3 p-3 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div>
                <span class="text-[var(--color-text-muted)] block">Latitude</span>
                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.lat ? form.lat.toFixed(6) : '—'"></span>
            </div>
            <div>
                <span class="text-[var(--color-text-muted)] block">Longitude</span>
                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.lng ? form.lng.toFixed(6) : '—'"></span>
            </div>
            <div>
                <span class="text-[var(--color-text-muted)] block">Radius</span>
                <span class="font-mono font-medium text-[var(--color-secondary)]" x-text="form.radius + ' m'"></span>
            </div>
        </div>

        @if ($reverseGeocode && !$readonly)
        <!-- Detected Address Display -->
        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg" x-show="form.address">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-green-800 mb-1">Alamat Terdeteksi</p>
                    <p class="text-sm text-green-700 truncate" x-text="form.address"></p>
                </div>
                <button type="button" 
                        @click="clearAddress()" 
                        class="flex-shrink-0 text-xs text-green-600 hover:text-green-800 font-medium"
                        title="Hapus alamat otomatis, isi manual">
                    Hapus
                </button>
            </div>
            <p class="text-xs text-green-600 mt-2">Klik "Hapus" untuk isi alamat manual, atau geser marker untuk update otomatis.</p>
        </div>
        @endif

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
            radius: @js($radius ?? 100),
            address: @js($reverseGeocode ? '' : ''),
        },
        map: null,
        marker: null,
        radiusCircle: null,
        locating: false,
        geocoding: false,
        geocodeTimeout: null,
        defaultLat: {{ $defaultLat }},
        defaultLng: {{ $defaultLng }},
        defaultRadius: {{ $radius ?? 100 }},
        minRadius: {{ $minRadius ?? 10 }},
        maxRadius: {{ $maxRadius ?? 5000 }},
        height: '{{ $height }}',
        readonly: {{ $readonly ? 'true' : 'false' }},
        editableRadius: {{ $editableRadius ? 'true' : 'false' }},
        reverseGeocode: {{ $reverseGeocode ? 'true' : 'false' }},
        geocodeUserAgent: '{{ $geocodeUserAgent }}',
        nameAddress: '{{ $nameAddress }}',
        addressElementId: '{{ $addressElementId }}',

        async init() {
            if (typeof L === 'undefined') {
                await this.loadLeaflet();
            }
            // Wait for next tick to ensure DOM is ready
            this.$nextTick(() => {
                this.initMap();
            });
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
            if (!this.$refs.mapContainer) return;
            
            const lat = parseFloat(this.form.lat) || this.defaultLat;
            const lng = parseFloat(this.form.lng) || this.defaultLng;
            const radius = parseFloat(this.form.radius) || this.defaultRadius;

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

            // Force map to recalculate size after a tick
            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 100);

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

            // Radius Circle
            if (this.editableRadius || !this.readonly) {
                this.radiusCircle = L.circle([lat, lng], {
                    radius: radius,
                    color: '#E50000',
                    fillColor: '#E50000',
                    fillOpacity: 0.15,
                    weight: 2,
                    dashArray: '5, 10',
                    interactive: this.editableRadius && !this.readonly,
                }).addTo(this.map);

                // Make circle radius draggable by clicking on its edge
                if (this.editableRadius && !this.readonly) {
                    this.radiusCircle.on('click', (e) => {
                        if (!e.originalEvent) return;
                        // Check if click is near the edge (within ~20px of radius)
                        const clickLatLng = e.latlng;
                        const center = this.marker.getLatLng();
                        const distance = this.map.distance(center, clickLatLng);
                        const radiusMeters = this.radiusCircle.getRadius();
                        const tolerance = 30; // meters
                        
                        if (Math.abs(distance - radiusMeters) < tolerance) {
                            // Click near edge - start radius editing mode
                            this.startRadiusDrag(e);
                        }
                    });
                }
            }

            if (!this.readonly) {
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.form.lat = pos.lat;
                    this.form.lng = pos.lng;
                    this.updateRadiusCirclePosition(pos);
                    this.triggerGeocode(pos.lat, pos.lng);
                });

                this.map.on('click', (e) => {
                    // Don't trigger if clicking on circle edge (handled above)
                    if (e.originalEvent.target._leaflet_pos) return;
                    
                    this.marker.setLatLng(e.latlng);
                    this.form.lat = e.latlng.lat;
                    this.form.lng = e.latlng.lng;
                    this.updateRadiusCirclePosition(e.latlng);
                    this.triggerGeocode(e.latlng.lat, e.latlng.lng);
                });
            }
        },

        // Radius editing via drag
        startRadiusDrag(e) {
            const startLatLng = e.latlng;
            const center = this.marker.getLatLng();
            
            const onMove = (moveEvent) => {
                const newLatLng = moveEvent.latlng;
                const newRadius = this.map.distance(center, newLatLng);
                const clampedRadius = Math.max(this.minRadius, Math.min(this.maxRadius, newRadius));
                this.form.radius = Math.round(clampedRadius);
                this.radiusCircle.setRadius(clampedRadius);
            };
            
            const onUp = () => {
                this.map.off('mousemove', onMove);
                this.map.off('mouseup', onUp);
                this.map.dragging.enable();
            };
            
            this.map.dragging.disable();
            this.map.on('mousemove', onMove);
            this.map.on('mouseup', onUp);
        },

        onRadiusChange() {
            if (this.radiusCircle) {
                this.radiusCircle.setRadius(this.form.radius);
                // Fit bounds to show full circle
                const bounds = this.radiusCircle.getBounds();
                this.map.fitBounds(bounds, { padding: [20, 20], maxZoom: 16 });
            }
        },

        updateRadiusCirclePosition(latlng) {
            if (this.radiusCircle) {
                this.radiusCircle.setLatLng(latlng);
            }
        },

        // Debounced reverse geocoding
        triggerGeocode(lat, lng) {
            if (!this.reverseGeocode) return;
            
            clearTimeout(this.geocodeTimeout);
            this.geocodeTimeout = setTimeout(() => {
                this.reverseGeocodeLatLng(lat, lng);
            }, 500); // 500ms debounce to avoid rate limiting
        },

        async reverseGeocodeLatLng(lat, lng) {
            if (this.geocoding) return;
            
            this.geocoding = true;
            this.showToast('Mencari alamat...', 'info');
            
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&zoom=18&addressdetails=1`;
                const response = await fetch(url, {
                    headers: {
                        'User-Agent': this.geocodeUserAgent,
                        'Accept': 'application/json',
                    },
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data && data.display_name) {
                    // Use display_name or construct from address parts for Indonesia
                    let address = data.display_name;
                    
                    // Optionally, format address for Indonesia (road, city, province)
                    if (data.address) {
                        const parts = [];
                        if (data.address.road) parts.push(data.address.road);
                        if (data.address.village || data.address.suburb || data.address.neighbourhood) {
                            parts.push(data.address.village || data.address.suburb || data.address.neighbourhood);
                        }
                        if (data.address.city || data.address.town || data.address.municipality) {
                            parts.push(data.address.city || data.address.town || data.address.municipality);
                        }
                        if (data.address.state || data.address.province) {
                            parts.push(data.address.state || data.address.province);
                        }
                        if (data.address.country) parts.push(data.address.country);
                        
                        if (parts.length > 0) {
                            address = parts.join(', ');
                        }
                    }
                    
                    this.form.address = address;
                    this.syncAddressToElement(address);
                    this.showToast('Alamat terdeteksi otomatis', 'success');
                }
            } catch (e) {
                console.warn('Reverse geocoding failed:', e);
                // Silent fail - user can still type manually
            } finally {
                this.geocoding = false;
            }
        },

        // Sync address to visible textarea/input element by ID
        syncAddressToElement(address) {
            if (!this.addressElementId) return;
            const el = document.getElementById(this.addressElementId);
            if (el) {
                el.value = address;
                // Trigger input event so Alpine/other listeners react
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },

        clearAddress() {
            this.form.address = '';
            this.syncAddressToElement('');
            this.showToast('Alamat dikosongkan, isi manual', 'info');
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
                    this.triggerGeocode(lat, lng);
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
            this.form.radius = this.defaultRadius;
            this.form.address = '';
            this.syncAddressToElement('');
            this.updateMapPosition(this.defaultLat, this.defaultLng);
            this.showToast('Direset ke lokasi default');
        },

        resetRadius() {
            this.form.radius = this.defaultRadius;
            if (this.radiusCircle) {
                this.radiusCircle.setRadius(this.defaultRadius);
            }
            this.showToast('Radius direset ke default');
        },

        updateMapPosition(lat, lng) {
            if (this.map && this.marker) {
                const pos = [lat, lng];
                this.marker.setLatLng(pos);
                this.updateRadiusCirclePosition(pos);
                this.map.panTo(pos);
                // Ensure map renders correctly
                setTimeout(() => this.map.invalidateSize(), 50);
            }
        },

        showToast(message, type = 'success') {
            const existing = document.querySelector('.map-toast');
            if (existing) existing.remove();
            
            const colors = {
                success: 'bg-green-600',
                info: 'bg-blue-600',
                error: 'bg-red-600',
            };
            
            const toast = document.createElement('div');
            toast.className = `map-toast fixed bottom-6 right-6 ${colors[type] || colors.success} text-white px-5 py-3 rounded-lg shadow-lg z-[9999]`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        },
    }));
});
</script>
@endpush