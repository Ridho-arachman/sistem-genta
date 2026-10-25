<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Absensi</h1>
                <p class="text-zinc-400 text-sm">Filter absensi pengawas dapur dan export laporan.</p>
            </div>
            <a href="{{ route('absensi.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                + Check-in Absensi
            </a>
        </div>

        <!-- Filter form -->
        <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
            <form method="GET" action="{{ route('absensi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Dapur</label>
                    <select name="dapur_id" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Dapur</option>
                        @foreach ($dapurs as $dapur)
                            <option value="{{ $dapur->id }}" {{ request('dapur_id') == $dapur->id ? 'selected' : '' }}>
                                {{ $dapur->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
                    <a href="{{ route('absensi.index') }}" class="text-zinc-400 hover:text-white px-4 py-2 rounded-lg text-sm font-medium border border-zinc-700">Reset</a>
                </div>
            </form>
        </div>

        <!-- Export button -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('absensi.export', array_merge(request()->only(['dapur_id', 'start_date', 'end_date']), ['format' => 'csv'])) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                📥 CSV
            </a>
            <a href="{{ route('absensi.export', array_merge(request()->only(['dapur_id', 'start_date', 'end_date']), ['format' => 'xlsx'])) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                📊 Excel (XLSX)
            </a>
            <a href="{{ route('absensi.export', array_merge(request()->only(['dapur_id', 'start_date', 'end_date']), ['format' => 'pdf'])) }}"
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                📄 PDF (Cetak)
            </a>
        </div>

        <!-- Table -->
        <div class="bg-surface border border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-700 bg-zinc-900/50">
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Tanggal</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Pengawas</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400">Dapur</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-center">Check-in</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-center">Check-out</th>
                        <th class="pb-3 pt-4 text-sm font-medium text-zinc-400 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach ($absensis as $absensi)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-4 text-mono text-zinc-300 text-sm">{{ $absensi->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-4 text-white">{{ $absensi->user->name }}</td>
                            <td class="py-4 text-zinc-300">{{ $absensi->dapur->nama }}</td>
                            <td class="py-4 text-center text-mono text-zinc-300 text-sm">
                                {{ $absensi->check_in_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="py-4 text-center text-mono text-zinc-300 text-sm">
                                {{ $absensi->check_out_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($absensi->status === 'hadir') bg-green-500/20 text-green-400
                                    @elseif($absensi->status === 'izin') bg-blue-500/20 text-blue-400
                                    @elseif($absensi->status === 'cuti') bg-yellow-500/20 text-yellow-400
                                    @else bg-red-500/20 text-red-400 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $absensi->status)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $absensis->links() }}
    </div>
</x-layout>
