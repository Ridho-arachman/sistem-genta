<x-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-surface border border-zinc-800 rounded-xl p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-white mb-6">Edit Dapur</h1>

            <form method="POST" action="{{ route('dapurs.update', $dapur) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Nama Dapur</label>
                        <input type="text" name="nama" required
                               class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                               value="{{ old('nama', $dapur->nama) }}">
                        @error('nama')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Lokasi</label>
                        <input type="text" name="lokasi"
                               class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                               value="{{ old('lokasi', $dapur->lokasi) }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3"
                              class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">{{ old('alamat', $dapur->alamat) }}</textarea>
                </div>

                <fieldset class="border border-zinc-800 rounded-lg p-4">
                    <legend class="text-sm font-medium text-zinc-300 mb-3">📍 Geofence untuk Absensi Otomatis (Opsional)</legend>
                    <p class="text-xs text-zinc-500 mb-4">Isi jika ingin absensi otomatis via GPS ketika pengawas masuk/keluar area dapur.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Latitude</label>
                            <input type="number" step="0.0000001" name="latitude"
                                   class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                                   value="{{ old('latitude', $dapur->latitude) }}">
                            @error('latitude')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Longitude</label>
                            <input type="number" step="0.0000001" name="longitude"
                                   class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                                   value="{{ old('longitude', $dapur->longitude) }}">
                            @error('longitude')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Radius (meter)</label>
                            <input type="number" name="geofence_radius" min="10" max="5000"
                                   class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                                   value="{{ old('geofence_radius', $dapur->geofence_radius ?? 100) }}">
                            @error('geofence_radius')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-zinc-500 mt-1">Radius area deteksi (default: 100m)</p>
                        </div>
                    </div>
                </fieldset>

                <div class="flex justify-end pt-4">
                    <a href="{{ route('dapurs.index') }}" class="text-zinc-400 hover:text-white mr-4">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
