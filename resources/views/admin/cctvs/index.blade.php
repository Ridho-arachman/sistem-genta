@extends('admin.layouts.app', ['pageTitle' => 'Data CCTV'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Data CCTV</h1>
        <div class="muted">Kelola kamera CCTV per dapur</div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.cctvs.monitoring-dashboard') }}" class="btn-secondary">Monitoring</a>
        <a href="{{ route('admin.cctvs.create') }}" class="btn-primary">＋ Tambah CCTV</a>
    </div>
</div>

<div class="panel">
    @if ($cctvs->count() > 0)
        <div class="panel-head">
            <h2>Daftar CCTV</h2>
            <span class="text-sm text-[var(--color-text-muted)]">{{ $cctvs->total() }} kamera terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-th">Nama</th>
                        <th class="table-th">Dapur</th>
                        <th class="table-th">Area</th>
                        <th class="table-th">Cam #</th>
                        <th class="table-th">IP Address</th>
                        <th class="table-th">Status</th>
                        <th class="table-th">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @foreach ($cctvs as $cctv)
                    <tr class="hover:bg-[var(--color-surface)]/50 transition-colors">
                        <td class="table-td font-semibold text-[var(--color-secondary)]">{{ $cctv->nama }}</td>
                        <td class="table-td">{{ $cctv->dapur->nama }}</td>
                        <td class="table-td">{{ $cctv->section ?? '-' }}</td>
                        <td class="table-td">{{ $cctv->camera_number ?? '-' }}</td>
                        <td class="table-td text-[var(--color-text-muted)] font-mono text-xs">{{ $cctv->ip_address ?? '-' }}</td>
                        <td class="table-td">
                            <span class="badge
                                @if($cctv->status === 'online') badge-success
                                @elseif($cctv->status === 'offline') badge-danger
                                @else badge-warning @endif">
                                {{ ucfirst($cctv->status) }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.cctvs.show', $cctv) }}" class="text-[var(--color-primary)] hover:underline text-sm font-medium">Detail</a>
                                <a href="{{ route('admin.cctvs.edit', $cctv) }}" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                                <form action="{{ route('admin.cctvs.destroy', $cctv) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus CCTV {{ $cctv->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[var(--color-border)]">
            {{ $cctvs->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-[var(--color-surface)] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Belum Ada Data CCTV</h3>
            <p class="text-[var(--color-text-muted)] mb-4">Mulai dengan menambahkan CCTV pertama</p>
            <a href="{{ route('admin.cctvs.create') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah CCTV
            </a>
        </div>
    @endif
</div>
@endsection