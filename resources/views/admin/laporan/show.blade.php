@extends('admin.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('page_content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
    <div>
        <div class="text-[var(--color-primary)] text-[11px] font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
        <h1 class="text-[28px] font-extrabold tracking-[-0.5px] mt-1 mb-1">Detail Laporan</h1>
        <div class="text-[var(--color-text-muted)] text-sm">Verifikasi dan tindak lan-jut laporan penemuan</div>
    </div>
</div>

<div class="card card-bordered" id="laporanDetail" x-data="laporanDetail()" x-effect="document.body.style.overflow = photoPreview ? 'hidden' : ''">
    <div class="card-body">
        <div class="card-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
            <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </span>
            <h2 class="text-sm font-bold text-[var(--color-secondary)]">Informasi Laporan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)]">Tanggal Laporan</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->created_at->format('d M Y') }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)]">Lokasi / Dapur</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->dapur->nama ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->createdBy->name ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)]">Alamat Lokasi</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->address ?? '-' }}</p>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)]">Status Saat Ini</p>
                    <span class="badge badge-{{ match($laporan->status) { 'diperiksa' => 'success', 'temuan' => 'error', 'tindak lanjut' => 'warning', 'selesai' => 'success', default => 'secondary' } }}">{{ ucfirst($laporan->status) }}</span>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)] mb-2">Koordinat GPS</p>
                    <p class="font-mono text-sm font-semibold text-[var(--color-secondary)]">{{ $laporan->latitude }}, {{ $laporan->longitude }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)] mb-2">Status Baru</p>
                    <div class="relative">
                        <select id="statusSelect" class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none">
                            <option value="diperiksa" {{ old('status', $laporan->status) === 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                            <option value="temuan" {{ old('status', $laporan->status) === 'temuan' ? 'selected' : '' }}>Temuan</option>
                            <option value="tindak lanjut" {{ old('status', $laporan->status) === 'tindak lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                            <option value="selesai" {{ old('status', $laporan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>

    <div class="mt-6 border-t border-[var(--color-border)] pt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Deskripsi Temuan</p>
        <div class="p-3 bg-[var(--color-surface)] rounded-lg text-sm text-[var(--color-text)]">
            {{ old('catatan', $laporan->catatan) ?: 'Belum ada catatan temuan dari pengawas.' }}
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
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2 cursor-pointer" @click="openPhoto('{{ $photoUrl }}')">
                <img src="{{ $photoUrl }}" alt="Foto laporan" class="w-full max-h-[420px] object-contain rounded-md">
                <p class="text-[10px] text-[var(--color-text-muted)] mt-1 text-center">Klik untuk memperbesar</p>
            </div>
        @else
            <p class="text-sm text-[var(--color-text-muted)] italic">Foto belum diambil pengawas.</p>
        @endif
    </div>

    <!-- Catatan Admin (verification form) -->
    <div id="catatanSection" class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2" id="catatanLabel" x-text="catatanLabel">Catatan Verifikasi</p>
        <form method="POST" action="{{ route('admin.laporan.update', $laporan->id) }}" id="catatanForm" @submit="if (locked) { $event.preventDefault(); return; } if (editor) { $refs.catatan.value = editor.root.innerHTML; }">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" id="statusInput" :value="status">
            <textarea id="catatan-admin" name="catatan" rows="6" x-ref="catatan" :readonly="locked || isSelesai" class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Tuliskan catatan verifikasi, temuan, atau tindak lanjut admin...">{{ old('catatan', $laporan->catatan) }}</textarea>
            <button type="submit" class="btn btn-primary mt-3" id="saveBtn" :disabled="locked || isSelesai">
                Simpan Verifikasi
            </button>
        </form>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Foto Preview Modal (full screen) -->
    <div id="laporanPhotoPreviewModal" class="fixed inset-0 bg-black flex items-center justify-center z-[1000]" x-show="photoPreview" x-cloak @click.self="photoPreview = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <button type="button" @click="photoPreview = false" class="absolute top-4 right-4 bg-white/10 text-white p-3 rounded-lg cursor-pointer shadow-lg z-20 backdrop-blur-sm hover:bg-white/20 transition-colors" aria-label="Tutup preview">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="sr-only">Tutup preview</span>
        </button>
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <img id="laporanPreviewImage" :src="photoSrc" alt="Preview foto laporan" class="max-w-full max-h-full object-contain">
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.4/dist/quill.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanDetail', () => ({
        photoPreview: false,
        photoSrc: '',
        status: @js(old('status', $laporan->status)),
        currentStatus: @js($laporan->status),
        editor: null,
        labels: {
            'diperiksa': 'Catatan Verifikasi Pemeriksaan',
            'temuan': 'Catatan Temuan & Tindak Lanjut',
            'tindak lanjut': 'Catatan Tindak Lanjut',
            'selesai': 'Catatan Selesai'
        },

        get isSelesai() {
            return this.status === 'selesai';
        },

        get locked() {
            return (this.currentStatus || '').toLowerCase() === 'selesai';
        },

        get catatanLabel() {
            return this.labels[this.status] || 'Catatan Admin';
        },

        openPhoto(src) {
            this.photoSrc = src;
            this.photoPreview = true;
        },

        init() {
            try {
                this.editor = new Quill('#catatan-admin', {
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
        }
    }));
});
</script>
@endpush
