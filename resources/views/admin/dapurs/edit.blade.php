@extends('admin.layouts.app', ['pageTitle' => 'Edit Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Edit Dapur: {{ $dapur->nama }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.dapurs.update', $dapur) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama Dapur <span class="text-red-500">*</span></label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $dapur->nama) }}" required class="input-field" placeholder="Contoh: Dapur Pusat Kota">
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lokasi" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Lokasi / Area</label>
                <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi', $dapur->lokasi) }}" class="input-field" placeholder="Contoh: Kecamatan Tengah">
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3" class="input-field" placeholder="Alamat lengkap dapur">{{ old('alamat', $dapur->alamat) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="latitude" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Latitude</label>
                    <input id="latitude" type="number" step="any" name="latitude" value="{{ old('latitude', $dapur->latitude) }}" class="input-field" placeholder="-6.123456">
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Longitude</label>
                    <input id="longitude" type="number" step="any" name="longitude" value="{{ old('longitude', $dapur->longitude) }}" class="input-field" placeholder="106.123456">
                </div>
            </div>

            <div>
                <label for="geofence_radius" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Radius Geofence (meter)</label>
                <input id="geofence_radius" type="number" name="geofence_radius" value="{{ old('geofence_radius', $dapur->geofence_radius ?? 100) }}" min="10" max="5000" class="input-field" placeholder="100">
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1">Perbarui Dapur</button>
                <a href="{{ route('admin.dapurs.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection