@extends('pengawas.layouts.app', ['pageTitle' => 'Buat Laporan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Buat Laporan Inspeksi</h1>
        <div class="muted">Isi formulir pemeriksaan dapur</div>
    </div>
    <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">← Kembali</a>
</div>

<div class="panel" style="max-width: 720px;">
    <form method="POST" action="{{ route('pengawas.laporan.store') }}" id="laporanForm" enctype="multipart/form-data">
        @csrf

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Pilih Dapur</label>
            <select name="dapur_id" class="input-field">
                <option value="{{ auth()->user()->dapur->id ?? '' }}">{{ auth()->user()->dapur->nama ?? 'Dapur Anda' }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Kategori Pemeriksaan</label>
            <select name="kategori" class="input-field">
                <option>Kebersihan dan sanitasi</option>
                <option>Penyimpanan bahan</option>
                <option>Proses pengolahan</option>
                <option>Distribusi makanan</option>
                <option>Peralatan dan fasilitas</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Hasil Pemeriksaan</label>
            <select name="status" class="input-field" id="hasilSelect">
                <option value="diperiksa">Sesuai standar</option>
                <option value="temuan">Perlu perbaikan</option>
                <option value="temuan">Temuan kritis</option>
            </select>
        </div>

        <!-- Foto Selfie + GPS Capture -->
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Foto Selfie di Lokasi</label>
            <button type="button" class="btn-action btn-action-outline mt-2 w-full" onclick="openCamera()">
                📸 Ambil Foto Selfie (dengan GPS)
            </button>
            <img id="capturePreview" src="" alt="Preview foto" class="mt-2 max-w-xs rounded-lg hidden border">
            <input type="hidden" name="selfie_photo" id="selfiePhoto">
            <input type="hidden" name="latitude" id="latitudeInput">
            <input type="hidden" name="longitude" id="longitudeInput">
            <div id="gpsAccuracy" class="text-xs text-[var(--color-text-muted)] mt-1 hidden"></div>
            <p class="text-xs text-[var(--color-text-muted)] mt-1">Foto akan dilengkapi koordinat GPS akurat (±10m)</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Catatan Pengawas</label>
            <textarea name="catatan" class="input-field" rows="4" placeholder="Tuliskan kondisi yang ditemukan…"></textarea>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" id="submitBtn">Kirim Laporan</button>
        </div>
    </form>
</div>

<!-- Camera Modal -->
<div id="cameraModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[1000] hidden">
    <div class="bg-[var(--color-background)] rounded-lg p-4 max-w-md w-full">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold">Foto Selfie</h3>
            <button class="btn-secondary" onclick="closeCamera()">×</button>
        </div>
        <video id="videoStream" class="w-full rounded-lg mb-3" autoplay playsinline></video>
        <div class="flex gap-2 justify-center">
            <button class="btn-action btn-action-outline" onclick="capturePhoto()">📸 Capture</button>
            <button class="btn-action btn-action-outline" onclick="closeCamera()">Batal</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');
    const selfieInput = document.getElementById('selfiePhoto');
    const previewImg = document.getElementById('capturePreview');
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    const gpsAccuracyEl = document.getElementById('gpsAccuracy');
    const videoEl = document.getElementById('videoStream');
    const modal = document.getElementById('cameraModal');

    const GPS_ACCURACY_THRESHOLD = 10;
    let stream = null;

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

    // Capture photo + GPS (GPS wajib akurat)
    let retryCount = 0;
    const maxRetries = 3;

    window.capturePhoto = function() {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const accuracy = pos.coords.accuracy;
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);

                if (accuracy <= GPS_ACCURACY_THRESHOLD) {
                    // GPS akurat
                    latInput.value = lat;
                    lngInput.value = lng;
                    gpsAccuracyEl.textContent = 'GPS akurat ±' + Math.round(accuracy) + 'm';
                    gpsAccuracyEl.classList.remove('hidden');
                    doSavePhoto();
                } else {
                    // GPS lemah - minta coba lagi
                    alert('GPS kurang akurat (±' + Math.round(accuracy) + 'm). Pindah ke area terbuka lalu coba lagi.');
                    capturePhoto();
                }
            },
            function(err) {
                retryCount++;
                if (retryCount <= maxRetries) {
                    alert('GPS gagal. Klik OK untuk coba lagi (izinkan akses lokasi): ' + (retryCount + 1) + '/' + (maxRetries + 1));
                    capturePhoto();
                } else {
                    alert('GPS tidak dapat didapatkan. Tutup kamera, coba di area terbuka, lalu ambil foto lagi.');
                    closeCamera();
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    function doSavePhoto() {
        if (!stream || !videoEl.videoWidth) {
            alert('Kamera belum siap.');
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoEl, 0, 0);

        const dataURL = canvas.toDataURL('image/jpeg', 0.8);
        previewImg.src = dataURL;
        previewImg.classList.remove('hidden');
        selfieInput.value = dataURL;

        closeCamera();
        retryCount = 0; // reset retry count
    }

    // Form submit validation - wajib ada foto + GPS
    form.addEventListener('submit', function(e) {
        if (!selfieInput.value) {
            e.preventDefault();
            alert('Ambil foto selfie dulu!');
            return false;
        }
        if (!latInput.value || !lngInput.value) {
            e.preventDefault();
            alert('GPS belum tersedia. Ambil foto lagi di area terbuka.');
            return false;
        }
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
    });
});
</script>
@endpush
