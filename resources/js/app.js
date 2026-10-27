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

Alpine.start();