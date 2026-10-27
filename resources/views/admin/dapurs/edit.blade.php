@extends('admin.layouts.app', ['pageTitle' => 'Edit Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Edit Dapur: {{ $dapur->nama }}</h1>
            <div class="muted">Perbarui data dapur dan geofence</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.dapurs.update', $dapur) }}" class="space-y-6">
        @csrf
        @method('PUT')

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
                    <input id="nama" type="text" name="nama" value="{{ old('nama', $dapur->nama) }}" required 
                           class="input-field" placeholder="Contoh: Dapur Pusat Kota" autofocus>
                    @error('nama')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="lokasi">Lokasi / Area</label>
                    <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi', $dapur->lokasi) }}" 
                           class="input-field" placeholder="Contoh: Kecamatan Tengah">
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3" class="input-field" placeholder="Alamat lengkap dapur">{{ old('alamat', $dapur->alamat) }}</textarea>
            </div>
        </div>

        {{-- Coordinates Section --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">📍</span>
                Koordinat & Geofence
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-field">
                    <label class="form-label" for="latitude">Latitude</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <input id="latitude" type="number" step="any" name="latitude" value="{{ old('latitude', $dapur->latitude) }}" 
                               class="input-field select-enhanced" placeholder="-6.123456">
                    </div>
                    <p class="form-hint">Contoh: -6.123456 (negatif untuk LS)</p>
                </div>

                <div class="form-field">
                    <label class="form-label" for="longitude">Longitude</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <input id="longitude" type="number" step="any" name="longitude" value="{{ old('longitude', $dapur->longitude) }}" 
                               class="input-field select-enhanced" placeholder="106.123456">
                    </div>
                    <p class="form-hint">Contoh: 106.123456 (positif untuk BT)</p>
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="geofence_radius">Radius Geofence (meter)</label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path></svg>
                    <input id="geofence_radius" type="number" name="geofence_radius" value="{{ old('geofence_radius', $dapur->geofence_radius ?? 100) }}" min="10" max="5000" 
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
                    <li>• Gunakan Google Maps: klik kanan titik lokasi → "Apa di sini?" untuk dapatkan koordinat</li>
                    <li>• Format: Latitude (negatif untuk LS), Longitude (positif untuk BT)</li>
                    <li>• Radius geofence: jarak toleransi GPS pengawas saat absensi</li>
                </ul>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Perbarui Dapur</span>
            </button>
            <a href="{{ route('admin.dapurs.index') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>
@endsection