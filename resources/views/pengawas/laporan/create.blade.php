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
    <form class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Pilih Dapur</label>
            <select class="input-field">
                <option>{{ auth()->user()->dapur->nama ?? 'Dapur Anda' }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Kategori Pemeriksaan</label>
            <select class="input-field">
                <option>Kebersihan dan sanitasi</option>
                <option>Penyimpanan bahan</option>
                <option>Proses pengolahan</option>
                <option>Distribusi makanan</option>
                <option>Peralatan dan fasilitas</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Hasil Pemeriksaan</label>
            <select class="input-field">
                <option>Sesuai standar</option>
                <option>Perlu perbaikan</option>
                <option>Temuan kritis</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Catatan Pengawas</label>
            <textarea class="input-field" rows="4" placeholder="Tuliskan kondisi yang ditemukan…"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Foto Dokumentasi</label>
            <input type="file" class="input-field" accept="image/*">
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('pengawas.laporan.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Kirim Laporan</button>
        </div>
    </form>
</div>
@endsection