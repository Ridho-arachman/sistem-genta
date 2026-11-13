@extends('admin.layouts.app', ['pageTitle' => 'Tambah Dapur'])

@section('page_content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <div class="text-[var(--color-primary)] text-[11px] font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
            <h1 class="text-[28px] font-extrabold tracking-[-0.5px] mt-1 mb-1">Tambah Dapur Baru</h1>
            <div class="text-[var(--color-text-muted)] text-sm">Isi data dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.dapurs.store') }}" class="space-y-6" x-data="dapurForm(@json($kecamatanMapping), @js(old('kabupaten_kota')), @js(old('kecamatan')))">
        @csrf

        {{-- Row 1: Nama Dapur --}}
        <div class="card card-bordered">
            <div class="card-body">
                <div class="card-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
                    <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    <h2 class="text-sm font-bold text-[var(--color-secondary)]">Informasi Dasar</h2>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="nama">
                        Nama Dapur <span class="text-xs text-[var(--color-primary)]">*</span>
                    </label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required
                           class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Contoh: Dapur Pusat Kota" autofocus>
                    @error('nama')
                        <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="3" class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Alamat lengkap dapur">{{ old('alamat') }}</textarea>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="map_embed_url">Embed Google Maps</label>
                    <textarea id="map_embed_url" name="map_embed_url" rows="4" class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Tempelkan src URL iframe Google Maps…">{{ old('map_embed_url') }}</textarea>
                    <p class="text-[11px] text-[var(--color-text-muted)] mt-1">Contoh: https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.12…</p>
                </div>
        </div>
    </div>

        {{-- Row 2: Wilayah --}}
        <div class="card card-bordered">
            <div class="card-body">
                <div class="card-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
                    <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    <h2 class="text-sm font-bold text-[var(--color-secondary)]">Wilayah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="kabupaten_kota">
                            Kabupaten / Kota <span class="text-xs text-[var(--color-primary)]">*</span>
                        </label>
                        <div class="relative">
                            <select id="kabupaten_kota" name="kabupaten_kota"
                                    class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none" required
                                    x-model="form.kabupaten_kota"
                                    x-on:change="onKabupatenChange($event.target.value)">
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                @foreach(\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab)
                                    <option value="{{ $kab }}" {{ old('kabupaten_kota') == $kab ? 'selected' : '' }}>
                                        {{ $kab }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        @error('kabupaten_kota')
                            <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="kecamatan">
                            Kecamatan <span class="text-xs text-[var(--color-primary)]">*</span>
                        </label>
                        <div class="relative">
                            <select id="kecamatan" name="kecamatan"
                                    class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none" required
                                    x-model="form.kecamatan"
                                    :disabled="kecamatanOptions.length === 0"
                                    x-html="getKecamatanOptionsHtml()">
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        @error('kecamatan')
                            <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="provinsi">Provinsi</label>
                        <input id="provinsi" type="text" name="provinsi" value="Banten"
                               class="input input-bordered border border-[var(--color-border)] w-full" readonly>
                        <input type="hidden" name="provinsi" value="Banten">
                    </div>
                </div>
        </div>
    </div>

        <div class="flex flex-wrap gap-3 mt-2 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan Dapur</span>
            </button>
            <a href="{{ route('admin.dapurs.index') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>

@php
$kecamatanMapping = [];
$controller = app(\App\Http\Controllers\DapurController::class);
foreach (\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab) {
    $kecs = $controller->getKnownKecamatan($kab);
    if (!empty($kecs)) {
        $kecamatanMapping[$kab] = $kecs;
    }
}
@endphp

@endsection