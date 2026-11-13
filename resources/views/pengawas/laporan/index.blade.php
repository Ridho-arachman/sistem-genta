@extends('pengawas.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('page_content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Laporan Pengawasan</h1>
        <div class="muted">Rekap inspeksi dan tindak lanjut</div>
    </div>
</div>

<div class="card card-bordered">
    {{-- Filter Form --}}
    <div class="card-body">
        <div class="bg-[var(--color-surface)] p-4 rounded-lg mb-6">
        <form method="GET" action="{{ route('pengawas.laporan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-[var(--color-text-muted)] mb-1">Dapur</label>
                <select name="dapur_id" class="input input-bordered w-full">
                    <option value="">-- Semua Dapur --</option>
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}" {{ request('dapur_id') == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[var(--color-text-muted)] mb-1">Tanggal Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input input-bordered w-full">
            </div>
            <div>
                <label class="block text-sm font-medium text-[var(--color-text-muted)] mb-1">Tanggal Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input input-bordered w-full">
            </div>
            <div>
                <label class="block text-sm font-medium text-[var(--color-text-muted)] mb-1">Status</label>
                <select name="status" class="input input-bordered w-full">
                    <option value="">-- Semua Status --</option>
                    <option value="diperiksa" {{ request('status') == 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                    <option value="temuan" {{ request('status') == 'temuan' ? 'selected' : '' }}>Temuan</option>
                    <option value="tindak lanjut" {{ request('status') == 'tindak lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('pengawas.laporan.index') }}" class="btn btn-secondary ml-2">Reset</a>
            </div>
        </form>
    </div>
</div>
</div>

    {{-- Laporan Table --}}
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>LOKASI</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @if ($laporans->isNotEmpty())
                    @foreach ($laporans as $laporan)
                        <tr class="cursor-pointer hover:bg-[var(--color-surface)]/50 transition-colors" data-action="navigate-to-report" data-href="{{ route('pengawas.laporan.show', $laporan->id) }}">
                            <td>{{ $laporan->created_at->format('d M Y') }}</td>
                            <td>{{ $laporan->dapur->nama ?? 'Dapur' }}</td>
                            <td>
                                <span class="badge badge-{{ match($laporan->status) { 'diperiksa' => 'success', 'temuan' => 'danger', 'tindak lanjut' => 'warning', 'selesai' => 'success', default => 'info' } }}">{{ ucfirst($laporan->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('pengawas.laporan.show', $laporan->id) }}" class="text-[var(--color-primary)] text-sm font-medium underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="4">Belum ada laporan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($laporans->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $laporans->links() }}
        </div>
    @endif
</div>
</div>

<script src="{{ asset('js/pengawas-common.js') }}"></script>
@endsection