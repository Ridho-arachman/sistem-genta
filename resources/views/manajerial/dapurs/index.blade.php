@extends('manajerial.layouts.app', ['pageTitle' => 'Data Dapur'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Data Dapur</h2>
        <p class="text-[var(--color-text-muted)]">Monitoring dapur pangan (Read Only)</p>
    </div>

    <div class="card overflow-hidden">
        @if ($dapurs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th min-w-[200px]">Nama</th>
                            <th class="table-th min-w-[150px]">Lokasi</th>
                            <th class="table-th min-w-[150px] hidden lg:table-cell">Pengawas</th>
                            <th class="table-th min-w-[150px] hidden lg:table-cell">Kepala Dapur</th>
                            <th class="table-th min-w-[120px]">Alamat</th>
                            <th class="table-th min-w-[120px] hidden lg:table-cell">Koordinat</th>
                            <th class="table-th min-w-[100px]">Radius Geofence</th>
                            <th class="table-th min-w-[100px] text-center">Jumlah CCTV</th>
                            <th class="table-th min-w-[80px] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($dapurs as $dapur)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td font-medium min-w-0">
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
                            <td class="table-td max-w-xs truncate min-w-0">{{ $dapur->alamat ?? '-' }}</td>
                            <td class="table-td text-[var(--color-text-muted)] font-mono text-sm hidden lg:table-cell min-w-0">
                                @if ($dapur->latitude && $dapur->longitude)
                                    {{ $dapur->latitude }}, {{ $dapur->longitude }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="table-td min-w-0">{{ $dapur->geofence_radius ?? 100 }} meter</td>
                            <td class="table-td text-center min-w-0">{{ $dapur->cctvs_count ?? 0 }}</td>
                            <td class="table-td text-center min-w-0">
                                <a href="{{ route('manajerial.dapurs.show', $dapur) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a>
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
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Data Dapur</h3>
            </div>
        @endif
    </div>
</div>
@endsection