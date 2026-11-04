@extends('pengawas.layouts.app', ['pageTitle' => 'Inspeksi Dapur'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Form Inspeksi Dapur</h1>
        <div class="muted">Isi formulir pemeriksaan dapur (1 foto dilengkapi peta + GPS)</div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999] hidden">
    <div class="bg-[var(--color-surface)] p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-[var(--color-primary)] border-t-transparent rounded-full animate-spin"></div>
        <p class="text-[var(--color-text)] font-medium">Memproses foto…</p>
        <p class="text-xs text-[var(--color-text-muted)]">Harap tunggu, GPS dan peta sedang dimuat</p>
    </div>
</div>

<div class="panel max-w-[720px]">
    <form method="POST" action="{{ route('pengawas.laporan.store') }}" id="laporanForm" class="space-y-6">
        @csrf

        {{-- Error alert untuk validasi server --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <h3 class="text-red-800 font-medium text-sm mb-2">Validasi gagal</h3>
                <ul class="text-red-700 text-sm list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Status laporan (default diperiksa) --}}
        <input type="hidden" name="status" value="diperiksa">

        {{-- Pilih Dapur --}}
        <div>
            <label for="dapur_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Pilih Dapur <span class="text-red-500">*</span></label>
            <select id="dapur_id" name="dapur_id" class="input-field">
                <option value="">-- Pilih Dapur --</option>
                @foreach (auth()->user()->dapurs as $dapur)
                    <option value="{{ $dapur->id }}" {{ $selectedDapurId == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                @endforeach
            </select>
            @error('dapur_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Satu card untuk semua indikator (hasil dropdown saja, foto di bawah) --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-6">

            {{-- Indikator 1: Kebersihan dan sanitasi --}}
            <div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
                <h3 class="font-medium text-[var(--color-secondary)]">Kebersihan dan sanitasi <span class="text-red-500">*</span></h3>
                <input type="hidden" name="kategori[]" value="Kebersihan dan sanitasi">
                
                {{-- Select Hasil --}}
                <div class="mt-3">
                    <select name="hasil[]" class="input-field w-full">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Indikator 2: Penyimpanan bahan --}}
            <div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
                <h3 class="font-medium text-[var(--color-secondary)]">Penyimpanan bahan <span class="text-red-500">*</span></h3>
                <input type="hidden" name="kategori[]" value="Penyimpanan bahan">
                
                {{-- Select Hasil --}}
                <div class="mt-3">
                    <select name="hasil[]" class="input-field w-full">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Indikator 3: Proses pengolahan --}}
            <div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
                <h3 class="font-medium text-[var(--color-secondary)]">Proses pengolahan <span class="text-red-500">*</span></h3>
                <input type="hidden" name="kategori[]" value="Proses pengolahan">
                
                {{-- Select Hasil --}}
                <div class="mt-3">
                    <select name="hasil[]" class="input-field w-full">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Indikator 4: Distribusi makanan --}}
            <div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
                <h3 class="font-medium text-[var(--color-secondary)]">Distribusi makanan <span class="text-red-500">*</span></h3>
                <input type="hidden" name="kategori[]" value="Distribusi makanan">
                
                {{-- Select Hasil --}}
                <div class="mt-3">
                    <select name="hasil[]" class="input-field w-full">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                </div>
            </div>

            {{-- Indikator 5: Peralatan dan fasilitas --}}
            <div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
                <h3 class="font-medium text-[var(--color-secondary)]">Peralatan dan fasilitas <span class="text-red-500">*</span></h3>
                <input type="hidden" name="kategori[]" value="Peralatan dan fasilitas">
                
                {{-- Select Hasil --}}
                <div class="mt-3">
                    <select name="hasil[]" class="input-field w-full">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="diperiksa">Sesuai standar</option>
                        <option value="temuan_sedang">Perlu perbaikan</option>
                        <option value="temuan_kritis">Temuan kritis</option>
                    </select>
                    @error('hasil')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        {{-- Foto tunggal (Selfie/Foto Utama) --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-3">
            <h3 class="font-medium text-[var(--color-secondary)]">Foto Dapur <span class="text-red-500">*</span></h3>
            <div class="space-y-3">
                <div id="card_photo" class="relative group cursor-pointer" data-action="toggle-camera">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)] flex items-center gap-2">
                            <svg class="w-5 h-5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Ambil foto dapur <span class="text-red-500">*</span></span>
                        </span>
                        
                    </div>
                    <input type="file" id="photo_input" accept="image/*" capture="environment" class="opacity-0 absolute inset-0 w-0 h-0" data-action="handle-photo-selected">
                    <div id="photo_preview" class="mt-2 hidden"></div>
                </div>
            </div>
        </div>

        {{-- Hidden inputs for photo data (base64) and GPS --}}
        <input type="hidden" name="selfie_photo" id="photo_data" value="">
        <input type="hidden" name="latitude" id="latitudeInput" value="">
        <input type="hidden" name="longitude" id="longitudeInput" value="">
        <input type="hidden" name="address" id="addressInput" value="">

        {{-- Error untuk foto dan GPS (validasi server) --}}
        @error('selfie_photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('latitude')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('longitude')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        {{-- Catatan Pengawas --}}
        <div>
            <label for="catatan" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Catatan Pengawas <span class="text-[var(--color-text-muted)]">(opsional)</span></label>
            <textarea id="catatan" name="catatan" rows="4" class="input-field" placeholder="Tuliskan kondisi yang ditemukan…"></textarea>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 justify-end pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="btn-primary" id="submitBtn">Kirim Laporan</button>
        </div>
    </form>
</div>

<!-- Photo Preview Modal -->
<div id="photoPreviewModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[1000] hidden">
    <div class="relative max-w-4xl max-h-[90vh] w-full mx-4">
        <button type="button" data-action="close-photo-preview" class="absolute -top-12 right-0 bg-[var(--color-surface)] text-[var(--color-text)] p-2 rounded-full cursor-pointer shadow-lg z-10" aria-label="Tutup preview">
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
    
    let photo = null; // {dataUrl, lat, lng, accuracy, address, gpsStatus}
    
    // Convert lat/lng to OSM tile x,y at given zoom
    function latLngToTile(lat, lng, zoom) {
        const n = Math.pow(2, zoom);
        const xFloat = (lng + 180) / 360 * n;
        const yFloat = (1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * n;
        const x = Math.floor(xFloat);
        const y = Math.floor(yFloat);
        return { x, y, z: zoom, fracX: xFloat - x, fracY: yFloat - y };
    }

    // Compress image to maxSizeMB using canvas downscale + JPEG quality
    async function compressImage(file, maxSizeMB = 5) {
        if (file.size <= maxSizeMB * 1024 * 1024) {
            return file;
        }
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const maxDim = 1600;
                    let { width, height } = img;
                    if (width > height && width > maxDim) {
                        height = (maxDim / width) * height;
                        width = maxDim;
                    } else if (height > maxDim) {
                        width = (maxDim / height) * width;
                        height = maxDim;
                    }
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.8;
                    let dataUrl = canvas.toDataURL('image/jpeg', quality);
                    while (dataUrl.length * 0.75 > maxSizeMB * 1024 * 1024 && quality > 0.1) {
                        quality -= 0.1;
                        dataUrl = canvas.toDataURL('image/jpeg', quality);
                    }
                    resolve(dataUrl);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Update hidden inputs
    function updateHiddenInputs() {
        document.getElementById('photo_data').value = photo?.dataUrl || '';
        document.getElementById('latitudeInput').value = photo?.lat || '';
        document.getElementById('longitudeInput').value = photo?.lng || '';
        document.getElementById('addressInput').value = photo?.address || '';
    }

    // Show/hide loading overlay
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
        }
    }

    // Render photo preview
    function renderPreview() {
        const el = document.getElementById('photo_preview');
        if (!photo || !photo.dataUrl) {
            el.innerHTML = '';
            el.classList.add('hidden');
            return;
        }

        let gpsStatusHtml;
        if (photo.gpsStatus === 'loading') {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-[var(--color-secondary)]">⏳ GPS memproses…</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">Tunggu hingga selesai sebelum kirim laporan</p>
               </div>`;
        } else if (photo.lat && photo.lng) {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-[var(--color-primary)]">✅ GPS tersimpan</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">${photo.lat}, ${photo.lng}</p>
                <p class="text-[9px] text-[var(--color-text-muted)]">${photo.address ? photo.address : 'GPS tersimpan'}</p>
               </div>`;
        } else {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-red-600">❌ GPS gagal</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">Lokasi tidak tersedia. Coba ambil foto ulang.</p>
               </div>`;
        }

        el.innerHTML = `
            <div class="relative group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2">
                <img src="${photo.dataUrl}" alt="Foto dapur" class="w-full h-48 object-cover rounded-md cursor-pointer" data-action="open-photo-preview">
                <button type="button" data-action="remove-photo" class="delete-photo-btn absolute top-2 right-4 z-50 bg-white bg-opacity-100 p-2 rounded-full text-red-600 hover:text-red-800 shadow-lg" aria-label="Hapus foto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                ${gpsStatusHtml}
            </div>
        `;
        el.classList.remove('hidden');
    }

    // Open photo preview modal
    function openPhotoPreview() {
        if (!photo || !photo.dataUrl) return;

        const previewImg = document.getElementById('previewImage');
        const previewGps = document.getElementById('previewGps');
        const modal = document.getElementById('photoPreviewModal');

        previewImg.src = photo.dataUrl;

        if (photo.lat && photo.lng) {
            previewGps.innerHTML = '📍 ' + photo.lat + ', ' + photo.lng + (photo.accuracy ? '  (±' + Math.round(photo.accuracy) + 'm)' : '') +
                (photo.address ? '<br><span class="text-[0.7rem]">' + photo.address + '</span>' : '');
        } else {
            previewGps.textContent = 'GPS tidak tersedia';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close photo preview modal
    function closePhotoPreview() {
        document.getElementById('photoPreviewModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Toggle camera or open preview based on whether photo exists
    function toggleCamera() {
        if (photo && photo.dataUrl) {
            openPhotoPreview();
        } else {
            document.getElementById('photo_input').click();
        }
    }

    // Remove photo
    function removePhoto() {
        photo = null;
        renderPreview();
        updateHiddenInputs();
    }

    // Handle photo selection from native camera/file input
    async function handlePhotoSelected(input) {
        const files = Array.from(input.files);
        if (!files.length) return;

        const file = files[0];
        if (!file.type.startsWith('image/')) {
            window.alert('Bukan file gambar');
            input.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            window.alert('Foto terlalu besar (maks 5MB)');
            input.value = '';
            return;
        }

        // Show loading
        showLoading(true);

        try {
            // Compress if > 5MB
            let dataUrl = file.size > 5 * 1024 * 1024 ? await compressImage(file, 5) : await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });

            const photoId = 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            photo = {
                id: photoId,
                dataUrl: dataUrl,
                lat: '',
                lng: '',
                accuracy: '',
                address: '',
                gpsStatus: 'loading'
            };

            renderPreview();
            updateHiddenInputs();

            // Reset input
            input.value = '';

            // Process GPS + watermark in background
            getCurrentLocationForPhoto(photoId, dataUrl);
        } catch (err) {
            console.error('Photo processing error:', err);
            window.alert('Gagal memproses foto');
        } finally {
            showLoading(false);
        }
    }

    // Get GPS for a specific photo using Capacitor Geolocation, then create combined image
    async function getCurrentLocationForPhoto(photoId, originalDataUrl) {
        if (!photo || photo.id !== photoId) return;

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

            // Get full address via reverse geocoding (Nominatim / OpenStreetMap) — blocking
            let address = '';
            try {
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`);
                const data = await resp.json();
                address = data.display_name || '';
            } catch (err) {
                console.warn('Reverse geocode error:', err);
            }

            // Create combined image WITH address — photo + map + GPS + alamat
            const combinedDataUrl = await createCombinedImage(originalDataUrl, lat, lng, address);

            // Update photo with complete data
            photo.dataUrl = combinedDataUrl;
            photo.lat = lat;
            photo.lng = lng;
            photo.accuracy = accuracy;
            photo.address = address;
            photo.gpsStatus = 'success';

            renderPreview();
            updateHiddenInputs();
        } catch (err) {
            photo.lat = '';
            photo.lng = '';
            photo.accuracy = '';
            photo.address = '';
            photo.gpsStatus = 'error';
            
            let msg = 'GPS tidak tersedia';
            if (err.code === 1 || err.message?.includes('denied')) {
                msg += ' (izin lokasi ditolak)';
            } else if (err.code === 2 || err.message?.includes('unavailable')) {
                msg += ' (lokasi tidak tersedia)';
            } else if (err.code === 3 || err.message?.includes('timeout')) {
                msg += ' (timeout)';
            }
            console.warn('GPS error:', err);
            
            // Even without GPS, create combined image (just photo, no map overlay)
            const combinedDataUrl = await createCombinedImage(originalDataUrl, '', '', '');
            photo.dataUrl = combinedDataUrl;
            
            renderPreview();
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
        ctx.fillText(lat + ', ' + lng, textX, textY + 120);
        
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

    // Make submit handler async for native confirm
    form.addEventListener('submit', async function(e) {
        // ALWAYS prevent default first - we'll submit manually if all validations pass
        e.preventDefault();

        // Debug: cek semua hidden input sebelum submit
        console.log('[Laporan Submit Debug] photo_data:', document.getElementById('photo_data').value ? 'TERISI' : 'KOSONG');
        console.log('[Laporan Submit Debug] latitude:', document.getElementById('latitudeInput').value);
        console.log('[Laporan Submit Debug] longitude:', document.getElementById('longitudeInput').value);
        console.log('[Laporan Submit Debug] address:', document.getElementById('addressInput').value);
        console.log('[Laporan Submit Debug] photo object:', photo);

        // 1. Validasi dapur harus dipilih dulu
        const dapurSelect = document.getElementById('dapur_id');
        if (!dapurSelect.value) {
            window.alert('Pilih dapur dulu');
            dapurSelect.focus();
            return false;
        }

        // 2. Validasi 5 dropdown hasil (urutan dari atas)
        const hasilSelects = document.querySelectorAll('select[name="hasil[]"]');
        const indikatorNames = ['Kebersihan dan sanitasi', 'Penyimpanan bahan', 'Proses pengolahan', 'Distribusi makanan', 'Peralatan dan fasilitas'];
        for (let i = 0; i < 5; i++) {
            if (!hasilSelects[i].value) {
                window.alert('Pilih hasil: ' + indikatorNames[i]);
                hasilSelects[i].focus();
                return false;
            }
        }

        // 3. Foto dapur
        if (!photo || !photo.dataUrl) {
            window.alert('Foto dapur belum diisi. Klik area foto untuk mengambil gambar.');
            document.getElementById('card_photo').focus();
            return false;
        }

        // 4. Validasi tidak ada foto yang masih memproses
        if (photo.gpsStatus === 'loading') {
            window.alert('Tunggu foto selesai diproses (GPS masih loading).');
            return false;
        }

        // 5. Validasi GPS: foto harus memiliki lokasi (WAJIB)
        if (!photo.lat || !photo.lng) {
            window.alert('GPS gagal didapatkan. Lokasi wajib terisi sebelum kirim laporan. Coba ambil foto ulang.');
            return false;
        }

        updateHiddenInputs();

        // 6. Konfirmasi sebelum kirim (di akhir setelah semua validasi lolos)
        const confirmed = window.confirm('Kirim laporan sekarang?\n\nData yang akan dikirim:\n- Dapur: ' + dapurSelect.options[dapurSelect.selectedIndex].text + '\n- Foto: tersedia\n- GPS: ' + photo.lat + ', ' + photo.lng);
        if (!confirmed) {
            return false;
        }

        // All validations passed - submit the form programmatically
        console.log('[Laporan Submit Debug] Semua validasi lolos, memanggil form.submit()');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
        form.submit();
    });

    // Toggle camera / open photo preview
    document.getElementById('card_photo').addEventListener('click', toggleCamera);

    // Handle photo selection
    document.getElementById('photo_input').addEventListener('change', function(e) {
    handlePhotoSelected(e.target);
});

    // Remove photo
    document.getElementById('photo_preview').addEventListener('click', function(e) {
        if (e.target.dataset.action === 'remove-photo') {
            removePhoto();
        }
    });

    // Open photo preview modal
    document.getElementById('photo_preview').addEventListener('click', function(e) {
        if (e.target.dataset.action === 'open-photo-preview') {
            openPhotoPreview();
        }
    });

    // Close modal on backdrop click
    document.getElementById('photoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoPreview();
        }
    });

    // Close photo preview modal button
    document.getElementById('photoPreviewModal').querySelector('[data-action="close-photo-preview"]').addEventListener('click', closePhotoPreview);
});
</script>
@endpush