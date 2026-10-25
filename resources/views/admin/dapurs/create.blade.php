@extends('admin.layouts.app', ['pageTitle' => 'Tambah Dapur'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Tambah Dapur Baru</h2>
            <p class="text-[var(--color-text-muted)] mt-1">Isi data dapur termasuk koordinat untuk geofence absensi</p>
        </div>

        <form method="POST" action="{{ route('admin.dapurs.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="nama" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama Dapur <span class="text-red-500">*</span></label>
                <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required class="input-field" placeholder="Contoh: Dapur Pusat Kota">
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lokasi" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Lokasi / Area</label>
                <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi') }}" class="input-field" placeholder="Contoh: Kecamatan Tengah">
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3" class="input-field" placeholder="Alamat lengkap dapur"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="latitude" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Latitude</label>
                    <input id="latitude" type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="input-field" placeholder="-6.123456">
                    <p class="mt-1 text-xs text-[var(--color-text-muted)]">Contoh: -6.123456</p>
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Longitude</label>
                    <input id="longitude" type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="input-field" placeholder="106.123456">
                    <p class="mt-1 text-xs text-[var(--color-text-muted)]">Contoh: 106.123456</p>
                </div>
            </div>

            <div>
                <label for="geofence_radius" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Radius Geofence (meter)</label>
                <input id="geofence_radius" type="number" name="geofence_radius" value="{{ old('geofence_radius', 100) }}" min="10" max="5000" class="input-field" placeholder="100">
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Radius validasi GPS absensi (default: 100 meter, max: 5000 meter)</p>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
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

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1">Simpan Dapur</button>
                <a href="{{ route('admin.dapurs.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection