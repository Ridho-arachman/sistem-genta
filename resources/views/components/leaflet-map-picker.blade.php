@props([
    'latitude' => null,
    'longitude' => null,
    'nameLat' => 'latitude',
    'nameLng' => 'longitude',
    'readonly' => false,
    'defaultLat' => -6.2,  // Banten center
    'defaultLng' => 106.0, // Banten center
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
    // Wilayah auto-fill props
    'nameKecamatan' => 'kecamatan',
    'nameKabupaten' => 'kabupaten_kota',
    'nameProvinsi' => 'provinsi',
    'kecamatanElementId' => null,
    'kabupatenElementId' => null,
    'provinsiElementId' => null,
    // Banten restriction props
    'restrictToBanten' => true,
    'bantenBounds' => [[-6.55, 105.6], [-5.75, 107.0]], // [southWest, northEast]
    'bantenCenter' => [-6.2, 106.0],
    'bantenZoom' => 10,
])

<?php
$id = $id ?? 'map-' . uniqid();
$latId = $id . '-lat';
$lngId = $id . '-lng';
$radiusId = $id . '-radius';
$addressId = $id . '-address';
$kecamatanId = $id . '-kecamatan';
$kabupatenId = $id . '-kabupaten';
$provinsiId = $id . '-provinsi';
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
        <input type="hidden" :name="nameKecamatan" :id="kecamatanId" x-model="form.kecamatan">
        <input type="hidden" :name="nameKabupaten" :id="kabupatenId" x-model="form.kabupaten_kota">
        <input type="hidden" :name="nameProvinsi" :id="provinsiId" x-model="form.provinsi">
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
            kecamatan: @js($reverseGeocode ? '' : ''),
            kabupaten_kota: @js($reverseGeocode ? '' : ''),
            provinsi: @js($reverseGeocode ? '' : ''),
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
        kecamatanElementId: '{{ $kecamatanElementId }}',
        kabupatenElementId: '{{ $kabupatenElementId }}',
        provinsiElementId: '{{ $provinsiElementId }}',
        // Banten restriction
        restrictToBanten: {{ $restrictToBanten ? 'true' : 'false' }},
        bantenBounds: @js($bantenBounds),
        bantenCenter: @js($bantenCenter),
        bantenZoom: {{ $bantenZoom }},

        async init() {
            if (typeof L === 'undefined') {
                await this.loadLeaflet();
            }
            // Wait for next tick to ensure DOM is ready
            this.$nextTick(() => {
                this.initMap();
            });
        },

        // Check if coordinates are within Banten bounds
        isWithinBanten(lat, lng) {
            if (!this.restrictToBanten) return true;
            const bounds = this.bantenBounds;
            return lat >= bounds[0][0] && lat <= bounds[1][0] && lng >= bounds[0][1] && lng <= bounds[1][1];
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
                maxBounds: this.restrictToBanten ? L.latLngBounds(this.bantenBounds) : null,
                maxBoundsViscosity: 1.0, // Prevent dragging outside bounds
            }).setView([lat, lng], this.restrictToBanten ? this.bantenZoom : 16);

            // If restrictToBanten, also set initial view to Banten center if no coordinates
            if (this.restrictToBanten && (!this.form.lat || !this.form.lng)) {
                this.map.setView(this.bantenCenter, this.bantenZoom);
            }

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
                    // Validate Banten bounds
                    if (this.restrictToBanten && !this.isWithinBanten(pos.lat, pos.lng)) {
                        this.showToast('Lokasi harus berada di wilayah Banten', 'error');
                        // Reset marker to previous valid position
                        this.marker.setLatLng([this.form.lat, this.form.lng]);
                        return;
                    }
                    this.form.lat = pos.lat;
                    this.form.lng = pos.lng;
                    this.updateRadiusCirclePosition(pos);
                    this.triggerGeocode(pos.lat, pos.lng);
                });

                this.map.on('click', (e) => {
                    // Don't trigger if clicking on circle edge (handled above)
                    if (e.originalEvent.target._leaflet_pos) return;
                    
                    // Validate Banten bounds
                    if (this.restrictToBanten && !this.isWithinBanten(e.latlng.lat, e.latlng.lng)) {
                        this.showToast('Lokasi harus berada di wilayah Banten', 'error');
                        return;
                    }
                    
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
                    
                    // Optionally, format address for Indonesia with more granular details
                    let kecamatan = null;
                    let kabupaten = null;
                    let provinsi = null;
                    
                    if (data.address) {
                        const parts = [];
                        
                        // House/building level detail (most granular)
                        if (data.address.house_number) {
                            const housePart = data.address.road 
                                ? `${data.address.road} ${data.address.house_number}`
                                : data.address.house_number;
                            parts.push(housePart);
                        } else if (data.address.road) {
                            parts.push(data.address.road);
                        }
                        
                        // Building name if available
                        if (data.address.building && !parts.some(p => p.includes(data.address.building))) {
                            parts.push(data.address.building);
                        }
                        
                        // Hamlet / Dusun / RW / RT level
                        if (data.address.hamlet) parts.push(data.address.hamlet);
                        if (data.address.subdivision) parts.push(data.address.subdivision);
                        
                        // Village / Kelurahan / Desa level
                        if (data.address.village) parts.push(data.address.village);
                        else if (data.address.suburb) parts.push(data.address.suburb);
                        else if (data.address.neighbourhood) parts.push(data.address.neighbourhood);
                        
                        // Subdistrict / Kecamatan level - prioritize subdistrict > city_district > district
                        // Note: in Indonesia, 'district' often = kecamatan, but can sometimes be ambiguous
                        kecamatan = data.address.subdistrict || data.address.city_district || data.address.district;
                        if (kecamatan) parts.push(kecamatan);
                        
                        // City / Kota / Kabupaten level - prioritize city > municipality > county > town
                        kabupaten = data.address.city || data.address.municipality || data.address.county || data.address.town;
                        if (kabupaten) parts.push(kabupaten);
                        
                        // Province level
                        provinsi = data.address.state || data.address.province;
                        if (provinsi) parts.push(provinsi);
                        
                        // Postcode
                        if (data.address.postcode) parts.push(data.address.postcode);
                        
                        // Country
                        if (data.address.country) parts.push(data.address.country);
                        
                        if (parts.length > 0) {
                            address = parts.join(', ');
                        }
                    }
                    
                    // PER-FIELD FALLBACK: run extractFromDisplayName for each missing field individually
                    // This prevents "kecamatan found but kabupaten missing" scenarios
                    const fallback = this.extractFromDisplayName(data.display_name);
                    
                    // Fallback for kecamatan if OSM didn't provide one
                    if (!kecamatan && fallback.kecamatan) {
                        kecamatan = fallback.kecamatan;
                    }
                    
                    // Fallback for kabupaten if OSM didn't provide one
                    // IMPORTANT: Don't auto-discard kabupaten just because it equals kecamatan.
                    // Valid Indonesian cases: Kecamatan Serang in Kota Serang, Kecamatan Cirebon in Kota Cirebon, etc.
                    // Only use fallback if kabupaten is genuinely missing (null/empty).
                    if (!kabupaten) {
                        if (fallback.kabupaten) {
                            kabupaten = fallback.kabupaten;
                        } else {
                            kabupaten = '';
                        }
                    }
                    
                    // Fallback for provinsi if OSM didn't provide one
                    if (!provinsi && fallback.provinsi) {
                        provinsi = fallback.provinsi;
                    }
                    
                    // NO auto-clear of kabupaten just because it equals kecamatan.
                    // Valid cases exist where kecamatan and kota share the same name.
                    // The extractFromDisplayName already handles validation via explicit labels.
                    
                    // BANTEN RESTRICTION: Validate that the detected location is in Banten
                    if (this.restrictToBanten && provinsi && provinsi !== 'Banten') {
                        this.showToast('Lokasi terdeteksi di luar Banten: ' + provinsi + '. Pilih lokasi di Banten.', 'error');
                        // Clear wilayah fields
                        this.form.kecamatan = '';
                        this.form.kabupaten_kota = '';
                        this.form.provinsi = '';
                        this.syncToElement(this.kecamatanElementId, '');
                        this.syncToElement(this.kabupatenElementId, '');
                        this.syncToElement(this.provinsiElementId, '');
                        this.geocoding = false;
                        return;
                    }
                    
                    // Always sync individual fields (set if exists, clear if null)
                    this.form.kecamatan = kecamatan || '';
                    this.syncToElement(this.kecamatanElementId, kecamatan || '');
                    
                    this.form.kabupaten_kota = kabupaten || '';
                    this.syncToElement(this.kabupatenElementId, kabupaten || '');
                    
                    this.form.provinsi = provinsi || '';
                    this.syncToElement(this.provinsiElementId, provinsi || '');
                    
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

        // Generic sync to any element
        syncToElement(elementId, value) {
            if (!elementId) return;
            const el = document.getElementById(elementId);
            if (el) {
                el.value = value;
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },

        // Fallback: extract kecamatan, kabupaten, provinsi from display_name using regex
        // Indonesian format varies, so parse by labels first, then positional fallback with strict validation
        extractFromDisplayName(displayName) {
            const result = { kecamatan: null, kabupaten: null, provinsi: null };
            if (!displayName) return result;
            
            // Known Indonesian provinces for validation
            const knownProvinces = [
                'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
                'Sumatera Selatan', 'Bangka Belitung', 'Bengkulu', 'Lampung',
                'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
                'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
                'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
                'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
                'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Tengah', 'Papua Pegunungan', 'Papua Selatan', 'Papua Barat Daya'
            ];
            
            // Helper to find first match from regex
            const findFirst = (regex, str) => {
                const m = str.match(regex);
                return m ? m[1].trim() : null;
            };
            
            // 1. Extract Provinsi - match "Provinsi X" or known province name near end
            let provinsi = findFirst(/(?:^|,)\s*Provinsi\s+([^,]+)/i, displayName);
            if (!provinsi) {
                // Try known province names (case insensitive, word boundary)
                for (const p of knownProvinces) {
                    const regex = new RegExp(`(?:^|,)\\s*${p.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&')}(?=,|$)`, 'i');
                    const m = displayName.match(regex);
                    if (m) { provinsi = p; break; }
                }
            }
            result.provinsi = provinsi;
            
            // 2. Extract Kabupaten/Kota - match explicit labels "Kabupaten X", "Kota X", "Kab. X"
            let kabupaten = findFirst(/(?:^|,)\s*(?:Kabupaten|Kota|Kab\.)\s+([^,]+)/i, displayName);
            result.kabupaten = kabupaten;
            
            // 3. Extract Kecamatan - match explicit labels "Kecamatan X", "Kec. X"
            let kecamatan = findFirst(/(?:^|,)\s*(?:Kecamatan|Kec\.)\s+([^,]+)/i, displayName);
            result.kecamatan = kecamatan;
            
            // 4. Positional fallback (LAST RESORT) - only if explicit labels not found
            // Split by comma and analyze from the end: Indonesia, Provinsi, Kabupaten, Kecamatan, Kelurahan, ...
            if (!result.kecamatan || !result.kabupaten || !result.provinsi) {
                const parts = displayName.split(',').map(p => p.trim()).filter(p => p.length > 0);
                if (parts.length >= 3) {
                    // Find province index (from end)
                    let provIdx = -1;
                    if (result.provinsi) {
                        provIdx = parts.findIndex(p => p.toLowerCase().includes(result.provinsi.toLowerCase()));
                    } else {
                        // Try to find known province from end
                        for (let i = parts.length - 1; i >= 0; i--) {
                            for (const p of knownProvinces) {
                                if (parts[i].toLowerCase() === p.toLowerCase() || parts[i].toLowerCase().includes(' ' + p.toLowerCase())) {
                                    provIdx = i;
                                    result.provinsi = p;
                                    break;
                                }
                            }
                            if (provIdx >= 0) break;
                        }
                    }
                    
                    // Kabupaten is typically the part before province
                    if (!result.kabupaten && provIdx > 0) {
                        const candidate = parts[provIdx - 1];
                        // Validate: not a kecamatan/kelurahan/jalan
                        if (!/^(Kecamatan|Kec\.|Kelurahan|Desa|Kel\.|Dusun|RW|RT|Jalan|Jl\.)/i.test(candidate)) {
                            result.kabupaten = candidate.replace(/^(Kabupaten|Kota|Kab\.)\s+/i, '');
                        }
                    }
                    
                    // Kecamatan is typically the part before kabupaten
                    if (!result.kecamatan && result.kabupaten) {
                        const kabIdx = parts.findIndex(p => p.toLowerCase().includes(result.kabupaten.toLowerCase()));
                        if (kabIdx > 0) {
                            const candidate = parts[kabIdx - 1];
                            // Validate: not a kelurahan/jalan
                            if (!/^(Kelurahan|Desa|Kel\.|Dusun|RW|RT|Jalan|Jl\.)/i.test(candidate)) {
                                result.kecamatan = candidate.replace(/^(Kecamatan|Kec\.)\s+/i, '');
                            }
                        }
                    }
                }
            }
            
            // Validation guards to prevent cross-contamination:
            
            // Guard 1: If kabupaten value contains kecamatan keywords, it's likely a false match
            if (result.kabupaten && /^(Kecamatan|Kec\.|Kelurahan|Desa|Kel\.|Dusun|RW|RT|Jalan|Jl\.)/i.test(result.kabupaten)) {
                result.kabupaten = null;
            }
            
            // Guard 2: If kecamatan value contains kabupaten/kota keywords, it's likely a false match
            if (result.kecamatan && /^(Kabupaten|Kota|Kab\.)/i.test(result.kecamatan)) {
                result.kecamatan = null;
            }
            
            // Guard 3: If kecamatan contains known province name as whole word, discard it
            if (result.kecamatan) {
                for (const p of knownProvinces) {
                    // Use word boundary to avoid false positives like "Tangerang" in "Kec. Tangerang"
                    const wordBoundaryRegex = new RegExp(`\\b${p.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&')}\\b`, 'i');
                    if (wordBoundaryRegex.test(result.kecamatan)) {
                        result.kecamatan = null;
                        break;
                    }
                }
            }
            
            // Guard 4: Handle same-name kecamatan/kabupaten (e.g., Kecamatan Serang in Kota Serang)
            // If BOTH have explicit labels in display_name and names match, KEEP BOTH - this is valid in Indonesia
            // Only discard if one or both came from positional fallback (no explicit label)
            if (result.kabupaten && result.kecamatan && 
                result.kabupaten.toLowerCase() === result.kecamatan.toLowerCase()) {
                
                // Check if both have explicit labels in display_name
                const hasExplicitKabupaten = /(?:^|,)\s*(?:Kabupaten|Kota|Kab\.)\s+([^,]+)/i.test(displayName);
                const hasExplicitKecamatan = /(?:^|,)\s*(?:Kecamatan|Kec\.)\s+([^,]+)/i.test(displayName);
                
                if (hasExplicitKabupaten && hasExplicitKecamatan) {
                    // Both have explicit labels - valid Indonesian case (e.g., "Kecamatan Serang, Kota Serang")
                    // Keep both, do nothing
                } else if (hasExplicitKabupaten && !hasExplicitKecamatan) {
                    // Only kabupaten has explicit label - kecamatan likely from positional fallback, discard kecamatan
                    result.kecamatan = null;
                } else if (!hasExplicitKabupaten && hasExplicitKecamatan) {
                    // Only kecamatan has explicit label - kabupaten likely from positional fallback, discard kabupaten
                    result.kabupaten = null;
                } else {
                    // Neither has explicit label - both from positional fallback, ambiguous
                    // Keep kecamatan (more granular), discard kabupaten
                    result.kabupaten = null;
                }
            }
            
            // Guard 5: If kabupaten contains province name, discard it
            if (result.kabupaten && result.provinsi && 
                result.kabupaten.toLowerCase().includes(result.provinsi.toLowerCase())) {
                result.kabupaten = null;
            }
            
            return result;
        },

        clearAddress() {
            this.form.address = '';
            this.form.kecamatan = '';
            this.form.kabupaten_kota = '';
            this.form.provinsi = '';
            this.syncAddressToElement('');
            this.syncToElement(this.kecamatanElementId, '');
            this.syncToElement(this.kabupatenElementId, '');
            this.syncToElement(this.provinsiElementId, '');
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
                    
                    // Validate Banten bounds for current location
                    if (this.restrictToBanten && !this.isWithinBanten(lat, lng)) {
                        this.locating = false;
                        this.showToast('Lokasi Anda di luar wilayah Banten. Pilih lokasi manual di peta.', 'error');
                        return;
                    }
                    
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
            // Use Banten center if restricted to Banten
            const resetLat = this.restrictToBanten ? this.bantenCenter[0] : this.defaultLat;
            const resetLng = this.restrictToBanten ? this.bantenCenter[1] : this.defaultLng;
            
            this.form.lat = resetLat;
            this.form.lng = resetLng;
            this.form.radius = this.defaultRadius;
            this.form.address = '';
            this.form.kecamatan = '';
            this.form.kabupaten_kota = '';
            this.form.provinsi = '';
            this.syncAddressToElement('');
            this.syncToElement(this.kecamatanElementId, '');
            this.syncToElement(this.kabupatenElementId, '');
            this.syncToElement(this.provinsiElementId, '');
            this.updateMapPosition(resetLat, resetLng);
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