import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;
window.L = L;

// Office Map Component
Alpine.data('officeMap', () => ({
    map: null,
    marker: null,
    DEFAULT_LAT: -6.2088,
    DEFAULT_LNG: 106.8456,
    currentLat: -6.2088,
    currentLng: 106.8456,

    init() {
        const mapEl = document.getElementById('geofenceMap');
        if (!mapEl) return;

        // Initialize map
        this.map = L.map('geofenceMap').setView([this.DEFAULT_LAT, this.DEFAULT_LNG], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(this.map);

        // Custom marker icon (red pin)
        const officeIcon = L.divIcon({
            className: 'office-marker',
            html: '<div style="width: 28px; height: 28px; background: #E50000; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });

        // Add marker
        this.marker = L.marker([this.DEFAULT_LAT, this.DEFAULT_LNG], { icon: officeIcon, draggable: true }).addTo(this.map);
        this.marker.bindPopup('<b>Lokasi Kantor</b><br>Seret untuk memindahkan').openPopup();

        // Marker drag event
        this.marker.on('dragend', (e) => {
            const pos = e.target.getLatLng();
            this.currentLat = pos.lat;
            this.currentLng = pos.lng;
            this.updateValues(this.currentLat, this.currentLng);
            this.map.panTo([this.currentLat, this.currentLng]);
        });

        // Map click to set center
        this.map.on('click', (e) => {
            this.currentLat = e.latlng.lat;
            this.currentLng = e.latlng.lng;

            this.marker.setLatLng([this.currentLat, this.currentLng]);
            this.updateValues(this.currentLat, this.currentLng);
            this.marker.openPopup();
        });

        // Locate me button
        document.getElementById('btnLocateMe').addEventListener('click', () => {
            if (!navigator.geolocation) {
                window.showToast('Geolocation tidak didukung browser ini');
                return;
            }
            const btn = document.getElementById('btnLocateMe');
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Mencari lokasi...</span>';

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.currentLat = pos.coords.latitude;
                    this.currentLng = pos.coords.longitude;

                    this.map.setView([this.currentLat, this.currentLng], 16);
                    this.marker.setLatLng([this.currentLat, this.currentLng]);

                    this.updateValues(this.currentLat, this.currentLng);
                    this.marker.openPopup();

                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>Gunakan Lokasi Saya</span>';
                },
                (err) => {
                    window.showToast('Gagal mendapatkan lokasi: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>Gunakan Lokasi Saya</span>';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        // Reset button
        document.getElementById('btnResetMap').addEventListener('click', () => {
            this.currentLat = this.DEFAULT_LAT;
            this.currentLng = this.DEFAULT_LNG;

            this.map.setView([this.DEFAULT_LAT, this.DEFAULT_LNG], 15);
            this.marker.setLatLng([this.DEFAULT_LAT, this.DEFAULT_LNG]);

            this.updateValues(this.DEFAULT_LAT, this.DEFAULT_LNG);
        });

        // Fix map render on tab show
        setTimeout(() => this.map.invalidateSize(), 100);
    },

    updateValues(lat, lng) {
        this.currentLat = lat;
        this.currentLng = lng;

        document.getElementById('office_lat').value = this.currentLat.toFixed(6);
        document.getElementById('office_lng').value = this.currentLng.toFixed(6);

        document.getElementById('displayLat').textContent = this.currentLat.toFixed(6);
        document.getElementById('displayLng').textContent = this.currentLng.toFixed(6);
    }
}));

// Kitchen search filter (komponen x-kitchen-filter)
Alpine.data('kitchenFilter', () => ({
    query: '',
    listId: '',
    searchId: '',

    init(listId, searchId) {
        this.listId = listId;
        this.searchId = searchId;
    },

    filter() {
        const list = document.getElementById(this.listId);
        if (!list) return;
        const cards = list.querySelectorAll('.kitchen-card');
        const q = this.query.toLowerCase();
        cards.forEach(card => {
            const name = card.querySelector('.kitchen-name')?.textContent.toLowerCase() || '';
            const meta = card.querySelector('.kitchen-meta')?.textContent.toLowerCase() || '';
            card.style.display = (name.includes(q) || meta.includes(q)) ? 'flex' : 'none';
        });
    },
}));

// Shared: autocomplete dapur (kepala dapur) — dipakai users/create & users/edit
Alpine.data('dapurAutocomplete', () => ({
    searchQuery: '',
    showDropdown: false,
    highlightedIndex: -1,

    init() {
        const current = this.$root.kepalaDapurId;
        if (current && !this.searchQuery) {
            const d = (this.$root.dapurs || []).find(x => x.id == current);
            if (d) this.searchQuery = d.nama;
        }
    },

    get filteredDapurs() {
        if (!this.searchQuery) return this.$root.dapurs || [];
        const q = this.searchQuery.toLowerCase();
        return (this.$root.dapurs || []).filter(d =>
            d.nama.toLowerCase().includes(q) ||
            (d.lokasi || '').toLowerCase().includes(q)
        );
    },

    selectDapur(dapur) {
        this.$root.kepalaDapurId = dapur.id;
        this.searchQuery = dapur.nama;
        this.showDropdown = false;
        this.highlightedIndex = -1;
    },

    focusFirstOption() {
        if (this.filteredDapurs.length > 0) this.highlightedIndex = 0;
    },

    focusLastOption() {
        if (this.filteredDapurs.length > 0) this.highlightedIndex = this.filteredDapurs.length - 1;
    },

    selectHighlighted() {
        if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredDapurs.length) {
            this.selectDapur(this.filteredDapurs[this.highlightedIndex]);
        }
    }
}));

// Shared: multi-select dapur (pengawas) — dipakai users/create & users/edit
Alpine.data('dapurMultiSelect', () => ({
    searchQuery: '',

    get filteredDapurs() {
        if (!this.searchQuery) return this.$root.dapurs || [];
        const q = this.searchQuery.toLowerCase();
        return (this.$root.dapurs || []).filter(d =>
            d.nama.toLowerCase().includes(q) ||
            (d.lokasi || '').toLowerCase().includes(q)
        );
    },

    toggleDapur(id) {
        this.$root.toggleDapur(id);
    },

    removeDapur(id) {
        this.$root.removeDapur(id);
    },

    getDapurName(id) {
        return this.$root.getDapurName(id);
    }
}));

// Cascade kabupaten/kota → kecamatan (dipakai dapurs/create & dapurs/edit)
Alpine.data('dapurForm', (allKecamatan, kabupaten, kecamatan) => ({
    form: {
        kabupaten_kota: kabupaten || '',
        kecamatan: kecamatan || '',
    },
    kecamatanOptions: [],
    allKecamatan: allKecamatan || {},

    onKabupatenChange(kab) {
        this.form.kabupaten_kota = kab;
        this.form.kecamatan = '';
        this.kecamatanOptions = kab ? (this.allKecamatan[kab] || []) : [];
    },

    getKecamatanOptionsHtml() {
        if (this.kecamatanOptions.length === 0) {
            return '<option value="">-- Pilih Kabupaten/Kota dulu --</option>';
        }
        return '<option value="">-- Pilih Kecamatan --</option>' +
            this.kecamatanOptions.map(kec =>
                `<option value="${kec}">${kec}</option>`
            ).join('');
    },

    init() {
        if (this.form.kabupaten_kota) {
            this.kecamatanOptions = this.allKecamatan[this.form.kabupaten_kota] || [];
        }
    }
}));

// HLS video player (hls.js) — dipakai admin/cctvs/index & show
Alpine.data('hlsPlayer', (src, autoplay = false) => ({
    error: false,

    init() {
        const video = this.$el.querySelector('video') || this.$el;
        const showError = () => {
            this.error = true;
            video.classList.add('hidden');
        };

        if (typeof Hls === 'undefined') {
            showError();
            return;
        }

        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(src);
            hls.attachMedia(video);

            if (autoplay) {
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    video.play().catch(() => {});
                });
            }

            hls.on(Hls.Events.ERROR, (event, data) => {
                if (!data.fatal) return;
                if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                    hls.startLoad();
                } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                    hls.recoverMediaError();
                } else {
                    hls.destroy();
                    showError();
                }
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = src;
            video.onerror = showError;
            if (autoplay) {
                video.addEventListener('loadedmetadata', () => {
                    video.play().catch(() => {});
                });
            }
        } else {
            showError();
        }
    }
}));

// Global UI state (dipakai lintas section, mis. hamburger ↔ sidebar)
Alpine.store('ui', {
    sidebarOpen: false,
    toggle() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    close() {
        this.sidebarOpen = false;
    },
});

Alpine.start();