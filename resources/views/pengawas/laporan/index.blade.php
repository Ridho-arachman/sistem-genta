@extends('pengawas.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Laporan Pengawasan</h1>
        <div class="muted">Rekap inspeksi dan tindak lanjut</div>
    </div>
    <a href="{{ route('pengawas.laporan.create') }}" class="btn-primary">＋ Buat Laporan</a>
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
                <tr>
                    <td class="table-td">{{ now()->format('d M Y') }}</td>
                    <td class="table-td">{{ auth()->user()->dapur->nama ?? 'Dapur' }}</td>
                    <td class="table-td">{{ auth()->user()->name }}</td>
                    <td class="table-td"><span class="badge badge-success">Selesai</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection