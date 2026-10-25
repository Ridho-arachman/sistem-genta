@extends('pengawas.layouts.app', ['pageTitle' => 'Absensi Check-in'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Absensi Check-in</h2>
            <p class="text-[var(--color-text-muted)] mt-1">Ambil foto selfie dan pastikan GPS aktif untuk validasi lokasi</p>
        </div>

        <form method="POST" action="{{ route('pengawas.absensi.store') }}" enctype="multipart/form-data" class="space-y-6" id="absensiForm">
            @csrf

            <div>
                <label for="dapur_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Dapur <span class="text-red-500">*</span></label>
                <select id="dapur_id" name="dapur_id" required class="input-field" onchange="updateGeofenceInfo(this.value)">
                    <option value="">Pilih dapur</option>
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}" data-lat="{{ $dapur->latitude }}" data-lng="{{ $dapur->longitude }}" data-radius="{{ $dapur->geofence_radius ?? 100 }}">{{ $dapur->nama }}</option>
                    @endforeach
                </select>
                @error('dapur_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Geofence Info --}}
            <div id="geofenceInfo" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2 text-blue-800 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-medium">Info Lokasi Dapur</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm text-blue-700">
                    <div>Latitude: <span id="infoLat" class="font-mono">-</span></div>
                    <div>Longitude: <span id="infoLng" class="font-mono">-</span></div>
                    <div>Radius: <span id="infoRadius" class="font-mono">-</span> meter</div>
                    <div>Status GPS: <span id="gpsStatus" class="font-memo">Memeriksa...</span></div>
                </div>
            </div>

            {{-- GPS Location (Hidden inputs filled by JS) --}}
            <input type="hidden" name="lat" id="latInput" required>
            <input type="hidden" name="lng" id="lngInput" required>

            <div>
                <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Foto Selfie <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[var(--color-border)] rounded-lg p-6 text-center hover:border-[var(--color-primary)] transition-colors">
                    <input type="file" name="selfie_path" id="selfieInput" accept="image/*" capture="environment" required class="hidden" onchange="previewImage(this)">
                    <div id="previewContainer" class="hidden">
                        <img id="previewImage" src="#" alt="Preview" class="max-h-48 mx-auto rounded-lg mb-2">
                        <button type="button" onclick="removeImage()" class="text-sm text-red-600 hover:underline">Hapus foto</button>
                    </div>
                    <div id="uploadPlaceholder">
                        <svg class="w-12 h-12 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <p class="mt-2 text-[var(--color-text-muted)]">Klik atau tarik foto selfie di sini</p>
                        <p class="text-xs text-[var(--color-text-muted)]">Gunakan kamera depan (selfie)</p>
                    </div>
                </div>
                @error('selfie_path')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                <select id="status" name="status" required class="input-field">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="cuti">Cuti</option>
                    <option value="tidak_hadir">Tidak Hadir</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)]">
                <h4 class="font-medium text-[var(--color-secondary)] mb-2">Informasi Penting:</h4>
                <ul class="text-sm text-[var(--color-text-muted)] space-y-1">
                    <li class="flex items-start gap-2"><svg class="w-4 h-4 mt-0.5 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Foto selfie wajib diambil saat check-in (menggunakan kamera depan)</li>
                    <li class="flex items-start gap-2"><svg class="w-4 h-4 mt-0.5 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> GPS harus aktif dan Anda harus berada dalam radius dapur yang dipilih</li>
                    <li class="flex items-start gap-2"><svg class="w-4 h-4 mt-0.5 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Data lokasi akan diverifikasi otomatis dengan geofence dapur</li>
                    <li class="flex items-start gap-2"><svg class="w-4 h-4 mt-0.5 text-[var(--color-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Pastikan izin kamera & lokasi diizinkan di browser/HP</li>
                </ul>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" id="submitBtn" class="btn-primary flex-1 py-3" disabled>
                    <span id="btnText">Mengambil lokasi GPS...</span>
                    <svg id="btnSpinner" class="w-5 h-5 mr-2 inline animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
                <a href="{{ route('pengawas.absensi.index') }}" class="btn-secondary flex-1 text-center py-3">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    let watchId = null;
    let currentPosition = null;

    // Request GPS on page load
    document.addEventListener('DOMContentLoaded', function() {
        getCurrentLocation();
    });

    function getCurrentLocation() {
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const submitBtn = document.getElementById('submitBtn');

        if (!navigator.geolocation) {
            btnText.textContent = 'GPS tidak didukung browser ini';
            submitBtn.disabled = true;
            return;
        }

        btnText.textContent = 'Mengambil lokasi GPS...';
        btnSpinner.classList.remove('hidden');
        submitBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentPosition = position;
                document.getElementById('latInput').value = position.coords.latitude;
                document.getElementById('lngInput').value = position.coords.longitude;
                
                btnText.textContent = 'Lokasi ditemukan - Siap absensi';
                btnSpinner.classList.add('hidden');
                submitBtn.disabled = false;
                
                // Update GPS status in geofence info
                document.getElementById('gpsStatus').textContent = 'Ditemukan ✓';
                document.getElementById('gpsStatus').className = 'font-mono text-green-600';
            },
            function(error) {
                let msg = 'Gagal mendapatkan lokasi: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED: msg += 'Izin lokasi ditolak'; break;
                    case error.POSITION_UNAVAILABLE: msg += 'Lokasi tidak tersedia'; break;
                    case error.TIMEOUT: msg += 'Timeout'; break;
                    default: msg += 'Error tidak diketahui';
                }
                btnText.textContent = msg;
                btnSpinner.classList.add('hidden');
                submitBtn.disabled = true;
                
                document.getElementById('gpsStatus').textContent = 'Error: ' + msg;
                document.getElementById('gpsStatus').className = 'font-mono text-red-600';
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    }

    function updateGeofenceInfo(dapurId) {
        const select = document.getElementById('dapur_id');
        const option = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('geofenceInfo');
        
        if (dapurId && option.dataset.lat) {
            document.getElementById('infoLat').textContent = option.dataset.lat;
            document.getElementById('infoLng').textContent = option.dataset.lng;
            document.getElementById('infoRadius').textContent = option.dataset.radius;
            infoDiv.classList.remove('hidden');
            
            // Check if current position is within geofence
            if (currentPosition) {
                checkGeofence(
                    currentPosition.coords.latitude,
                    currentPosition.coords.longitude,
                    parseFloat(option.dataset.lat),
                    parseFloat(option.dataset.lng),
                    parseInt(option.dataset.radius)
                );
            }
        } else {
            infoDiv.classList.add('hidden');
        }
    }

    function checkGeofence(lat1, lng1, lat2, lng2, radius) {
        const R = 6371000; // Earth radius in meters
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        
        const statusEl = document.getElementById('gpsStatus');
        if (distance <= radius) {
            statusEl.textContent = `Dalam radius (${Math.round(distance)}m / ${radius}m) ✓`;
            statusEl.className = 'font-mono text-green-600';
        } else {
            statusEl.textContent = `DI LUAR RADIUS (${Math.round(distance)}m / ${radius}m) ✗`;
            statusEl.className = 'font-mono text-red-600';
        }
    }

    function previewImage(input) {
        const previewContainer = document.getElementById('previewContainer');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const previewImage = document.getElementById('previewImage');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.getElementById('selfieInput');
        const previewContainer = document.getElementById('previewContainer');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        
        input.value = '';
        previewContainer.classList.add('hidden');
        uploadPlaceholder.classList.remove('hidden');
    }

    // Allow clicking on the upload area to trigger file input
    document.querySelector('.border-dashed').addEventListener('click', function(e) {
        if (e.target === this || e.target.closest('#uploadPlaceholder')) {
            document.getElementById('selfieInput').click();
        }
    });

    // Drag and drop support
    const dropZone = document.querySelector('.border-dashed');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-[var(--color-primary)]', 'bg-[var(--color-primary)]/5');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-[var(--color-primary)]', 'bg-[var(--color-primary)]/5');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('selfieInput').files = files;
            previewImage(document.getElementById('selfieInput'));
        }
    }
</script>
@endsection