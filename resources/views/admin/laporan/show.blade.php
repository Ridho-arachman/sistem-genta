@extends('admin.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Detail Laporan</h1>
        <div class="muted">Verifikasi dan tindak lan-jut laporan penemuan</div>
    </div>
</div>

<div class="panel" id="laporanDetail" data-current-status="{{ $laporan->status }}" data-has-coords="{{ $laporan->latitude && $laporan->longitude ? '1' : '0' }}" data-default-lat="{{ $laporan->latitude ?? '' }}" data-default-lng="{{ $laporan->longitude ?? '' }}">
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

    <!-- Foto Selfie (READ-ONLY, diambil pengawas di HP) -->
    <div class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Foto Selfie Pengawas</p>
        @if ($laporan->selfie_photo)
            <img src="{{ asset('storage/' . $laporan->selfie_photo) }}" alt="Selfie pengawas" class="max-w-xs max-h-48 rounded-lg border object-cover">
            <div class="text-xs text-[var(--color-text-muted)] mt-1">Koordinat: {{ $laporan->latitude }}, {{ $laporan->longitude }}</div>
        @else
            <p class="text-sm text-[var(--color-text-muted)] italic">Foto belum diambil pengawas.</p>
        @endif
    </div>

    <!-- Peta Statis (READ-ONLY, marker di lokasi foto) -->
    @if ($laporan->latitude && $laporan->longitude)
        <div class="mt-6">
            <p class="text-sm text-[var(--color-text-muted)] mb-2">Lokasi Foto ({{ $laporan->latitude }}, {{ $laporan->longitude }})</p>
            <div id="locationMap" class="w-full rounded-lg border" style="height: 256px;"></div>
        </div>
    @endif

    <!-- Catatan Admin (verification form) -->
    <div id="catatanSection" class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2" id="catatanLabel">Catatan Verifikasi</p>
        <form method="POST" action="{{ route('admin.laporan.update', $laporan->id) }}" id="catatanForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" id="statusInput" value="{{ old('status', $laporan->status) }}">
            <input type="hidden" name="latitude" id="latitudeInput" value="{{ $laporan->latitude ?? -6.108900 }}">
            <input type="hidden" name="longitude" id="longitudeInput" value="{{ $laporan->longitude ?? 106.145000 }}">
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

    // Set initial visibility and label
    updateCatatanVisibility();

    // Render static map (from saved GPS coords)
    @if ($laporan->latitude && $laporan->longitude)
        const lat = {!! json_encode((float) ($laporan->latitude ?? -6.108900)) !!};
        const lng = {!! json_encode((float) ($laporan->longitude ?? 106.145000)) !!};
        const mapEl = document.getElementById('locationMap');
        if (mapEl && typeof L !== 'undefined') {
            const map = L.map(mapEl).setView([parseFloat(lat), parseFloat(lng)], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map)
                .bindPopup('Lokasi Foto (' + lat + ', ' + lng + ')').openPopup();
        }
    @endif

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
