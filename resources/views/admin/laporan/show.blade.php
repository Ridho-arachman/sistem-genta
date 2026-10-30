@extends('admin.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Detail Laporan</h1>
        <div class="muted">Verifikasi dan tindak lan-jut laporan penemuan</div>
    </div>
</div>

<div class="card p-8" id="laporanDetail" data-current-status="{{ $laporan->status }}">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Tanggal Laporan</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->created_at->format('d M Y') }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Lokasi / Dapur</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->dapur->nama ?? 'Serang 01' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->dapur->kepalaDapur?->name ?? 'Siti Rahma' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Kategori Pemeriksaan</p>
                <p class="font-semibold text-[var(--color-secondary)]">Kebersihan dan sanitasi</p>
            </div>
        </div>
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Status Saat Ini</p>
                <span class="badge badge-{{ $laporan->status === 'temuan' ? 'danger' : ($laporan->status === 'selesai' ? 'success' : 'secondary') }}">{{ ucfirst($laporan->status) }}</span>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)] mb-2">Status Baru</p>
                <select id="statusSelect" class="input-field">
                    <option value="diperiksa" {{ old('status', $laporan->status) === 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                    <option value="temuan" {{ old('status', $laporan->status) === 'temuan' ? 'selected' : '' }}>Temuan</option>
                    <option value="tindak lanjut" {{ old('status', $laporan->status) === 'tindak lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                    <option value="selesai" {{ old('status', $laporan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-[var(--color-border)] pt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Deskripsi Temuan</p>
        <div class="p-3 bg-[var(--color-surface)] rounded-lg text-sm text-[var(--color-text)]">
            {{ old('catatan', $laporan->catatan ?? 'Temuan kritikal pada area pendingin gudang: suhu tidak konsisten, ada titik panas pada pos POS-02 yang menyebabkan penurunan kualitas produk.') }}
        </div>
    </div>

    <!-- Catatan Admin -->
    <div id="catatanSection" class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2" id="catatanLabel">Catatan Verifikasi</p>
        <form method="POST" action="{{ route('admin.laporan.update', $laporan->id) }}" id="catatanForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" id="statusInput" value="{{ old('status', $laporan->status) }}">

            <!-- Photo capture -->
            <div class="mb-3">
                <label class="text-xs text-[var(--color-text-muted)] block mb-1">Foto Selfie Pengawas</label>
                <button type="button" class="btn-action btn-action-outline mt-2" onclick="openCamera()">
                    📸 Ambil Foto
                </button>

                @if ($laporan->selfie_photo)
                    <img id="existingSelfie" src="{{ asset('storage/' . $laporan->selfie_photo) }}" alt="Selfie pengawas" class="mt-2 max-w-xs rounded-lg border">
                @else
                    <img id="capturePreview" src="" alt="Preview foto" class="mt-2 max-w-xs rounded-lg hidden border">
                @endif

                <div id="locationMap" class="mt-2 w-full rounded-lg hidden border" style="height: 256px;"></div>
                <div id="gpsAccuracy" class="text-xs text-[var(--color-text-muted)] mt-1 hidden"></div>
                <input type="hidden" name="selfie_photo" id="selfiePhoto">
                <input type="hidden" name="latitude" id="latitudeInput" value="{{ $laporan->dapur->latitude ?? old('latitude', -6.108900) }}">
                <input type="hidden" name="longitude" id="longitudeInput" value="{{ $laporan->dapur->longitude ?? old('longitude', 106.145000) }}">
            </div>

            <textarea id="catatan-admin" name="catatan" rows="6" class="input-field" placeholder="Tuliskan catatan verifikasi, temuan, atau tindak lanjut admin...">{{ old('catatan', $laporan->catatan) }}</textarea>
            <button type="submit" class="btn-action btn-action-primary mt-3" id="saveBtn">
                Simpan Verifikasi
            </button>
        </form>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('admin.laporan.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>

<!-- Camera Modal -->
<div id="cameraModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[1000] hidden">
    <div class="bg-[var(--color-background)] rounded-lg p-4 max-w-md w-full">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold">Ambil Foto Selfie</h3>
            <button class="btn-secondary" onclick="closeCamera()">×</button>
        </div>
        <video id="videoStream" class="w-full rounded-lg mb-3" autoplay playsinline></video>
        <div class="flex gap-2">
            <button class="btn-action btn-action-outline" onclick="capturePhoto()">📸 Capture</button>
            <button class="btn-action btn-action-outline" onclick="closeCamera()">Batal</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.snow.css" rel="stylesheet">
<link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill Rich Text Editor
    let editor;
    try {
        editor = new Quill('#catatan-admin', {
            theme: 'snow',
            placeholder: 'Tuliskan catatan verifikasi, temuan, atau tindak lanjut admin...',
            modules: {
                toolbar: [
                    [{ 'header': [false, 1, 2, 3] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link']
                ]
            }
        });
    } catch (e) {
        console.error('Quill init error:', e);
    }

    const statusSelect = document.getElementById('statusSelect');
    const catatanSection = document.getElementById('catatanSection');
    const statusInput = document.getElementById('statusInput');
    const form = document.getElementById('catatanForm');
    const saveBtn = document.getElementById('saveBtn');
    const currentStatusDisplay = document.querySelector('[data-current-status]');
    const labelEl = document.getElementById('catatanLabel');
    const previewImg = document.getElementById('capturePreview');
    const existingSelfie = document.getElementById('existingSelfie');
    const selfieInput = document.getElementById('selfiePhoto');
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    const gpsAccuracyEl = document.getElementById('gpsAccuracy');
    const videoEl = document.getElementById('videoStream');
    const modal = document.getElementById('cameraModal');

    let stream = null;

    // Default koordinat dapur (akurat dari DB)
    const DAPPUR_LAT = parseFloat({{ json_encode($laporan->dapur->latitude ?? -6.108900) }});
    const DAPPUR_LNG = parseFloat({{ json_encode($laporan->dapur->longitude ?? 106.145000) }});

    // GPS accuracy threshold (meter) - di bawah ini dianggap akurat (±10m)
    const GPS_ACCURACY_THRESHOLD = 10;

    // Check if current status is 'selesai' - lock editing
    let isSelesai = false;
    if (currentStatusDisplay && currentStatusDisplay.dataset.currentStatus.toLowerCase() === 'selesai') {
        isSelesai = true;
        statusSelect.disabled = true;
        saveBtn.disabled = true;
        document.getElementById('catatan-admin').readOnly = true;
    }

    // Toggle catatan section visibility based on new status selection
    function updateCatatanVisibility() {
        const status = statusSelect.value;
        const textarea = document.getElementById('catatan-admin');

        catatanSection.style.display = 'block';

        if (status === 'selesai') {
            textarea.readOnly = true;
            saveBtn.disabled = true;
        } else {
            textarea.readOnly = false;
            saveBtn.disabled = false;
        }

        const labels = {
            'diperiksa': 'Catatan Verifikasi Pemeriksaan',
            'temuan': 'Catatan Temuan & Tindak Lanjut',
            'tindak lanjut': 'Catatan Tindak Lanjut',
            'selesai': 'Catatan Selesai'
        };
        labelEl.textContent = labels[status] || 'Catatan Admin';
    }

    // Open camera modal
    window.openCamera = function() {
        modal.classList.remove('hidden');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(function(s) {
                stream = s;
                videoEl.srcObject = s;
            })
            .catch(function(err) {
                alert('Kamera tidak tersedia: ' + err.message);
                closeCamera();
            });
    };

    // Close camera modal
    window.closeCamera = function() {
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
        modal.classList.add('hidden');
    };

    // Capture photo - GPS opsional, paksa dapur jika GPS lemah
    window.capturePhoto = function() {
        // Ambil foto dulu, GPS opsional
        doSavePhoto();

        // GPS di background (non-blocking)
        if (typeof navigator !== 'undefined' && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const accuracy = pos.coords.accuracy;
                    if (accuracy <= GPS_ACCURACY_THRESHOLD) {
                        // GPS akurat - update marker dan input
                        latInput.value = pos.coords.latitude.toFixed(6);
                        lngInput.value = pos.coords.longitude.toFixed(6);
                        gpsAccuracyEl.textContent = 'GPS akurat ±' + Math.round(accuracy) + 'm';
                        gpsAccuracyEl.classList.remove('hidden');
                        showMap();
                    } else {
                        // GPS lemah - paksa pakai dapur (akurat)
                        latInput.value = DAPPUR_LAT.toFixed(6);
                        lngInput.value = DAPPUR_LNG.toFixed(6);
                        gpsAccuracyEl.textContent = 'GPS lemah (±' + Math.round(accuracy) + 'm) - pakai koordinat dapur';
                        gpsAccuracyEl.classList.remove('hidden');
                        showMap();
                    }
                },
                function(err) {
                    // GPS ditolak - paksa pakai dapur
                    latInput.value = DAPPUR_LAT.toFixed(6);
                    lngInput.value = DAPPUR_LNG.toFixed(6);
                    gpsAccuracyEl.classList.add('hidden');
                    showMap();
                },
                { enableHighAccuracy: true }
            );
        }
    };

    function doSavePhoto() {
        // Convert video frame to image
        const canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoEl, 0, 0);

        // Convert to base64 and save
        const dataURL = canvas.toDataURL('image/jpeg', 0.8);
        previewImg.src = dataURL;
        previewImg.classList.remove('hidden');
        selfieInput.value = dataURL;

        closeCamera();
    }

    // Show or refresh Leaflet map at current coords
    function showMap() {
        const mapEl = document.getElementById('locationMap');
        if (!mapEl || !latInput.value || !lngInput.value || typeof L === 'undefined') return;
        mapEl.classList.remove('hidden');
        if (mapEl._leafletInstance) { mapEl._leafletInstance.remove(); }
        const map = L.map(mapEl).setView([parseFloat(latInput.value), parseFloat(lngInput.value)], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([parseFloat(latInput.value), parseFloat(lngInput.value)]).addTo(map)
            .bindPopup('Lokasi Foto (' + latInput.value + ', ' + lngInput.value + ')').openPopup();
        mapEl._leafletInstance = map;
    }

    // Set initial visibility and label
    updateCatatanVisibility();

    if (isSelesai) {
        document.getElementById('catatan-admin').readOnly = true;
    }

    // Auto-init map on page load with DAPUR coordinates (akurat)
    showMap();

    // Optional: update map dengan GPS aktual jika user izinkan dan akurat
    if (!isSelesai && typeof navigator !== 'undefined' && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const accuracy = pos.coords.accuracy;
                if (accuracy <= GPS_ACCURACY_THRESHOLD) {
                    latInput.value = pos.coords.latitude.toFixed(6);
                    lngInput.value = pos.coords.longitude.toFixed(6);
                    gpsAccuracyEl.textContent = 'GPS akurat ±' + Math.round(accuracy) + 'm';
                    gpsAccuracyEl.classList.remove('hidden');
                    showMap();
                } else {
                    // GPS lemah - tetap pakai dapur
                    latInput.value = DAPPUR_LAT.toFixed(6);
                    lngInput.value = DAPPUR_LNG.toFixed(6);
                    gpsAccuracyEl.textContent = 'GPS lemah (±' + Math.round(accuracy) + 'm) - pakai dapur';
                    gpsAccuracyEl.classList.remove('hidden');
                }
            },
            function() { /* GPS denied - pakai dapur */ },
            { enableHighAccuracy: true }
        );
    }

    // Listen for new status changes
    statusSelect.addEventListener('change', function() {
        const status = this.value;
        statusInput.value = status;
        updateCatatanVisibility();
    });

    // Sync Quill content to textarea before form submit
    form.addEventListener('submit', function(e) {
        if (isSelesai) {
            e.preventDefault();
            return;
        }
        if (editor) {
            document.getElementById('catatan-admin').value = editor.root.innerHTML;
        }
    });
});
</script>
@endpush