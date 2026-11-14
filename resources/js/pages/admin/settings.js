// Halaman pengaturan sistem: form + peta geofence kantor
document.addEventListener('alpine:init', () => {
    Alpine.data('settingsForm', (branding = {}, map = {}, general = {}) => ({
        form: {
            app_name_desktop: branding.app_name_desktop ?? '',
            app_name_web: branding.app_name_web ?? '',
            app_tagline: branding.app_tagline ?? '',
            office_lat: map.office_lat ?? '',
            office_lng: map.office_lng ?? '',
            office_address: map.office_address ?? '',
            timezone: general.timezone ?? 'Asia/Jakarta',
        },
        saving: false,
        toast: { show: false, message: '' },
        map: null,
        marker: null,
        defaultLat: -6.2088,
        defaultLng: 106.8456,

        init() {
            this.initMap();
        },

        async reverseGeocode(lat, lng) {
            try {
                // Nominatim mewajibkan User-Agent
                const resp = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`,
                    { headers: { 'User-Agent': 'Genta-Admin-Panel/1.0' } },
                );
                const data = await resp.json();
                if (data.display_name) {
                    this.form.office_address = data.display_name;
                } else if (data.address) {
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
                this.form.office_address = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)} (Geocoding gagal)`;
            }
        },

        initMap() {
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

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(this.map);

            const icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="width: 28px; height: 28px; background: #E50000; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><div style="width: 12px; height: 12px; background: white; border-radius: 50%; margin: 5px auto 0; transform: rotate(45deg);"></div></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -28],
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
                { enableHighAccuracy: true, timeout: 10000 },
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
                        Accept: 'application/json',
                    },
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
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
            setTimeout(() => (this.toast.show = false), 3000);
        },
    }));
});
