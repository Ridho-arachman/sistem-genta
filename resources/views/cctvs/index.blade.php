<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Daftar CCTV</h1>
                <p class="text-zinc-400 text-sm">Pantau semua kamera CCTV di setiap dapur.</p>
            </div>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('cctvs.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        + Tambah CCTV
                    </a>
                @endif
            @endauth
        </div>

        <div class="bg-surface border border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-700 bg-zinc-900/50">
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Nama CCTV</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Dapur</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">IP Address</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-center">Status</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach ($cctvs as $cctv)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-4 text-white">{{ $cctv->nama }}</td>
                            <td class="py-4 text-zinc-300">{{ $cctv->dapur->nama }}</td>
                            <td class="py-4 text-mono text-zinc-300">{{ $cctv->ip_address ?? '-' }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($cctv->status === 'online') bg-green-500/20 text-green-400
                                    @elseif($cctv->status === 'offline') bg-red-500/20 text-red-400
                                    @else bg-yellow-500/20 text-yellow-400 @endif">
                                    {{ ucfirst($cctv->status) }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('cctvs.show', $cctv) }}" class="text-green-400 text-sm hover:text-green-300">Detail →</a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('cctvs.edit', $cctv) }}" class="ml-4 text-zinc-400 hover:text-white text-sm">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $cctvs->links() }}
    </div>
</x-layout>
