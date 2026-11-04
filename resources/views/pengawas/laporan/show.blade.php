@extends('pengawas.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Detail Laporan</h1>
        <div class="muted">Informasi hasil inspeksi dapur (hanya dibaca)</div>
    </div>
</div>

<div class="panel">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Tanggal Laporan</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->created_at->format('d M Y') }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Lokasi / Dapur</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->dapur->nama ?? 'Dapur' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $laporan->createdBy?->name ?? '-' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Alamat</p>
                <p class="text-sm text-[var(--color-text)]">{{ $laporan->address ?? '-' }}</p>
            </div>
        </div>
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Status</p>
                @include('pengawas.components.status-badge', ['status' => $laporan->status])
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">GPS</p>
                <p class="text-sm text-[var(--color-text)]">{{ $laporan->latitude }} , {{ $laporan->longitude }}</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Catatan</p>
                <p class="text-sm text-[var(--color-text)] whitespace-pre-wrap">{{ $laporan->catatan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Hasil Pemeriksaan per Indikator (dari laporan_hasil) -->
    <div class="mt-6 border-t border-[var(--color-border)] pt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Hasil Pemeriksaan per Indikator</p>
        @php
            $hasilLabels = [
                'diperiksa' => 'Sesuai standar',
                'temuan_sedang' => 'Perlu perbaikan',
                'temuan_kritis' => 'Temuan kritis',
            ];
            $hasilBadge = [
                'diperiksa' => 'success',
                'temuan_sedang' => 'warning',
                'temuan_kritis' => 'danger',
            ];
        @endphp
        @if ($laporan->hasil->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($laporan->hasil as $hasil)
                    <div class="border border-[var(--color-border)] rounded-lg p-3 bg-[var(--color-surface)]">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-medium text-[var(--color-text)]">{{ $hasil->indikator }}</span>
                            <span class="badge badge-{{ $hasilBadge[$hasil->hasil] ?? 'info' }} text-[9px] uppercase">{{ $hasilLabels[$hasil->hasil] ?? $hasil->hasil }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-[var(--color-text-muted)] italic">Belum ada data hasil pemeriksaan.</p>
        @endif
    </div>

    <!-- Foto Laporan -->
    @include('pengawas.components.foto-preview', ['photoUrl' => $laporan->selfie_photo])

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">Kembali</a>
    </div>

    <!-- Foto Preview Modal (full screen) -->
    <div id="laporanPhotoPreviewModal" class="fixed inset-0 bg-black flex items-center justify-center z-[1000] hidden" data-action="close-photo-preview">
        <button type="button" data-action="close-photo-preview" class="absolute top-4 right-4 bg-white/10 text-white p-3 rounded-lg cursor-pointer shadow-lg z-20 backdrop-blur-sm hover:bg-white/20 transition-colors" aria-label="Tutup preview">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="sr-only">Tutup preview</span>
        </button>
        <div class="relative max-w-full max-h-full flex items-center justify-center">
            <img id="laporanPreviewImage" src="" alt="Preview foto laporan" class="w-full h-full max-w-full max-h-full object-contain bg-black">
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('laporanPhotoPreviewModal');
        const previewImage = document.getElementById('laporanPreviewImage');

        // Open photo preview
        document.querySelectorAll('[data-action="open-photo-preview"]').forEach(el => {
            el.addEventListener('click', function() {
                const src = this.dataset.photoUrl;
                previewImage.src = src;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close photo preview
        function closeLaporanPhotoPreview() {
            document.getElementById('laporanPhotoPreviewModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on modal backdrop click
        document.getElementById('laporanPhotoPreviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLaporanPhotoPreview();
            }
        });

        // Close button
        document.querySelectorAll('[data-action="close-photo-preview"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeLaporanPhotoPreview();
            });
        });
    });
</script>
@endsection