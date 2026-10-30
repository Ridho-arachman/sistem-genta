@extends('admin.layouts.app', ['pageTitle' => 'Detail Laporan'])

@section('content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Detail Laporan</h1>
        <div class="muted">Verifikasi dan tindak lan-jut laporan penemuan</div>
    </div>
</div>

<div class="card p-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Tanggal Laporan</p>
                <p class="font-semibold text-[var(--color-secondary)]">30 Okt 2026</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Lokasi / Dapur</p>
                <p class="font-semibold text-[var(--color-secondary)]">Serang 01</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                <p class="font-semibold text-[var(--color-secondary)]">Siti Rahma</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Kategori Pemeriksaan</p>
                <p class="font-semibold text-[var(--color-secondary)]">Kebersihan dan sanitasi</p>
            </div>
        </div>
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Status Saat Ini</p>
                <span class="badge badge-danger">Temuan</span>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)] mb-2">Status Baru</p>
                <form method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <select onchange="this.form.submit()" class="input-field">
                        <option value="diperiksa" {{ 'diperiksa' === 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                        <option value="temuan" {{ 'temuan' === 'temuan' ? 'selected' : '' }}>Temuan</option>
                        <option value="tindak lanjut" {{ 'tindak lanjut' === 'temuan' ? 'selected' : '' }}>Tindak Lanjut</option>
                        <option value="selesai" {{ 'selesai' === 'temuan' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Deskripsi Temuan</p>
        <div class="p-3 bg-[var(--color-surface)] rounded-lg">
            <p class="text-sm">Temuan kritikal pada area pendingin gudang: suhu tidak konsisten, ada titik panas pada pos POS-02 yang menyebabkan penurunan kualitas produk.</p>
        </div>
    </div>

    <div class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Catatan Pengawas</p>
        <div class="p-3 bg-[var(--color-surface)] rounded-lg">
            <p class="text-sm italic">Penggunaan termometer digital perlu diperiksa ulang. Rekomendasi pelatihan ulang untuk petugas gudang.</p>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('admin.laporan.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Demo status options - in real app, this would be from controller
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanDetail', () => ({
        id: {{ $id }},
        status: 'temuan'
    }))
});
</script>
@endpush