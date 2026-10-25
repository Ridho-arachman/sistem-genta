<x-layout>
    <div class="max-w-3xl mx-auto">
        <div class="bg-surface border border-zinc-800 rounded-xl p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-white mb-6">Absensi Check-in</h1>
            <p class="text-zinc-400 text-sm mb-6">Absensi dicatat dengan lokasi GPS dan selfie. Pastikan Anda berada di lokasi dapur.</p>

            <form method="POST" action="{{ route('absensi.store') }}" class="space-y-6" id="absensiForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Dapur</label>
                        <select name="dapur_id" id="dapurSelect" required
                                class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                            @foreach ($dapurs as $dapur)
                                <option value="{{ $dapur->id }}">{{ $dapur->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                        <select name="status" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option>
                            <option value="tidak_hadir">Tidak Hadir</option>
                        </select>
                    </div>
                </div>

                <!-- Geolocation -->
                <div class="p-4 bg-zinc-900/50 border border-zinc-800 rounded-lg">
                    <h3 class="text-sm font-medium text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lokasi GPS
                    </h3>
                    <div class="flex items-center gap-3">
                        <button type="button" id="getLocationBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Ambil Lokasi
                        </button>
                        <div id="locationStatus" class="text-sm text-zinc-400">Belum dicatat</div>
                        <input type="hidden" name="lat" id="latInput">
                        <input type="hidden" name="lng" id="lngInput">
                    </div>
                    <p class="text-xs text-zinc-500 mt-2">Lokasi akan dicatat otomatis saat check-in.</p>
                </div>

                <!-- Selfie -->
                <div class="p-4 bg-zinc-900/50 border border-zinc-800 rounded-lg">
                    <h3 class="text-sm font-medium text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14.5M22 13h-4v-4m0 0l-3-3m3 3l-3 3M2 13h4v4m0 0l3-3m-3 3l3 3"></path></svg>
                        Selfie Verifikasi
                    </h3>
                    <input type="file" name="selfie_path" accept="image/*" capture="environment"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    <p class="text-xs text-zinc-500 mt-2">Ambil foto selfie untuk verifikasi kehadiran.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span>Check-in Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const getLocationBtn = document.getElementById('getLocationBtn');
        const locationStatus = document.getElementById('locationStatus');
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');

        getLocationBtn.addEventListener('click', () => {
            if (!navigator.geolocation) {
                locationStatus.textContent = 'Geolocation tidak didukung browser ini.';
                locationStatus.className = 'text-sm text-red-400';
                return;
            }

            getLocationBtn.textContent = 'Mengambil lokasi...';
            getLocationBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    latInput.value = lat;
                    lngInput.value = lng;
                    locationStatus.textContent = `Lokasi: ${lat}, ${lng}`;
                    locationStatus.className = 'text-sm text-green-400';
                    getLocationBtn.textContent = 'Lokasi Tercatat';
                    getLocationBtn.disabled = false;
                },
                (error) => {
                    locationStatus.textContent = 'Gagal mengambil lokasi: ' + error.message;
                    locationStatus.className = 'text-sm text-red-400';
                    getLocationBtn.textContent = 'Ambil Lokasi';
                    getLocationBtn.disabled = false;
                },
                { enableHighAccuracy: true }
            );
        });
    </script>
</x-layout>
