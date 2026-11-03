@extends('pengawas.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Laporan Pengawasan</h1>
        <div class="muted">Rekap inspeksi dan tindak lanjut</div>
    </div>
</div>

<div class="panel">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-th">TANGGAL</th>
                    <th class="table-th">LOKASI</th>
                    <th class="table-th">PENGAWAS</th>
                    <th class="table-th">STATUS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
                @if ($laporans->isNotEmpty())
                    @foreach ($laporans as $laporan)
                        <tr>
                            <td class="table-td">{{ $laporan->created_at->format('d M Y') }}</td>
                            <td class="table-td">{{ $laporan->dapur->nama ?? 'Dapur' }}</td>
                            <td class="table-td">{{ $laporan->createdBy?->name ?? '-' }}</td>
                            <td class="table-td">
                                <span class="badge badge-{{ $laporan->status === 'selesai' ? 'success' : ($laporan->status === 'temuan' ? 'warning' : 'info') }}">{{ ucfirst($laporan->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="table-td text-center" colspan="4">Belum ada laporan</td>
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
@endsection