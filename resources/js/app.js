import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;
window.L = L;

// Peta publik read-only (komponen x-google-map) — dipakai halaman landing
Alpine.data('publicMap', (config) => ({
    init() {
        const el = document.getElementById(config.id);
        if (!el || el._leaflet_id) {
            return;
        }

        this.map = L.map(config.id, {
            zoomControl: config.interactive,
            scrollWheelZoom: config.interactive,
            dragging: config.interactive,
            doubleClickZoom: config.interactive,
            boxZoom: config.interactive,
            keyboard: config.interactive,
            tap: config.interactive,
            touchZoom: config.interactive,
        }).setView([config.lat, config.lng], config.zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(this.map);

        if (!config.showMarker) {
            return;
        }

        const icon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="width: 28px; height: 28px; background: #E50000; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><div style="width: 12px; height: 12px; background: white; border-radius: 50%; margin: 5px auto 0; transform: rotate(45deg);"></div></div>',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28],
        });

        const marker = L.marker([config.lat, config.lng], { icon }).addTo(this.map);

        if (config.address) {
            marker.bindPopup('<div style="padding: 4px 8px; min-width: 180px;"><strong>' + config.title + '</strong><br><small>' + config.address + '</small></div>');
        }
    },
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
        const cards = list.querySelectorAll('[data-kitchen="card"]');
        const q = this.query.toLowerCase();
        cards.forEach(card => {
            const name = card.querySelector('[data-kitchen="name"]')?.textContent.toLowerCase() || '';
            const meta = card.querySelector('[data-kitchen="meta"]')?.textContent.toLowerCase() || '';
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