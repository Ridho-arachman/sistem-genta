@extends('admin.layouts.app', ['pageTitle' => 'Data Dapur'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Data Dapur</h1>
        <div class="muted">Kelola dapur pangan dan geofence absensi</div>
    </div>
    <a href="{{ route('admin.dapurs.create') }}" class="btn-action btn-action-primary">
        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Tambah Dapur</span>
    </a>
</div>

<div class="panel">
    @if ($dapurs->count() > 0)
        <div class="panel-head">
            <h2>Daftar Dapur</h2>
            <span class="text-sm text-[var(--color-text-muted)]">{{ $dapurs->total() }} dapur terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-th min-w-[200px]">Nama Dapur</th>
                        <th class="table-th min-w-[150px]">Lokasi</th>
                        <th class="table-th min-w-[150px] hidden lg:table-cell">Pengawas</th>
                        <th class="table-th min-w-[150px] hidden lg:table-cell">Kepala Dapur</th>
                        <th class="table-th min-w-[120px]">Alamat</th>
                        <th class="table-th min-w-[120px] hidden lg:table-cell">Koordinat</th>
                        <th class="table-th min-w-[100px]">Radius Geofence</th>
                        <th class="table-th min-w-[100px] text-center">Jumlah CCTV</th>
                        <th class="table-th min-w-[120px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @foreach ($dapurs as $dapur)
                    <tr class="hover:bg-[var(--color-surface)]/50 transition-colors">
                        <td class="table-td font-semibold text-[var(--color-secondary)] min-w-0">
                            <span class="truncate line-clamp-1 block">{{ $dapur->nama }}</span>
                        </td>
                        <td class="table-td min-w-0">{{ $dapur->lokasi ?? '-' }}</td>
                        <td class="table-td min-w-0 hidden lg:table-cell">
                            @if ($dapur->pengawas->count() > 0)
                                <div class="flex flex-wrap gap-1.5 max-h-[40px] overflow-hidden">
                                    @php $pCount = $dapur->pengawas->count(); @endphp
                                    @foreach ($dapur->pengawas->take(3) as $p)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)] truncate max-w-[120px] line-clamp-1">
                                            {{ $p->name }}
                                        </span>
                                    @endforeach
                                    @if ($pCount > 3)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text-muted)]">
                                            +{{ $pCount - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[var(--color-text-muted)] text-sm">Belum diassign</span>
                            @endif
                        </td>
                        <td class="table-td min-w-0 hidden lg:table-cell">
                            @if ($dapur->kepalaDapur)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#22C55E15] text-[#166534] truncate max-w-[200px] line-clamp-1">
                                    {{ $dapur->kepalaDapur->name }}
                                </span>
                            @else
                                <span class="text-[var(--color-text-muted)] text-sm">Belum diassign</span>
                            @endif
                        </td>
                        <td class="table-td max-w-xs truncate text-[var(--color-text-muted)] min-w-0">{{ $dapur->alamat ?? '-' }}</td>
                        <td class="table-td text-[var(--color-text-muted)] font-mono text-xs hidden lg:table-cell min-w-0">
                            @if ($dapur->latitude && $dapur->longitude)
                                {{ $dapur->latitude }}, {{ $dapur->longitude }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="table-td min-w-0">{{ $dapur->geofence_radius ?? 100 }} meter</td>
                        <td class="table-td text-center min-w-0">
                            <span class="badge badge-primary">{{ $dapur->cctvs_count ?? 0 }} kamera</span>
                        </td>
                        <td class="table-td text-center min-w-0">
                            <div class="flex items-center gap-3 justify-center">
                                <a href="{{ route('admin.dapurs.show', $dapur) }}" class="text-[var(--color-primary)] hover:underline text-sm font-medium">Detail</a>
                                <a href="{{ route('admin.dapurs.edit', $dapur) }}" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                                <form action="{{ route('admin.dapurs.destroy', $dapur) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus dapur {{ $dapur->nama }}?')">
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
            {{ $dapurs->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-[var(--color-surface)] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Belum Ada Data Dapur</h3>
            <p class="text-[var(--color-text-muted)] mb-4">Mulai dengan menambahkan dapur pertama</p>
            <a href="{{ route('admin.dapurs.create') }}" class="btn-action btn-action-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Dapur
            </a>
        </div>
    @endif
</div>
@endsection