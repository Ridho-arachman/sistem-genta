// Peta publik read-only (komponen x-google-map) — dipakai halaman landing
document.addEventListener('alpine:init', () => {
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
});
