<x-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Dashboard Monitoring</h1>
            <p class="text-zinc-400">Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-400 font-medium">Total Dapur</p>
                <p class="text-2xl font-bold text-white mt-1">{{ \App\Models\Dapur::count() }}</p>
            </div>
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-400 font-medium">Total CCTV</p>
                <p class="text-2xl font-bold text-white mt-1">{{ \App\Models\Cctv::count() }}</p>
            </div>
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-400 font-medium">CCTV Online</p>
                <p class="text-2xl font-bold text-green-400 mt-1 text-mono">{{ \App\Models\Cctv::where('status', 'online')->count() }}</p>
            </div>
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-400 font-medium">Absensi Hari Ini</p>
                <p class="text-2xl font-bold text-cyan-400 mt-1 text-mono">{{ \App\Models\Absensi::whereDate('created_at', today())->count() }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Pengawas</h3>
                <p class="text-zinc-400 text-sm mb-4">Kelola data pengawas dan absensi harian.</p>
                <a href="{{ route('absensi.index') }}" class="text-green-400 text-sm font-medium hover:text-green-300">Kelola Absensi →</a>
            </div>
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">CCTV Monitoring</h3>
                <p class="text-zinc-400 text-sm mb-4">Pantau status kamera per dapur.</p>
                <a href="{{ route('cctvs.index') }}" class="text-cyan-400 text-sm font-medium hover:text-cyan-300">Lihat CCTV →</a>
            </div>
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Laporan Absensi</h3>
                <p class="text-zinc-400 text-sm mb-4">Export PDF & Excel dengan filter tanggal.</p>
                <a href="{{ route('absensi.export') }}" class="text-purple-400 text-sm font-medium hover:text-purple-300">Export Laporan →</a>
            </div>
        </div>

        <!-- Dapur List -->
        <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-white mb-6">Daftar Dapur</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-700">
                            <th class="pb-3 text-sm font-medium text-zinc-400">Nama Dapur</th>
                            <th class="pb-3 text-sm font-medium text-zinc-400">Lokasi</th>
                            <th class="pb-3 text-sm font-medium text-zinc-400 text-right">CCTV</th>
                            <th class="pb-3 text-sm font-medium text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @foreach (\App\Models\Dapur::withCount('cctvs')->get() as $dapur)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="py-4 text-white">{{ $dapur->nama }}</td>
                                <td class="py-4 text-zinc-300">{{ $dapur->lokasi ?? '-' }}</td>
                                <td class="py-4 text-right text-mono text-zinc-300">{{ $dapur->cctvs_count }}</td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('dapurs.show', $dapur) }}" class="text-green-400 text-sm hover:text-green-300">Detail →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>