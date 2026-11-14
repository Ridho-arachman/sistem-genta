// Halaman form inspeksi pengawas: foto + watermark peta/GPS + validasi kirim

// Format tanggal dan jam untuk watermark foto
function getDateTimeString() {
    const now = new Date();
    const tanggal = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    return tanggal + ' ' + jam;
}

// Convert lat/lng to OSM tile x,y at given zoom
function latLngToTile(lat, lng, zoom) {
    const n = Math.pow(2, zoom);
    const xFloat = ((lng + 180) / 360) * n;
    const yFloat =
        ((1 - Math.log(Math.tan((lat * Math.PI) / 180) + 1 / Math.cos((lat * Math.PI) / 180)) / Math.PI) / 2) * n;
    const x = Math.floor(xFloat);
    const y = Math.floor(yFloat);
    return { x, y, z: zoom, fracX: xFloat - x, fracY: yFloat - y };
}

// Create combined image: photo + full-width map tile (bottom) + GPS text on map
function createCombinedImage(photoDataUrl, lat, lng, address) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');

            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            if (lat && lng) {
                const tile = latLngToTile(parseFloat(lat), parseFloat(lng), 16);
                const mapImg = new Image();
                mapImg.crossOrigin = 'anonymous';
                mapImg.src = `https://tile.openstreetmap.org/${tile.z}/${tile.x}/${tile.y}.png`;

                mapImg.onload = () => {
                    drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, tile.fracX, tile.fracY);
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                };
                mapImg.onerror = () => {
                    drawGPSOnly(ctx, canvas, lat, lng, address);
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                };
            } else {
                resolve(canvas.toDataURL('image/jpeg', 0.85));
            }
        };
        img.src = photoDataUrl;
    });
}

function drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, fracX = 0.5, fracY = 0.5) {
    const marginLeft = 3;
    const marginRight = 30;
    const gap = 60;
    const mapH = Math.min(900, canvas.height * 0.66);
    const availableW = canvas.width - marginLeft - marginRight - gap;
    const mapW = Math.min(1200, availableW * 0.4);
    const textW = availableW - mapW;

    const mapX = marginLeft;
    const mapY = canvas.height - mapH - marginLeft;
    const textX = mapX + mapW + gap;

    ctx.drawImage(mapImg, mapX, mapY, mapW, mapH);

    const pinX = mapX + fracX * mapW;
    const pinY = mapY + fracY * mapH;
    const pinSize = 40;
    const pinTipY = pinSize * 1.2;

    ctx.save();
    ctx.translate(pinX, pinY - pinTipY);

    ctx.fillStyle = '#E50000';
    ctx.beginPath();
    ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
    ctx.bezierCurveTo(pinSize * 0.55, -pinSize * 0.3, pinSize * 0.55, pinSize * 0.3, 0, pinSize * 1.2);
    ctx.bezierCurveTo(-pinSize * 0.55, pinSize * 0.3, -pinSize * 0.55, -pinSize * 0.3, -pinSize * 0.55, -pinSize * 0.3);
    ctx.closePath();
    ctx.fill();

    ctx.fillStyle = '#AA0000';
    ctx.beginPath();
    ctx.arc(0, -pinSize * 0.3, pinSize * 0.45, Math.PI, 0);
    ctx.bezierCurveTo(pinSize * 0.45, -pinSize * 0.3, pinSize * 0.45, pinSize * 0.15, 0, pinSize * 0.9);
    ctx.bezierCurveTo(-pinSize * 0.45, pinSize * 0.15, -pinSize * 0.45, -pinSize * 0.3, -pinSize * 0.45, -pinSize * 0.3);
    ctx.fill();

    ctx.fillStyle = '#FFFFFF';
    ctx.beginPath();
    ctx.arc(0, -pinSize * 0.3, pinSize * 0.22, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = '#FFFFFF';
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
    ctx.bezierCurveTo(pinSize * 0.55, -pinSize * 0.3, pinSize * 0.55, pinSize * 0.3, 0, pinSize * 1.2);
    ctx.bezierCurveTo(-pinSize * 0.55, pinSize * 0.3, -pinSize * 0.55, -pinSize * 0.3, -pinSize * 0.55, -pinSize * 0.3);
    ctx.stroke();

    ctx.restore();

    const textY = mapY;
    const textH = mapH;

    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
    ctx.fillRect(textX - 18, textY, textW + 30, textH);

    ctx.fillStyle = '#FFFFFF';
    ctx.font = 'bold 84px system-ui, sans-serif';
    ctx.fillText(lat + ', ' + lng, textX, textY + 120);

    ctx.fillStyle = '#CCCCCC';
    ctx.font = '64px system-ui, sans-serif';
    ctx.fillText('GPS: ' + lat + ', ' + lng, textX, textY + 220);

    if (address) {
        drawAddressLines(ctx, address, textX, textY, textW + 30);
    }
}

function drawGPSOnly(ctx, canvas, lat, lng, address) {
    const marginLeft = 3;
    const marginRight = 30;
    const gap = 60;
    const mapH = Math.min(900, canvas.height * 0.66);
    const availableW = canvas.width - marginLeft - marginRight - gap;
    const mapW = Math.min(1200, availableW * 0.4);
    const textW = availableW - mapW;

    const mapX = marginLeft;
    const mapY = canvas.height - mapH - marginLeft;
    const textX = mapX + mapW + gap;

    const textY = mapY;
    const textH = mapH;

    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
    ctx.fillRect(textX - 18, textY, textW + 30, textH);

    ctx.fillStyle = '#FFFFFF';
    ctx.font = 'bold 84px system-ui, sans-serif';
    ctx.fillText(lat + ', ' + lng, textX, textY + 120);

    ctx.fillStyle = '#CCCCCC';
    ctx.font = '64px system-ui, sans-serif';
    ctx.fillText('Peta tidak tersedia', textX, textY + 220);

    if (address) {
        drawAddressLines(ctx, address, textX, textY, textW + 30);
    }
}

// Alamat dipecah per baris + tanggal/jam di bawahnya (dipakai kedua varian watermark)
function drawAddressLines(ctx, address, textX, textY, maxWidth) {
    const words = address.split(' ');
    const lines = [];
    let currentLine = 'Alamat: ';

    for (const word of words) {
        const testLine = currentLine + word + ' ';
        if (ctx.measureText(testLine).width > maxWidth && currentLine !== 'Alamat: ') {
            lines.push(currentLine.trim());
            currentLine = word + ' ';
        } else {
            currentLine = testLine;
        }
    }
    if (currentLine.trim()) {
        lines.push(currentLine.trim());
    }

    ctx.fillStyle = '#FFFFFF';
    ctx.font = '68px system-ui, sans-serif';
    lines.forEach((line, i) => ctx.fillText(line, textX, textY + 320 + i * 75));

    ctx.font = '72px system-ui, sans-serif';
    ctx.fillText(getDateTimeString(), textX, textY + 320 + lines.length * 75 + 75);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('inspeksiForm', () => ({
        photo: null, // {id, dataUrl, lat, lng, accuracy, address, gpsStatus}
        loading: false,
        submitting: false,
        previewOpen: false,
        indikator: [
            'Kebersihan dan sanitasi',
            'Penyimpanan bahan',
            'Proses pengolahan',
            'Distribusi makanan',
            'Peralatan dan fasilitas',
        ],
        errors: { dapur: '', hasil: ['', '', '', '', ''], foto: '', gps: '' },

        get previewGps() {
            if (!this.photo?.lat || !this.photo?.lng) {
                return 'GPS tidak tersedia';
            }
            const accuracy = this.photo.accuracy ? `  (±${Math.round(this.photo.accuracy)}m)` : '';
            return `📍 ${this.photo.lat}, ${this.photo.lng}${accuracy}`;
        },

        // Capacitor dulu (aplikasi), fallback ke geolocation browser
        getPosition(timeout = 15000) {
            if (window.Capacitor?.Plugins?.Geolocation) {
                return window.Capacitor.Plugins.Geolocation.getCurrentPosition({ enableHighAccuracy: false, timeout });
            }
            if (!navigator.geolocation) {
                return Promise.reject(new Error('Geolocation not supported'));
            }
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: false,
                    timeout,
                    maximumAge: 60000,
                });
            });
        },

        // Klik kartu foto: cek GPS dulu, lalu buka kamera / preview
        async openCamera() {
            this.errors.foto = '';
            this.errors.gps = '';

            try {
                await this.getPosition(5000);
            } catch (err) {
                let msg = 'GPS tidak aktif. ';
                if (err.code === 1) msg += 'Izin lokasi ditolak. ';
                else if (err.code === 2) msg += 'Lokasi tidak tersedia. ';
                else if (err.code === 3) msg += 'Timeout. ';
                this.errors.gps = msg + 'Aktifkan GPS sebelum ambil foto.';
                return;
            }

            if (this.photo?.dataUrl) {
                this.openPreview();
            } else {
                this.$refs.photoInput.click();
            }
        },

        async handlePhotoSelected(event) {
            const input = event.target;
            const file = input.files?.[0];
            if (!file) {
                return;
            }
            if (!file.type.startsWith('image/')) {
                this.errors.foto = 'Bukan file gambar';
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                this.errors.foto = 'Foto terlalu besar (maks 5MB)';
                input.value = '';
                return;
            }

            this.errors.foto = '';
            this.loading = true;

            try {
                const dataUrl = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(file);
                });

                const photoId = 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                this.photo = {
                    id: photoId,
                    dataUrl,
                    lat: '',
                    lng: '',
                    accuracy: '',
                    address: '',
                    gpsStatus: 'loading',
                };
                input.value = '';

                // GPS + watermark diproses di background
                this.attachLocation(photoId, dataUrl);
            } catch (err) {
                console.error('Photo processing error:', err);
                this.errors.foto = 'Gagal memproses foto';
            } finally {
                this.loading = false;
            }
        },

        async attachLocation(photoId, originalDataUrl) {
            if (!this.photo || this.photo.id !== photoId) {
                return;
            }

            try {
                const position = await this.getPosition();
                const accuracy = position.coords.accuracy;
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);

                let address = '';
                try {
                    const resp = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`,
                    );
                    const data = await resp.json();
                    address = data.display_name || '';
                } catch (err) {
                    console.warn('Reverse geocode error:', err);
                }

                this.photo.dataUrl = await createCombinedImage(originalDataUrl, lat, lng, address);
                this.photo.lat = lat;
                this.photo.lng = lng;
                this.photo.accuracy = accuracy;
                this.photo.address = address;
                this.photo.gpsStatus = 'success';
            } catch (err) {
                let msg = 'GPS tidak tersedia';
                if (err.code === 1 || err.message?.includes('denied')) {
                    msg += ' (izin lokasi ditolak)';
                } else if (err.code === 2 || err.message?.includes('unavailable')) {
                    msg += ' (lokasi tidak tersedia)';
                } else if (err.code === 3 || err.message?.includes('timeout')) {
                    msg += ' (timeout)';
                }
                console.warn('GPS error:', msg, err);

                // Tanpa GPS tetap buat gambar gabungan (foto saja)
                this.photo.dataUrl = await createCombinedImage(originalDataUrl, '', '', '');
                this.photo.lat = '';
                this.photo.lng = '';
                this.photo.accuracy = '';
                this.photo.address = '';
                this.photo.gpsStatus = 'error';
            }
        },

        removePhoto() {
            this.photo = null;
            this.$refs.photoInput.value = '';
        },

        openPreview() {
            if (!this.photo?.dataUrl) {
                return;
            }
            this.previewOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closePreview() {
            this.previewOpen = false;
            document.body.style.overflow = '';
        },

        submit() {
            const form = this.$refs.form;
            const dapur = form.querySelector('select[name="dapur_id"]');

            this.errors = { dapur: '', hasil: ['', '', '', '', ''], foto: '', gps: '' };

            if (!dapur.value) {
                this.errors.dapur = 'Pilih dapur dulu';
                dapur.focus();
                return;
            }

            const hasilSelects = [...form.querySelectorAll('select[name="hasil[]"]')];
            let hasilError = false;
            for (let i = 0; i < 5; i++) {
                if (!hasilSelects[i]?.value) {
                    this.errors.hasil[i] = 'Pilih hasil: ' + this.indikator[i];
                    hasilSelects[i]?.focus();
                    hasilError = true;
                }
            }
            if (hasilError) {
                return;
            }

            if (!this.photo?.dataUrl) {
                this.errors.foto = 'Foto dapur belum diisi. Klik area foto untuk mengambil gambar.';
                return;
            }

            if (this.photo.gpsStatus === 'loading') {
                this.errors.gps = 'Tunggu foto selesai diproses (GPS masih loading).';
                return;
            }

            if (!this.photo.lat || !this.photo.lng) {
                this.errors.gps =
                    'GPS gagal didapatkan. Lokasi wajib terisi sebelum kirim laporan. Coba ambil foto ulang.';
                return;
            }

            const dapurText = dapur.options[dapur.selectedIndex].text;
            const confirmed = window.confirm(
                'Kirim laporan sekarang?\n\nData yang akan dikirim:\n- Dapur: ' +
                    dapurText +
                    '\n- Foto: tersedia\n- GPS: ' +
                    this.photo.lat +
                    ', ' +
                    this.photo.lng,
            );
            if (!confirmed) {
                return;
            }

            this.submitting = true;
            form.submit();
        },
    }));
});
