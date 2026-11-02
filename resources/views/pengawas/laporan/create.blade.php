@extends('pengawas.layouts.app', ['pageTitle' => 'Buat Laporan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Buat Laporan Inspeksi</h1>
        <div class="muted">Isi formulir pemeriksaan dapur (maksimal 6 foto, setiap foto dilengkapi peta + GPS)</div>
    </div>
    <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">← Kembali</a>
</div>

<div class="panel" style="max-width: 720px;">
    <form method="POST" action="{{ route('pengawas.laporan.store') }}" id="laporanForm" class="space-y-6">
        @csrf

        {{-- Pilih Dapur --}}
        <div>
            <label for="dapur_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Pilih Dapur</label>
            <select id="dapur_id" name="dapur_id" class="input-field">
                <option value="{{ auth()->user()->dapur->id ?? '' }}">{{ auth()->user()->dapur->nama ?? 'Dapur Anda' }}</option>
            </select>
        </div>

        {{-- Kategori & Hasil Pemeriksaan per Indikator --}}
        <div class="space-y-4">
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-2">Hasil Pemeriksaan per Kategori</label>

            {{-- Kebersihan dan sanitasi --}}
            <div class="card p-4 bg-[var(--color-surface)]/50">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[var(--color-secondary)]">Kebersihan dan sanitasi</span>
                    <input type="hidden" name="kategori[]" value="Kebersihan dan sanitasi">
                    <select name="hasil[]" class="input-field w-48">
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Penyimpanan bahan --}}
            <div class="card p-4 bg-[var(--color-surface)]/50">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[var(--color-secondary)]">Penyimpanan bahan</span>
                    <input type="hidden" name="kategori[]" value="Penyimpanan bahan">
                    <select name="hasil[]" class="input-field w-48">
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Proses pengolahan --}}
            <div class="card p-4 bg-[var(--color-surface)]/50">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[var(--color-secondary)]">Proses pengolahan</span>
                    <input type="hidden" name="kategori[]" value="Proses pengolahan">
                    <select name="hasil[]" class="input-field w-48">
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Distribusi makanan --}}
            <div class="card p-4 bg-[var(--color-surface)]/50">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[var(--color-secondary)]">Distribusi makanan</span>
                    <input type="hidden" name="kategori[]" value="Distribusi makanan">
                    <select name="hasil[]" class="input-field w-48">
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Peralatan dan fasilitas --}}
            <div class="card p-4 bg-[var(--color-surface)]/50">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-[var(--color-secondary)]">Peralatan dan fasilitas</span>
                    <input type="hidden" name="kategori[]" value="Peralatan dan fasilitas">
                    <select name="hasil[]" class="input-field w-48">
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Foto Selfie di Lokasi (Multiple) --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-[var(--color-secondary)] mb-0">Foto Selfie di Lokasi</label>
                <span class="text-xs text-[var(--color-text-muted)]">Maksimal 6 foto · Setiap foto otomatis dilengkapi peta + GPS</span>
            </div>

            {{-- Native camera input --}}
            <label for="selfie_photo" class="btn-action btn-action-outline w-full cursor-pointer flex items-center justify-center gap-2 mb-4" aria-label="Ambil foto selfie">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Ambil Foto Selfi (dengan GPS & Peta)</span>
            </label>
            <input type="file" id="selfie_photo" name="selfie_photo[]" accept="image/*" capture="environment" multiple class="hidden" onchange="handlePhotosSelected(this)">

            {{-- Photo Gallery Grid --}}
            <div id="photoGallery" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                <!-- Photo cards inserted here by JS -->
            </div>
        </div>

        {{-- Hidden inputs for photo data (base64) and GPS arrays --}}
        <input type="hidden" name="selfie_photo[]" id="photoDataInput" value="">
        <input type="hidden" name="latitude[]" id="latitudeInput" value="">
        <input type="hidden" name="longitude[]" id="longitudeInput" value="">
        <input type="hidden" name="address[]" id="addressInput" value="">

        {{-- Catatan Pengawas --}}
        <div>
            <label for="catatan" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Catatan Pengawas</label>
            <textarea id="catatan" name="catatan" rows="4" class="input-field" placeholder="Tuliskan kondisi yang ditemukan…"></textarea>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 justify-end pt-4 border-t border-[var(--color-border)]">
            <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" id="submitBtn">Kirim Laporan</button>
        </div>
    </form>
</div>

