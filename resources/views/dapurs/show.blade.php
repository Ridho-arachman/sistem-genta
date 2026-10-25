<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $dapur->nama }}</h1>
                <p class="text-zinc-400 text-sm">{{ $dapur->alamat ?? '-' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dapurs.index') }}" class="text-zinc-400 hover:text-white px-3 py-1 rounded text-sm border border-zinc-700">Kembali</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('dapurs.edit', $dapur) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Edit</a>
                    <a href="{{ route('cctvs.create') }}?dapur_id={{ $dapur->id }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Tambah CCTV</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Info Dapur</h3>
                <p class="text-zinc-300 text-sm mb-2"><strong>Lokasi:</strong> {{ $dapur->lokasi ?? '-' }}</p>
                <p class="text-zinc-300 text-sm mb-2"><strong>Alamat:</strong> {{ $dapur->alamat ?? '-' }}</p>
                @if ($dapur->latitude && $dapur->longitude)
                    <p class="text-zinc-300 text-sm mb-2"><strong>Koordinat:</strong> {{ $dapur->latitude }}, {{ $dapur->longitude }}</p>
                    <p class="text-zinc-300 text-sm mb-2"><strong>Geofence Radius:</strong> {{ $dapur->geofence_radius }} meter</p>
                @else
                    <p class="text-zinc-500 text-sm mb-2"><strong>Geofence:</strong> Belum dikonfigurasi</p>
                @endif
                <p class="text-zinc-300 text-sm"><strong>Total CCTV:</strong> {{ $dapur->cctvs->count() }}</p>
            </div>

            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Daftar CCTV</h3>
                @if ($dapur->cctvs->isEmpty())
                    <p class="text-zinc-500 text-sm">Belum ada CCTV terdaftar.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($dapur->cctvs as $cctv)
                            <li class="flex items-center justify-between p-3 bg-zinc-900 rounded border border-zinc-800">
                                <span class="text-white">{{ $cctv->nama }}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($cctv->status === 'online') bg-green-500/20 text-green-400
                                    @elseif($cctv->status === 'offline') bg-red-500/20 text-red-400
                                    @else bg-yellow-500/20 text-yellow-400 @endif">
                                    {{ ucfirst($cctv->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Monitoring Langsung</h3>
                @if ($dapur->cctvs->isEmpty())
                    <p class="text-zinc-500 text-sm">Belum ada kamera untuk dipantau.</p>
                @else
                    <a href="{{ route('cctvs.monitoring', $dapur) }}" class="w-full bg-surface border border-zinc-700 rounded-lg py-4 text-center text-green-400 hover:bg-zinc-900 hover:border-green-500 transition-colors text-sm font-medium">
                        ▶ Lihat Monitoring CCTV
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-layout>
