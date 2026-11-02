@extends('pengawas.layouts.app', ['pageTitle' => 'Inspeksi Dapur'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Form Inspeksi Dapur</h1>
        <div class="muted">Isi formulir pemeriksaan dapur (maksimal 6 foto, setiap foto dilengkapi peta + GPS)</div>
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

        {{-- Indikator 1: Kebersihan dan sanitasi --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-4">
            <h3 class="font-medium text-[var(--color-secondary)]">Kebersihan dan sanitasi</h3>
            <input type="hidden" name="kategori[]" value="Kebersihan dan sanitasi">
            
            {{-- Foto --}}
            <div class="space-y-3">
                <div id="card_indikator_1" class="relative group cursor-pointer" onclick="toggleIndicatorCamera(1)" data-indikator="1">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">📸 Foto Kebersihan dan sanitasi</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2-6 12-6-12 4-2z"></path></svg>
                    </div>
                    <input type="file" id="photo_indikator_1" name="photo_indikator_1" accept="image/*" capture="environment" class="hidden" data-indikator="1" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_indikator_1_preview" class="mt-2 hidden"></div>
                </div>
            </div>
            
            {{-- Select Hasil --}}
            <div>
                <select name="hasil[]" class="input-field w-full">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="diperiksa">Sesuai standar</option>
                    <option value="temuan_sedang">Perlu perbaikan</option>
                    <option value="temuan_kritis">Temuan kritis</option>
                </select>
            </div>
        </div>

        {{-- Indikator 2: Penyimpanan bahan --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-4">
            <h3 class="font-medium text-[var(--color-secondary)]">Penyimpanan bahan</h3>
            <input type="hidden" name="kategori[]" value="Penyimpanan bahan">
            
            {{-- Foto --}}
            <div class="space-y-3">
                <div id="card_indikator_2" class="relative group cursor-pointer" onclick="toggleIndicatorCamera(2)" data-indikator="2">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">📦 Foto Penyimpanan bahan</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <input type="file" id="photo_indikator_2" name="photo_indikator_2" accept="image/*" capture="environment" class="hidden" data-indikator="2" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_indikator_2_preview" class="mt-2 hidden"></div>
                </div>
            </div>
            
            {{-- Select Hasil --}}
            <div>
                <select name="hasil[]" class="input-field w-full">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="diperiksa">Sesuai standar</option>
                    <option value="temuan_sedang">Perlu perbaikan</option>
                    <option value="temuan_kritis">Temuan kritis</option>
                </select>
            </div>
        </div>

        {{-- Indikator 3: Proses pengolahan --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-4">
            <h3 class="font-medium text-[var(--color-secondary)]">Proses pengolahan</h3>
            <input type="hidden" name="kategori[]" value="Proses pengolahan">
            
            {{-- Foto --}}
            <div class="space-y-3">
                <div id="card_indikator_3" class="relative group cursor-pointer" onclick="toggleIndicatorCamera(3)" data-indikator="3">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">⚙️ Foto Proses pengolahan</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <input type="file" id="photo_indikator_3" name="photo_indikator_3" accept="image/*" capture="environment" class="hidden" data-indikator="3" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_indikator_3_preview" class="mt-2 hidden"></div>
                </div>
            </div>
            
            {{-- Select Hasil --}}
            <div>
                <select name="hasil[]" class="input-field w-full">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="diperiksa">Sesuai standar</option>
                    <option value="temuan_sedang">Perlu perbaikan</option>
                    <option value="temuan_kritis">Temuan kritis</option>
                </select>
            </div>
        </div>

        {{-- Indikator 4: Distribusi makanan --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-4">
            <h3 class="font-medium text-[var(--color-secondary)]">Distribusi makanan</h3>
            <input type="hidden" name="kategori[]" value="Distribusi makanan">
            
            {{-- Foto --}}
            <div class="space-y-3">
                <div id="card_indikator_4" class="relative group cursor-pointer" onclick="toggleIndicatorCamera(4)" data-indikator="4">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">🍽️ Foto Distribusi makanan</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-7m-7 0h-3m11 0h-4m4 0h-1m-1 0v-3m0 0v-3m0 0v-3m0-3v-6m0 4V5m0 3l-3-3m-3 3l3 3"></path></svg>
                    </div>
                    <input type="file" id="photo_indikator_4" name="photo_indikator_4" accept="image/*" capture="environment" class="hidden" data-indikator="4" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_indikator_4_preview" class="mt-2 hidden"></div>
                </div>
            </div>
            
            {{-- Select Hasil --}}
            <div>
                <select name="hasil[]" class="input-field w-full">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="diperiksa">Sesuai standar</option>
                    <option value="temuan_sedang">Perlu perbaikan</option>
                    <option value="temuan_kritis">Temuan kritis</option>
                </select>
            </div>
        </div>

        {{-- Indikator 5: Peralatan dan fasilitas --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-4">
            <h3 class="font-medium text-[var(--color-secondary)]">Peralatan dan fasilitas</h3>
            <input type="hidden" name="kategori[]" value="Peralatan dan fasilitas">
            
            {{-- Foto --}}
            <div class="space-y-3">
                <div id="card_indikator_5" class="relative group cursor-pointer" onclick="toggleIndicatorCamera(5)" data-indikator="5">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">🔧 Foto Peralatan dan fasilitas</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <input type="file" id="photo_indikator_5" name="photo_indikator_5" accept="image/*" capture="environment" class="hidden" data-indikator="5" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_indikator_5_preview" class="mt-2 hidden"></div>
                </div>
            </div>
            
            {{-- Select Hasil --}}
            <div>
                <select name="hasil[]" class="input-field w-full">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="diperiksa">Sesuai standar</option>
                    <option value="temuan_sedang">Perlu perbaikan</option>
                    <option value="temuan_kritis">Temuan kritis</option>
                </select>
            </div>
        </div>

        {{-- Foto Selfie di Depan Dapur (di akhir sebelum catatan) --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-3">
            <h3 class="font-medium text-[var(--color-secondary)]">Foto Selfie di Depan Dapur</h3>
            <div class="space-y-3">
                <div id="card_indikator_selfie" class="relative group cursor-pointer" onclick="toggleIndicatorCamera('selfie')" data-indikator="selfie">
                    <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
                        <span class="text-sm font-medium text-[var(--color-text)]">🤳 Selfie di depan dapur</span>
                        <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <input type="file" id="photo_selfie" name="photo_selfie" accept="image/*" capture="environment" class="hidden" data-indikator="selfie" onchange="handleIndicatorPhotoSelected(this)">
                    <div id="photo_selfie_preview" class="mt-2 hidden"></div>
                </div>
            </div>
        </div>

        {{-- Hidden inputs for photo data (base64) and GPS arrays --}}
        <input type="hidden" name="photo_indikator_1" id="photo_indikator_1_data" value="">
        <input type="hidden" name="photo_indikator_2" id="photo_indikator_2_data" value="">
        <input type="hidden" name="photo_indikator_3" id="photo_indikator_3_data" value="">
        <input type="hidden" name="photo_indikator_4" id="photo_indikator_4_data" value="">
        <input type="hidden" name="photo_indikator_5" id="photo_indikator_5_data" value="">
        <input type="hidden" name="photo_selfie" id="photo_selfie_data" value="">
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

    // Update hidden inputs per-indikator
    function updateHiddenInputs() {
        // Reset all
        for (let i = 1; i <= 5; i++) {
            document.getElementById('photo_indikator_' + i + '_data').value = '';
        }
        document.getElementById('photo_selfie_data').value = '';
        document.getElementById('latitudeInput').value = '';
        document.getElementById('longitudeInput').value = '';
        document.getElementById('addressInput').value = '';

        photos.forEach((p) => {
            if (p.indikator === 'selfie') {
                document.getElementById('photo_selfie_data').value = p.dataUrl;
            } else {
                document.getElementById('photo_indikator_' + p.indikator + '_data').value = p.dataUrl;
            }
        });
        // lat/lng/address arrays (first 5 = indikators, last = selfie)
        const lats = [], lngs = [], addrs = [];
        for (let i = 1; i <= 5; i++) {
            const p = photos.find(x => x.indikator == i);
            lats.push(p && p.lat ? p.lat : '');
            lngs.push(p && p.lng ? p.lng : '');
            addrs.push(p && p.address ? p.address : '');
        }
        const selfie = photos.find(x => x.indikator === 'selfie');
        lats.push(selfie && selfie.lat ? selfie.lat : '');
        lngs.push(selfie && selfie.lng ? selfie.lng : '');
        addrs.push(selfie && selfie.address ? selfie.address : '');
        document.getElementById('latitudeInput').value = lats.join(',');
        document.getElementById('longitudeInput').value = lngs.join(',');
        document.getElementById('addressInput').value = addrs.join(',');
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

    // Render per-indikator photo preview
    function renderIndicatorPreview(indikator) {
        const p = photos.find(x => x.indikator == indikator);
        const el = document.getElementById('photo_indikator_' + indikator + '_preview');
        const selfieEl = document.getElementById('photo_selfie_preview');
        const targetEl = indikator === 'selfie' ? selfieEl : el;

        if (!p || !p.dataUrl) {
            targetEl.innerHTML = '';
            targetEl.classList.add('hidden');
            return;
        }

        const gpsHtml = p.lat && p.lng
            ? `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-[var(--color-primary)]">📍 ${p.lat}, ${p.lng}</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">${p.address ? p.address : 'GPS tersimpan'}</p>
               </div>`
            : `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <p class="text-[9px] text-[var(--color-text-muted)]">Memproses GPS...</p>
               </div>`;

        targetEl.innerHTML = `
            <div class="relative group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2">
                <img src="${p.dataUrl}" alt="Foto ${indikator}" class="w-full h-32 object-cover rounded-md cursor-pointer" onclick="openIndicatorPhotoPreview('${indikator}')">
                <button type="button" onclick="removeIndicatorPhoto('${indikator}')" class="delete-photo-btn absolute top-2 right-4 z-50 bg-white bg-opacity-100 p-2 rounded-full text-red-600 hover:text-red-800 shadow-lg" aria-label="Hapus foto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                ${gpsHtml}
            </div>
        `;
        targetEl.classList.remove('hidden');
    }

    // Open photo preview modal for indicator photo
    window.openIndicatorPhotoPreview = function(indikator) {
        const p = photos.find(x => x.indikator == indikator);
        if (!p || !p.dataUrl) return;

        const previewImg = document.getElementById('previewImage');
        const previewGps = document.getElementById('previewGps');
        const modal = document.getElementById('photoPreviewModal');

        previewImg.src = p.dataUrl;

        if (p.lat && p.lng) {
            previewGps.innerHTML = '📍 ' + p.lat + ', ' + p.lng + (p.accuracy ? '  (±' + Math.round(p.accuracy) + 'm)' : '') +
                (p.address ? '<br><span style="font-size: 0.85em;">' + p.address + '</span>' : '');
        } else {
            previewGps.textContent = 'GPS tidak tersedia';
        }

        hideAllTrashButtons();

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    // Update card button muted state based on whether photo exists
    function updateCardButtonStates() {
        for (let i = 1; i <= 5; i++) {
            const card = document.getElementById('card_indikator_' + i);
            const p = photos.find(x => x.indikator == i);
            if (p && p.dataUrl) {
                card.onclick = null;
            } else {
                card.onclick = function() { toggleIndicatorCamera(i); };
            }
        }
        const selfieCard = document.getElementById('card_indikator_selfie');
        const selfieP = photos.find(x => x.indikator === 'selfie');
        if (selfieP && selfieP.dataUrl) {
            selfieCard.onclick = null;
        } else {
            selfieCard.onclick = function() { toggleIndicatorCamera('selfie'); };
        }
    }

    // Hide/show all trash buttons to prevent z-index overlap with preview modal
    function hideAllTrashButtons() {
        document.querySelectorAll('.delete-photo-btn').forEach(btn => {
            btn.style.display = 'none';
        });
    }

    function showAllTrashButtons() {
        document.querySelectorAll('.delete-photo-btn').forEach(btn => {
            btn.style.display = '';
        });
    }

    // Toggle camera or open preview based on whether photo exists
    window.toggleIndicatorCamera = function(indikator) {
        const p = photos.find(x => x.indikator == indikator);
        if (p && p.dataUrl) {
            openIndicatorPhotoPreview(indikator);
        } else {
            const inputId = indikator === 'selfie' ? 'photo_selfie' : 'photo_indikator_' + indikator;
            document.getElementById(inputId).click();
        }
    };

    // Remove an indicator photo
    window.removeIndicatorPhoto = function(indikator) {
        photos = photos.filter(p => p.indikator != indikator);
        renderIndicatorPreview(indikator);
        updateCardButtonStates();
        updateHiddenInputs();
    };

    // Handle indicator photo selection from native camera/file input
    window.handleIndicatorPhotoSelected = async function(input) {
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

        const indikator = input.getAttribute('data-indikator');

        // Show loading
        showLoading(true);

        try {
            // Compress if > 5MB
            let dataUrl = file.size > 5 * 1024 * 1024 ? await compressImage(file, 5) : await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });

            // Remove existing photo for this indikator if any
            photos = photos.filter(p => p.indikator != indikator);

            const photoId = 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const photo = {
                id: photoId,
                dataUrl: dataUrl,
                indikator: indikator,
                lat: '',
                lng: '',
                accuracy: '',
                address: '',
                gpsStatus: 'loading'
            };
            photos.push(photo);

            renderIndicatorPreview(indikator);
            updateCardButtonStates();
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
            photos[photoIndex].dataUrl = combinedDataUrl;
            photos[photoIndex].lat = lat;
            photos[photoIndex].lng = lng;
            photos[photoIndex].accuracy = accuracy;
            photos[photoIndex].address = address;
            photos[photoIndex].gpsStatus = 'success';

            renderIndicatorPreview(photos[photoIndex].indikator);
            updateCardButtonStates();
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
            
            renderIndicatorPreview(photos[photoIndex].indikator);
            updateCardButtonStates();
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
        showAllTrashButtons();
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

    // Make submit handler async for native confirm
    form.addEventListener('submit', async function(e) {
        // ALWAYS prevent default first - we'll submit manually if all validations pass
        e.preventDefault();

        // Konfirmasi sebelum kirim laporan inspeksi
        const confirmed = window.confirm('Kirim laporan sekarang?');
        if (!confirmed) {
            return false;
        }

        // Validate all 5 hasil select dropdowns have values
        const hasilSelects = document.querySelectorAll('select[name="hasil[]"]');
        const indikatorNames = ['Kebersihan dan sanitasi', 'Penyimpanan bahan', 'Proses pengolahan', 'Distribusi makanan', 'Peralatan dan fasilitas'];
        let missingIndikator = null;
        hasilSelects.forEach((select, index) => {
            if (!select.value) {
                missingIndikator = indikatorNames[index];
            }
        });
        if (missingIndikator) {
            window.alert('Pilih hasil: ' + missingIndikator);
            return false;
        }

        // Check all 5 indikators + selfie have photos
        const indikatorPhotos = [];
        for (let i = 1; i <= 5; i++) {
            indikatorPhotos.push(photos.find(p => p.indikator == i));
        }
        const selfiePhoto = photos.find(p => p.indikator === 'selfie');
        const allHavePhotos = indikatorPhotos.every(p => p) && selfiePhoto;

        if (!allHavePhotos) {
            window.alert('Lengkapi semua foto dulu');
            return false;
        }

        // Check if any photo still processing (no watermark yet)
        const stillProcessing = photos.some(p => p.gpsStatus === 'loading');
        if (stillProcessing) {
            window.alert('Tunggu foto selesai diproses');
            return false;
        }

        updateHiddenInputs();

        const hasGps = photos.some(p => p.lat && p.lng);
        if (!hasGps) {
            const gpsConfirmed = window.confirm('GPS tidak tersedia. Lanjutkan tanpa lokasi?');
            if (!gpsConfirmed) {
                return false;
            }
        }

        // All validations passed - submit the form programmatically
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
        form.submit();
    });
});
</script>
@endpush