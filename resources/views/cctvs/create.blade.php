<x-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-surface border border-zinc-800 rounded-xl p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-white mb-6">Tambah CCTV</h1>

            <form method="POST" action="{{ route('cctvs.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Dapur</label>
                    <select name="dapur_id" required
                            class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                        @foreach ($dapurs as $dapur)
                            <option value="{{ $dapur->id }}" {{ old('dapur_id') == $dapur->id ? 'selected' : '' }}>
                                {{ $dapur->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('dapur_id')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Nama CCTV</label>
                    <input type="text" name="nama" required
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                           placeholder="Kamera Utama"
                           value="{{ old('nama') }}">
                    @error('nama')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">IP Address</label>
                        <input type="text" name="ip_address"
                               class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                               placeholder="192.168.1.100"
                               value="{{ old('ip_address') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                        <select name="status"
                                class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                            <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">RTSP URL</label>
                    <input type="text" name="rtsp_url"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                           placeholder="rtsp://user:pass@ip:554/stream"
                           value="{{ old('rtsp_url') }}">
                    <p class="text-xs text-zinc-500 mt-1">URL asli kamera (butuh media server untuk konversi ke HLS).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">HLS URL (opsional)</label>
                    <input type="text" name="hls_url"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all"
                           placeholder="https://example.com/stream.m3u8"
                           value="{{ old('hls_url') }}">
                    <p class="text-xs text-zinc-500 mt-1">URL HLS (.m3u8) untuk playback browser langsung. Dihasilkan dari media server (MediaMTX, SRS, dll).</p>
                </div>

                <div class="flex justify-end pt-4">
                    <a href="{{ route('cctvs.index') }}" class="text-zinc-400 hover:text-white mr-4">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
