@extends('admin.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Detail Laporan</h1>
        <div class="muted">Verifikasi dan tindak lan-jut laporan penemuan</div>
    </div>
</div>

<div class="panel" id="laporanDetail" data-current-status="{{ $laporan->status }}">
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
                <span class="badge badge-{{ match($laporan->status) { 'diperiksa' => 'success', 'temuan' => 'danger', 'tindak lanjut' => 'warning', 'selesai' => 'success', default => 'secondary' } }}">{{ ucfirst($laporan->status) }}</span>
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

    <!-- Foto Laporan (READ-ONLY, sudah dilengkapi peta + GPS) -->
    <div class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Foto Pemeriksaan (dengan Peta & GPS)</p>
        @php
            $photo = $laporan->selfie_photo;
            $photoUrl = null;
            if ($photo) {
                if (str_starts_with($photo, 'data:')) {
                    $photoUrl = $photo; // base64 data URL
                } elseif (str_starts_with($photo, 'storage/')) {
                    $photoUrl = asset($photo);
                } else {
                    $photoUrl = asset('storage/' . $photo); // path file di storage
                }
            }
        @endphp
        @if ($photoUrl)
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2 cursor-pointer" onclick="openLaporanPhotoPreview('{{ $photoUrl }}')">
                <img src="{{ $photoUrl }}" alt="Foto laporan" class="w-full h-96 object-cover rounded-md">
                <p class="text-[10px] text-[var(--color-text-muted)] mt-1 text-center">Klik untuk memperbesar</p>
            </div>
        @else
            <p class="text-sm text-[var(--color-text-muted)] italic">Foto belum diambil pengawas.</p>
        @endif
    </div>

    <!-- Catatan Admin (verification form) -->
    <div id="catatanSection" class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2" id="catatanLabel">Catatan Verifikasi</p>
        <form method="POST" action="{{ route('admin.laporan.update', $laporan->id) }}" id="catatanForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" id="statusInput" value="{{ old('status', $laporan->status) }}">
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

    <!-- Foto Preview Modal (full screen) -->
    <div id="laporanPhotoPreviewModal" class="fixed inset-0 bg-black flex items-center justify-center z-[1000] hidden" onclick="closeLaporanPhotoPreview()">
        <button type="button" onclick="event.stopPropagation(); closeLaporanPhotoPreview()" class="absolute top-4 right-4 bg-white/10 text-white p-3 rounded-lg cursor-pointer shadow-lg z-20 backdrop-blur-sm hover:bg-white/20 transition-colors" aria-label="Tutup preview">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="sr-only">Tutup preview</span>
        </button>
        <div class="relative max-w-full max-h-full flex items-center justify-center">
            <img id="laporanPreviewImage" src="" alt="Preview foto laporan" class="w-full h-full max-w-full max-h-full object-contain bg-black">
        </div>
    </div>

    <script>
        window.openLaporanPhotoPreview = function(src) {
            document.getElementById('laporanPreviewImage').src = src;
            document.getElementById('laporanPhotoPreviewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };
        window.closeLaporanPhotoPreview = function() {
            document.getElementById('laporanPhotoPreviewModal').classList.add('hidden');
            document.body.style.overflow = '';
        };
    </script>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.min.js"></script>
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