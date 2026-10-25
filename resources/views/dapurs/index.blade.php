<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Daftar Dapur</h1>
                <p class="text-zinc-400 text-sm">Kelola data dapur dan pantau CCTV per lokasi.</p>
            </div>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('dapurs.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        + Tambah Dapur
                    </a>
                @endif
            @endauth
        </div>

        <div class="bg-surface border border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-700 bg-zinc-900/50">
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Nama Dapur</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Lokasi</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-center">CCTV</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach ($dapurs as $dapur)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-4 text-white">{{ $dapur->nama }}</td>
                            <td class="py-4 text-zinc-300">{{ $dapur->lokasi ?? '-' }}</td>
                            <td class="py-4 text-center text-mono text-zinc-300">{{ $dapur->cctvs_count }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('dapurs.show', $dapur) }}" class="text-green-400 text-sm hover:text-green-300">Detail →</a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('dapurs.edit', $dapur) }}" class="ml-4 text-zinc-400 hover:text-white text-sm">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $dapurs->links() }}
    </div>
</x-layout>
