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
                            <th class="table-th">Nama</th>
                            <th class="table-th">Lokasi</th>
                            <th class="table-th">Alamat</th>
                            <th class="table-th">Koordinat</th>
                            <th class="table-th">Radius Geofence</th>
                            <th class="table-th">Jumlah CCTV</th>
                            <th class="table-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($dapurs as $dapur)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td font-medium">{{ $dapur->nama }}</td>
                            <td class="table-td">{{ $dapur->lokasi ?? '-' }}</td>
                            <td class="table-td max-w-xs truncate">{{ $dapur->alamat ?? '-' }}</td>
                            <td class="table-td text-[var(--color-text-muted)] font-mono text-sm">
                                @if ($dapur->latitude && $dapur->longitude)
                                    {{ $dapur->latitude }}, {{ $dapur->longitude }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="table-td">{{ $dapur->geofence_radius ?? 100 }} meter</td>
                            <td class="table-td">{{ $dapur->cctvs_count ?? 0 }}</td>
                            <td class="table-td">
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