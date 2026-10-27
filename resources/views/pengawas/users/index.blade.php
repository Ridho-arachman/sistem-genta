@extends('pengawas.layouts.app', ['pageTitle' => 'Manajemen Pengguna'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Manajemen Pengguna</h1>
        <div class="muted">Daftar pengguna dalam sistem (hanya baca)</div>
    </div>
</div>

<div class="panel">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-th">NAMA</th>
                    <th class="table-th">PERAN</th>
                    <th class="table-th">AKSES</th>
                    <th class="table-th">STATUS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
                <tr>
                    <td class="table-td">{{ auth()->user()->name }}</td>
                    <td class="table-td"><span class="badge badge-primary">Pengawas</span></td>
                    <td class="table-td">{{ auth()->user()->dapur->nama ?? 'Dapur yang ditugaskan' }}</td>
                    <td class="table-td"><span class="badge badge-success">Aktif</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-sm text-[var(--color-text-muted)]">
        <b>Catatan:</b> Pengawas hanya dapat melihat data sendiri. Manajemen pengguna penuh tersedia untuk Admin.
    </div>
</div>
@endsection