<!-- Photo Preview Modal -->
<div id="photoPreviewModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[1000] hidden">
    <div class="relative max-w-4xl max-h-[90vh] w-full mx-4">
        <button type="button" onclick="closePhotoPreview()" class="absolute -top-12 right-0 bg-[var(--color-surface)] text-[var(--color-text)] p-2 rounded-full cursor-pointer shadow-lg z-10" aria-label="Tutup preview">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="bg-[var(--color-background)] rounded-lg overflow-hidden w-full max-h-[70vh]">
            <img id="previewImage" src="" alt="Preview foto" class="w-full max-h-[70vh] object-contain">
            <div id="previewInfo" class="p-4 border-t border-[var(--color-border)]">
                <p id="previewGps" class="text-sm text-[var(--color-text-muted)]"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');
    const photoInput = document.getElementById('selfie_photo');
    const photoGallery = document.getElementById('photoGallery');
    const photoDataInput = document.getElementById('photoDataInput');
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');

    const MAX_PHOTOS = 6;
    let photos = []; // Array of {id, dataUrl, lat, lng, accuracy, address}

    // Convert lat/lng to OSM tile x,y at given zoom
    function latLngToTile(lat, lng, zoom) {
        const n = Math.pow(2, zoom);
        const xFloat = (lng + 180) / 360 * n;
        const yFloat = (1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * n;
        const x = Math.floor(xFloat);
        const y = Math.floor(yFloat);
        return { x, y, z: zoom, fracX: xFloat - x, fracY: yFloat - y };
    }

    // Update hidden inputs from photos array
    function updateHiddenInputs() {
        photoDataInput.value = photos.map(p => p.dataUrl).join('|');
        latInput.value = photos.map(p => p.lat || '').join(',');
        lngInput.value = photos.map(p => p.lng || '').join(',');
        addressInput.value = photos.map(p => (p.address || '').replace(/\|/g, ' ')).join('|');
    }

    // Render photo gallery - setiap foto sudah berisi peta + GPS di dalamnya (combined)
    function renderPhotoGallery() {
        photoGallery.innerHTML = '';

        photos.forEach((photo, index) => {
            const card = document.createElement('div');
            card.id = 'photo-card-' + photo.id;
            card.className = 'photo-card relative group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2 transition-all cursor-pointer';
            card.onclick = () => openPhotoPreview(photo.id);

            card.innerHTML = `
                <div class="relative group">
                    <img src="${photo.dataUrl}" alt="Foto ${index + 1}" class="w-full h-48 object-cover rounded-md">
                </div>

                <div class="flex items-center justify-between text-xs mt-1 pt-1 border-t border-[var(--color-border)]">
                    <span class="text-[var(--color-text-muted)] truncate">Foto ${index + 1}</span>
                    <button type="button" onclick="event.stopPropagation(); removePhoto('${photo.id}')" class="text-red-600 hover:text-red-800" aria-label="Hapus foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
                ${photo.lat && photo.lng ? `
                <div class="mt-2 pt-1 border-t border-[var(--color-border)]">
                    <span class="text-[10px] font-medium text-[var(--color-primary)]">📍 ${photo.lat}, ${photo.lng}</span>
                    <p class="text-[10px] text-[var(--color-text-muted)]">${photo.accuracy ? '±' + Math.round(photo.accuracy) + 'm' : 'GPS tersimpan'}</p>
                    ${photo.address ? `<p class="text-[9px] text-[var(--color-text-muted)] mt-1 truncate max-w-full">${photo.address}</p>` : ''}
                </div>
                ` : `
                <div class="mt-2 pt-1 border-t border-[var(--color-border)]">
                    <p class="text-[10px] text-[var(--color-text-muted)]">Memproses GPS...</p>
                </div>
                `}
            `;

            photoGallery.appendChild(card);
        });
    }

    // Remove a photo
    window.removePhoto = function(photoId) {
        photos = photos.filter(p => p.id !== photoId);
        renderPhotoGallery();
        updateHiddenInputs();
    };

    // Handle multiple photo selection from native camera/file input
    window.handlePhotosSelected = function(input) {
        const files = Array.from(input.files);
        if (!files.length) return;

        const remainingSlots = MAX_PHOTOS - photos.length;
        if (files.length > remainingSlots) {
            alert('Maksimal ' + MAX_PHOTOS + ' foto. Anda masih bisa menambahkan ' + remainingSlots + ' foto lagi.');
            input.value = '';
            return;
        }

        let processedCount = 0;
        files.forEach(file => {
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar: ' + file.name);
                processedCount++;
                if (processedCount === files.length) input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto maksimal 5MB: ' + file.name);
                processedCount++;
                if (processedCount === files.length) input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const photoId = 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const photo = {
                    id: photoId,
                    dataUrl: e.target.result, // original photo for now
                    lat: '',
                    lng: '',
                    accuracy: '',
                    gpsStatus: 'loading'
                };
                photos.push(photo);
                renderPhotoGallery();

                // Get GPS for this photo, then create combined image
                getCurrentLocationForPhoto(photoId, e.target.result);
                processedCount++;
                if (processedCount === files.length) input.value = '';
            };
            reader.readAsDataURL(file);
        });
    };

    // Get GPS for a specific photo using Capacitor Geolocation, then create combined image
    async function getCurrentLocationForPhoto(photoId, originalDataUrl) {
        const photoIndex = photos.findIndex(p => p.id === photoId);
        if (photoIndex === -1) return;

        try {
            let position;

            if (window.Capacitor && window.Capacitor.Plugins && window.Capacitor.Plugins.Geolocation) {
                position = await window.Capacitor.Plugins.Geolocation.getCurrentPosition({
                    enableHighAccuracy: false,
                    timeout: 15000
                });
            } else if (navigator.geolocation) {
                position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: false,
                        timeout: 15000,
                        maximumAge: 60000
                    });
                });
            } else {
                throw new Error('Geolocation not supported');
            }

            const accuracy = position.coords.accuracy;
            const lat = position.coords.latitude.toFixed(6);
            const lng = position.coords.longitude.toFixed(6);

            // Get full address via reverse geocoding (Nominatim / OpenStreetMap)
            let address = '';
            try {
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`);
                const data = await resp.json();
                address = data.display_name || '';
            } catch (err) {
                console.warn('Reverse geocode error:', err);
            }

            // Create combined image (photo + map + GPS text + address) via canvas
            const combinedDataUrl = await createCombinedImage(originalDataUrl, lat, lng, address);

            // Update photo with combined image and GPS
            photos[photoIndex].dataUrl = combinedDataUrl;
            photos[photoIndex].lat = lat;
            photos[photoIndex].lng = lng;
            photos[photoIndex].accuracy = accuracy;
            photos[photoIndex].address = address;
            photos[photoIndex].gpsStatus = 'success';

            renderPhotoGallery();
            updateHiddenInputs();
        } catch (err) {
            photos[photoIndex].lat = '';
            photos[photoIndex].lng = '';
            photos[photoIndex].accuracy = '';
            photos[photoIndex].address = '';
            photos[photoIndex].gpsStatus = 'error';
            
            let msg = 'GPS tidak tersedia';
            if (err.code === 1 || err.message?.includes('denied')) {
                msg += ' (izin lokasi ditolak)';
            } else if (err.code === 2 || err.message?.includes('unavailable')) {
                msg += ' (lokasi tidak tersedia)';
            } else if (err.code === 3 || err.message?.includes('timeout')) {
                msg += ' (timeout)';
            }
            console.warn('GPS error for photo ' + (photoIndex + 1) + ':', err);
            
            // Even without GPS, create combined image (just photo, no map overlay)
            const combinedDataUrl = await createCombinedImage(originalDataUrl, '', '', '');
            photos[photoIndex].dataUrl = combinedDataUrl;
            
            renderPhotoGallery();
            updateHiddenInputs();
        }
    }

    // Create combined image: photo + full-width map tile (bottom) + GPS text on map
    function createCombinedImage(photoDataUrl, lat, lng, address) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                // Canvas size matches photo
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');

                // Draw photo
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                if (lat && lng) {
                    const tile = latLngToTile(parseFloat(lat), parseFloat(lng), 16);
                    const mapUrl = `https://tile.openstreetmap.org/${tile.z}/${tile.x}/${tile.y}.png`;

                    const mapImg = new Image();
                    mapImg.crossOrigin = 'anonymous';
                    mapImg.src = mapUrl;

                    mapImg.onload = () => {
                        drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, tile.fracX, tile.fracY);
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                    mapImg.onerror = () => {
                        // If map fails, just draw GPS text
                        drawGPSOnly(ctx, canvas, lat, lng, address);
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                } else {
                    // No GPS, just return photo
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                }
            };
            img.src = photoDataUrl;
        });
    }

    function drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, fracX = 0.5, fracY = 0.5) {
        // Map (left) + GPS text (right) with margins
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

        // Draw map tile
        ctx.drawImage(mapImg, mapX, mapY, mapW, mapH);

        // Marker: pin-style map pin di posisi GPS AKTUAL dalam tile
        const pinX = mapX + fracX * mapW;
        const pinY = mapY + fracY * mapH;
        const pinSize = 40;
        
        // Pin tip (ujung tajam) berada di koordinat GPS, bukan center
        const pinTipY = pinSize * 1.2;
        
        ctx.save();
        ctx.translate(pinX, pinY - pinTipY);
        
        // Pin body (teardrop shape)
        ctx.fillStyle = '#E50000';
        ctx.beginPath();
        // Top circle
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
        // Right curve down
        ctx.bezierCurveTo(
            pinSize * 0.55, -pinSize * 0.3,
            pinSize * 0.55, pinSize * 0.3,
            0, pinSize * 1.2
        );
        // Left curve up
        ctx.bezierCurveTo(
            -pinSize * 0.55, pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3
        );
        ctx.closePath();
        ctx.fill();
        
        // Pin shadow/depth
        ctx.fillStyle = '#AA0000';
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.45, Math.PI, 0);
        ctx.bezierCurveTo(
            pinSize * 0.45, -pinSize * 0.3,
            pinSize * 0.45, pinSize * 0.15,
            0, pinSize * 0.9
        );
        ctx.bezierCurveTo(
            -pinSize * 0.45, pinSize * 0.15,
            -pinSize * 0.45, -pinSize * 0.3,
            -pinSize * 0.45, -pinSize * 0.3
        );
        ctx.fill();
        
        // White inner circle (hole/center)
        ctx.fillStyle = '#FFFFFF';
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.22, 0, Math.PI * 2);
        ctx.fill();
        
        // White stroke outline
        ctx.strokeStyle = '#FFFFFF';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
        ctx.bezierCurveTo(
            pinSize * 0.55, -pinSize * 0.3,
            pinSize * 0.55, pinSize * 0.3,
            0, pinSize * 1.2
        );
        ctx.bezierCurveTo(
            -pinSize * 0.55, pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3
        );
        ctx.stroke();
        
        ctx.restore();

        // GPS text di sebelah kanan peta
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
        
        // Full address
        if (address) {
            // Split address into multiple lines if too long
            const maxWidth = textW + 30;
            const words = address.split(' ');
            let lines = [];
            let currentLine = 'Alamat: ';
            
            for (const word of words) {
                const testLine = currentLine + word + ' ';
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && currentLine !== 'Alamat: ') {
                    lines.push(currentLine.trim());
                    currentLine = word + ' ';
                } else {
                    currentLine = testLine;
                }
            }
            if (currentLine.trim()) lines.push(currentLine.trim());
            
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '68px system-ui, sans-serif';
            lines.forEach((line, i) => {
                ctx.fillText(line, textX, textY + 320 + i * 75);
            });
        }
    }

    function drawGPSOnly(ctx, canvas, lat, lng, address) {
        // Map (left) + GPS text (right) with margins
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
        
        // Dark background for text
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(textX - 18, textY, textW + 30, textH);
        
        ctx.fillStyle = '#FFFFFF';
        ctx.font = 'bold 84px system-ui, sans-serif';
        ctx.fillText('GPS: ' + lat + ', ' + lng, textX, textY + 120);
        ctx.fillStyle = '#CCCCCC';
        ctx.font = '64px system-ui, sans-serif';
        ctx.fillText('Peta tidak tersedia', textX, textY + 220);
        
        // Full address
        if (address) {
            const maxWidth = textW + 30;
            const words = address.split(' ');
            let lines = [];
            let currentLine = 'Alamat: ';
            
            for (const word of words) {
                const testLine = currentLine + word + ' ';
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && currentLine !== 'Alamat: ') {
                    lines.push(currentLine.trim());
                    currentLine = word + ' ';
                } else {
                    currentLine = testLine;
                }
            }
            if (currentLine.trim()) lines.push(currentLine.trim());
            
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '68px system-ui, sans-serif';
            lines.forEach((line, i) => {
                ctx.fillText(line, textX, textY + 320 + i * 75);
            });
        }
    }

    // Open photo preview modal
    window.openPhotoPreview = function(photoId) {
        const photo = photos.find(p => p.id === photoId);
        if (!photo || !photo.dataUrl) return;
        
        const previewImg = document.getElementById('previewImage');
        const previewGps = document.getElementById('previewGps');
        const modal = document.getElementById('photoPreviewModal');
        
        previewImg.src = photo.dataUrl;
        
        if (photo.lat && photo.lng) {
            previewGps.innerHTML = '📍 ' + photo.lat + ', ' + photo.lng + (photo.accuracy ? '  (±' + Math.round(photo.accuracy) + 'm)' : '') 
                + (photo.address ? '<br><span style="font-size: 0.85em;">' + photo.address + '</span>' : '');
        } else {
            previewGps.textContent = 'GPS tidak tersedia';
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    // Close photo preview modal
    window.closePhotoPreview = function() {
        const modal = document.getElementById('photoPreviewModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoPreview();
        }
    });

    // Close modal on backdrop click
    document.getElementById('photoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoPreview();
        }
    });

    // Populate hidden inputs on submit
    form.addEventListener('submit', function(e) {
        if (photos.length === 0) {
            e.preventDefault();
            alert('Ambil setidaknya 1 foto selfie!');
            return false;
        }

        updateHiddenInputs();

        const hasGps = photos.some(p => p.lat && p.lng);
        if (!hasGps) {
            if (!confirm('GPS tidak tersedia untuk semua foto. Lanjutkan kirim laporan tanpa lokasi?')) {
                e.preventDefault();
                return false;
            }
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
    });
});
</script>
@endpush