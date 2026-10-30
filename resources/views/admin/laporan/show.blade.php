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
                <p class="font-semibold text-[var(--color-secondary)]" x-text="reportDate">30 Okt 2026</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Lokasi / Dapur</p>
                <p class="font-semibold text-[var(--color-secondary)]" x-text="reportLocation">Serang 01</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                <p class="font-semibold text-[var(--color-secondary)]" x-text="reportPengawas">Siti Rahma</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Kategori Pemeriksaan</p>
                <p class="font-semibold text-[var(--color-secondary)]" x-text="reportCategory">Kebersihan dan sanitasi</p>
            </div>
        </div>
        <div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)]">Status Saat Ini</p>
                <span class="badge badge-danger" x-text="currentStatus">Temuan</span>
            </div>
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text-muted)] mb-2">Status Baru</p>
                <form method="POST" action="{{ route('admin.laporan.update', 1) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <select name="status" onchange="this.form.submit()" class="input-field">
                        <option value="diperiksa">Diperiksa</option>
                        <option value="temuan" selected>Temuan</option>
                        <option value="tindak lanjut">Tindak Lanjut</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-[var(--color-border)] pt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Deskripsi Temuan</p>
        <div class="p-3 bg-[var(--color-surface)] rounded-lg text-sm text-[var(--color-text)]">
            Temuan kritikal pada area pendingin gudang: suhu tidak konsisten, ada titik panas pada pos POS-02 yang menyebabkan penurunan kualitas produk.
        </div>
    </div>

    <div class="mt-6">
        <p class="text-sm text-[var(--color-text-muted)] mb-2">Catatan Admin (Temuan & Tindak Lanjut)</p>
        <form method="POST" action="{{ route('admin.laporan.update', 1) }}">
            @csrf
            @method('PUT')
            <textarea id="catatan-admin" name="catatan" rows="6" class="input-field" placeholder="Tuliskan temuan, rekomendasi, atau tindak lanjut admin..."></textarea>
            <button type="submit" class="btn-action btn-action-primary mt-3">
                Simpan Verifikasi
            </button>
        </form>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('admin.laporan.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi TinyMCE Rich Text Editor
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#catatan-admin',
            plugins: 'lists link image table code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image table | code',
            height: 200,
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('TinyMCE initialized for catatan-admin');
                });
            }
        });
    }
});
</script>
@endpush