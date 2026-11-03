@extends('pengawas.layouts.app', ['pageTitle' => 'Detail Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Standard Header --}}
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>{{ $dapur->nama }}</h1>
            <div class="muted">{{ $dapur->lokasi ?? 'Lokasi tidak diset' }}</div>
        </div>
        <a href="{{ route('pengawas.dashboard') }}" class="btn-secondary">Kembali ke Beranda</a>
    </div>

    <div class="card p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Alamat</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->alamat ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Kepala Dapur</p>
                @if ($dapur->kepalaDapur)
                    <p class="font-semibold text-[var(--color-secondary)]">
                        {{ $dapur->kepalaDapur->name }} ({{ $dapur->kepalaDapur->email }})
                    </p>
                @else
                    <span class="text-[var(--color-text-muted)] text-sm">-</span>
                @endif
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Jumlah CCTV</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $dapur->cctvs->count() }}</p>
            </div>
        </div>

        {{-- Map Embed --}}
        @if ($dapur->map_embed_url)
        @php
            $lat = null;
            $lng = null;
            if (preg_match('/!3d([-0-9.]+)/', $dapur->map_embed_url, $m)) {
                $lat = $m[1];
            }
            if (preg_match('/!2d([-0-9.]+)/', $dapur->map_embed_url, $m)) {
                $lng = $m[1];
            }
        @endphp
        <div class="border-t border-[var(--color-border)] pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Lokasi di Peta</h3>
            <div class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden">
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
                @if ($lat && $lng)
                <a href="geo:{{ $lat }},{{ $lng }}?z=16"
                   class="absolute top-2 right-2 bg-white text-black px-3 py-1 rounded text-sm font-medium shadow-md hover:bg-gray-100 transition-colors">
                    Buka di Google Maps
                </a>
                @endif
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