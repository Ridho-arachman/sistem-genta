@extends('pengawas.layouts.app', ['pageTitle' => 'Pengaturan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Pengaturan Sistem</h1>
        <div class="muted">Konfigurasi aplikasi</div>
    </div>
</div>

<div class="panel" style="max-width: 600px;">
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama Aplikasi</label>
            <input type="text" class="input-field" value="Genta Pengawasan Dapur MBG" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Instansi</label>
            <input type="text" class="input-field" value="Genta Provinsi Banten" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Zona Waktu</label>
            <select class="input-field">
                <option selected>WIB — Asia/Jakarta</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Notifikasi</label>
            <select class="input-field">
                <option selected>Aktif</option>
                <option>Nonaktif</option>
            </select>
        </div>
        <button type="button" class="btn-primary" onclick="toast('Pengaturan hanya contoh, belum tersimpan ke server.')">Simpan Pengaturan</button>
    </form>
</div>
@endsection