@extends('admin.layouts.app', ['pageTitle' => 'Laporan Pengawasan'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Laporan Pengawasan</h1>
        <div class="muted">Rekap inspeksi dan tindak lanjut</div>
    </div>
    <a href="#" class="btn-light">⇩ Ekspor</a>
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
                    <td class="table-td">Serang 01</td>
                    <td class="table-td">Siti Rahma</td>
                    <td class="table-td"><span class="badge badge-success">Diperiksa</span></td>
                </tr>
                <tr>
                    <td class="table-td">{{ now()->subDay()->format('d M Y') }}</td>
                    <td class="table-td">Cilegon 02</td>
                    <td class="table-td">Andi Saputra</td>
                    <td class="table-td"><span class="badge badge-warning">Tindak lanjut</span></td>
                </tr>
                <tr>
                    <td class="table-td">{{ now()->subDay()->format('d M Y') }}</td>
                    <td class="table-td">Pandeglang 01</td>
                    <td class="table-td">Rina Putri</td>
                    <td class="table-td"><span class="badge badge-success">Selesai</span></td>
                </tr>
                <tr>
                    <td class="table-td">{{ now()->subDays(2)->format('d M Y') }}</td>
                    <td class="table-td">Lebak 03</td>
                    <td class="table-td">Budi Santoso</td>
                    <td class="table-td"><span class="badge badge-danger">Temuan</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection