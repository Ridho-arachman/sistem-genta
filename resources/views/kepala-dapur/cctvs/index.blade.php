@extends('kepala-dapur.layouts.app', ['pageTitle' => 'Data CCTV'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Data CCTV Dapur</h2>
        <p class="text-[var(--color-text-muted)]">Monitoring kamera CCTV di dapur Anda (Read Only)</p>
    </div>

    <div class="card overflow-hidden">
        @if ($cctvs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full"><thead class="bg-[var(--color-surface)]"><tr>
                    <th class="table-th">Nama</th><th class="table-th">Area</th><th class="table-th">Cam #</th><th class="table-th">IP Address</th><th class="table-th">Status</th><th class="table-th">Aksi</th>
                </tr></thead><tbody class="divide-y divide-[var(--color-border)]">
                    @foreach ($cctvs as $cctv)
                    <tr class="hover:bg-[var(--color-surface)]">
                        <td class="table-td font-medium">{{ $cctv->display_name }}</td>
                        <td class="table-td">{{ $cctv->section ?? '-' }}</td>
                        <td class="table-td">{{ $cctv->camera_number ?? '-' }}</td>
                        <td class="table-td text-[var(--color-text-muted)] font-mono text-sm">{{ $cctv->ip_address ?? '-' }}</td>
                        <td class="table-td"><span class="badge @if($cctv->status === 'online') badge-success @elseif($cctv->status === 'offline') badge-danger @else badge-warning @endif">{{ ucfirst($cctv->status) }}</span></td>
                        <td class="table-td">
                            <a href="{{ route('kepala-dapur.cctvs.show', $cctv) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a>
                            <a href="{{ route('kepala-dapur.cctvs.monitoring', $cctv->dapur) }}" class="text-blue-600 hover:underline text-sm ml-2">Monitoring</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody></table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">{{ $cctvs->links() }}</div>
        @else
            <div class="p-12 text-center"><svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg><h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Data CCTV</h3></div>
        @endif
    </div>
</div>
@endsection
