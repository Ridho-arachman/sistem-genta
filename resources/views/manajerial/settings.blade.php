@extends('manajerial.layouts.app', ['pageTitle' => 'Pengaturan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Manajerial Panel</div>
        <h1 class="text-2xl font-bold">Pengaturan Sistem</h1>
        <div class="muted text-[var(--color-text-muted)] mt-1">Konfigurasi sistem (Read Only)</div>
    </div>
</div>

<div class="panel">
    <h2 class="text-lg font-semibold mb-4 p-4 border-b border-[var(--color-border)]">Konfigurasi Sistem</h2>
    <form class="space-y-4 report-form max-w-md p-4">
        <label class="block">
            <span class="text-sm font-medium text-[var(--color-secondary)] mb-1 block">Nama Aplikasi</span>
            <input value="Genta Pengawasan Dapur MBG" class="input-field" readonly>
        </label>
        <label class="block">
            <span class="text-sm font-medium text-[var(--color-secondary)] mb-1 block">Instansi</span>
            <input value="Genta Provinsi Banten" class="input-field" readonly>
        </label>
        <label class="block">
            <span class="text-sm font-medium text-[var(--color-secondary)] mb-1 block">Zona Waktu</span>
            <select class="input-field">
                <option>WIB — Asia/Jakarta</option>
            </select>
        </label>
        <label class="block">
            <span class="text-sm font-medium text-[var(--color-secondary)] mb-1 block">Versi Sistem</span>
            <input value="2.0.0 (MVP to Final)" class="input-field" readonly>
        </label>
        <div class="p-3.5 rounded-xl text-sm leading-relaxed text-[var(--color-text-muted)] bg-[var(--color-surface)] border border-[var(--color-border)]">
            <b>ⓘ Mode Read Only</b><br>Panel manajerial bersifat read-only. Pengaturan sistem hanya dapat diubah melalui Admin Panel.
        </div>
    </form>
</div>
@endsection