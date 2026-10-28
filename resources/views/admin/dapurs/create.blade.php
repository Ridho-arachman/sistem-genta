@extends('admin.layouts.app', ['pageTitle' => 'Tambah Dapur'])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Tambah Dapur Baru</h1>
            <div class="muted">Isi data dapur termasuk koordinat untuk geofence absensi</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.dapurs.store') }}" class="space-y-6">
        @csrf

        {{-- Basic Info Section --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">⌂</span>
                Informasi Dasar
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-field">
                    <label class="form-label" for="nama">
                        Nama Dapur <span class="required">*</span>
                    </label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required 
                           class="input-field" placeholder="Contoh: Dapur Pusat Kota" autofocus>
                    @error('nama')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="lokasi">Lokasi / Area</label>
                    <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi') }}" 
                           class="input-field" placeholder="Contoh: Kecamatan Tengah">
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3" class="input-field" placeholder="Alamat lengkap dapur">{{ old('alamat') }}</textarea>
            </div>
        </div>

        {{-- Coordinates & Map Section --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">📍</span>
                Koordinat & Geofence
            </div>

            <!-- Map Picker Component -->
            <x-leaflet-map-picker 
                :latitude="old('latitude')"
                :longitude="old('longitude')"
                :nameLat="'latitude'"
                :nameLng="'longitude'"
                :readonly="false"
                :height="'350px'"
                :label="'Pilih Lokasi Dapur di Peta'"
                :helpText="'Klik peta untuk memilih lokasi, atau gunakan tombol \"Gunakan Lokasi Saya\". Marker merah bisa digeser.'"
            />

            <div class="form-field mt-4">
                <label class="form-label" for="geofence_radius">Radius Geofence (meter)</label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <input id="geofence_radius" type="number" name="geofence_radius" value="{{ old('geofence_radius', 100) }}" min="10" max="5000" 
                           class="input-field select-enhanced" placeholder="100">
                </div>
                <p class="form-hint">Radius validasi GPS absensi (default: 100 meter, max: 5000 meter)</p>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h4 class="font-medium text-blue-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tips Koordinat
                </h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Klik peta di atas untuk memilih lokasi dapur</li>
                    <li>• Atau klik "Gunakan Lokasi Saya" untuk ambil lokasi GPS perangkat</li>
                    <li>• Marker merah bisa digeser untuk penyesuaian presisi</li>
                    <li>• Format: Latitude (negatif untuk LS), Longitude (positif untuk BT)</li>
                    <li>• Radius geofence: jarak toleransi GPS pengawas saat absensi</li>
                </ul>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan Dapur</span>
            </button>
            <a href="{{ route('admin.dapurs.index') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>
@endsection