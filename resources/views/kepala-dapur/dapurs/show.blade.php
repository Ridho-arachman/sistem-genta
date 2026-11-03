@extends('admin.layouts.app', ['pageTitle' => 'Detail Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-secondary)]">{{ $dapur->nama }}</h2>
                <p class="text-[var(--color-text-muted)] mt-1">{{ $dapur->lokasi ?? 'Lokasi tidak diset' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kepala-dapur.dapurs.index') }}" class="btn-action btn-action-secondary">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Alamat</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->alamat ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Jumlah CCTV</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->cctvs->count() }}</p>
            </div>
        </div>

        {{-- Map Embed --}}
        @if ($dapur->map_embed_url)
        <div class="border-t border-[var(--color-border)] pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Lokasi di Peta</h3>
            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden relative">
                <iframe
                    width="100%"
                    height="100%"
                    frameborder="0"
                    style="border:0"
                    src="{{ $dapur->map_embed_url }}"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        @endif

        {{-- CCTV List --}}
        @if ($dapur->cctvs->count() > 0)
        <div class="border-t border-[var(--color-border)] pt-6">
            <h3 class="text-lg font-semibold mb-4">Daftar CCTV</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th">Nama</th>
                            <th class="table-th">Area</th>
                            <th class="table-th">Cam #</th>
                            <th class="table-th">IP Address</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($dapur->cctvs as $cctv)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td font-medium">{{ $cctv->nama }}</td>
                            <td class="table-td">{{ $cctv->section ?? '-' }}</td>
                            <td class="table-td">{{ $cctv->camera_number ?? '-' }}</td>
                            <td class="table-td text-[var(--color-text-muted)] font-mono text-sm">{{ $cctv->ip_address ?? '-' }}</td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($cctv->status === 'online') badge-success
                                    @elseif($cctv->status === 'offline') badge-danger
                                    @else badge-warning @endif">
                                    {{ ucfirst($cctv->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection