@extends('admin.layouts.app', ['pageTitle' => 'Edit Dapur: ' . $dapur->nama])

@section('page_content')
<x-admin::page-heading title="Edit Dapur: {{ $dapur->nama }}" subtitle="Perbarui data dapur" />

    <form method="POST" action="{{ route('admin.dapurs.update', $dapur) }}" class="space-y-6" id="dapur-form" x-data="dapurForm(@js($kecamatanMapping), @js(old('kabupaten_kota', $dapur->kabupaten_kota)), @js(old('kecamatan', $dapur->kecamatan)))">
        @csrf
        @method('PUT')

        {{-- Row 1: Nama Dapur --}}
        <x-admin::panel title="Informasi Dasar" icon="home">

                <x-admin::form-field label="Nama Dapur" name="nama" required>
                    <input id="nama" type="text" name="nama" value="{{ old('nama', $dapur->nama) }}" required
                           class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Contoh: Dapur Pusat Kota" autofocus>
                </x-admin::form-field>

                <x-admin::form-field label="Alamat Lengkap" name="alamat">
                    <textarea id="alamat" name="alamat" rows="3" class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Alamat lengkap dapur">{{ old('alamat', $dapur->alamat) }}</textarea>
                </x-admin::form-field>

                <x-admin::form-field label="Embed Google Maps" name="map_embed_url" hint="Contoh: https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.12…">
                    <textarea id="map_embed_url" name="map_embed_url" rows="4" class="input input-bordered border border-[var(--color-border)] w-full" placeholder="Tempelkan src URL iframe Google Maps…">{{ old('map_embed_url', $dapur->map_embed_url) }}</textarea>
                </x-admin::form-field>
        </x-admin::panel>

        {{-- Row 2: Wilayah --}}
        <x-admin::panel title="Wilayah" icon="location">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-admin::form-field label="Kabupaten / Kota" name="kabupaten_kota" required>
                        <div class="relative">
                            <select id="kabupaten_kota" name="kabupaten_kota"
                                    class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none" required
                                    x-model="form.kabupaten_kota"
                                    x-on:change="onKabupatenChange($event.target.value)">
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                @foreach(\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab)
                                    <option value="{{ $kab }}" {{ old('kabupaten_kota', $dapur->kabupaten_kota) === $kab ? 'selected' : '' }}>{{ $kab }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </x-admin::form-field>

                    <x-admin::form-field label="Kecamatan" name="kecamatan" required>
                        <div class="relative">
                            <select id="kecamatan" name="kecamatan"
                                    class="input input-bordered border border-[var(--color-border)] w-full pr-10 appearance-none" required
                                    x-model="form.kecamatan"
                                    :disabled="kecamatanOptions.length === 0"
                                    x-html="getKecamatanOptionsHtml()">
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </x-admin::form-field>

                    <x-admin::form-field label="Provinsi" name="provinsi">
                        <input id="provinsi" type="text" name="provinsi" value="Banten"
                               class="input input-bordered border border-[var(--color-border)] w-full" readonly>
                        <input type="hidden" name="provinsi" value="Banten">
                    </x-admin::form-field>
                </div>
        </x-admin::panel>

        <div class="flex flex-wrap gap-3 mt-2 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Perbarui Dapur</span>
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