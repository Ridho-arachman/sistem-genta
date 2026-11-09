@extends('pengawas.layouts.app', ['pageTitle' => 'Detail Dapur: ' . $dapur->nama])

@section('page_content')
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
        </div>

        {{-- Map Embed --}}
        @include('pengawas.components.peta-embed', ['dapur' => $dapur])

        {{-- Tombol Buka di Google Maps --}}
        @if ($dapur->map_embed_url)
        <div class="mt-6 flex items-center justify-center">
            <a href="{{ 'geo:0,0?q=' . urlencode($dapur->nama) . ',' . urlencode($dapur->lokasi) }}"
               class="flex-1 px-6 py-3 text-center bg-[var(--color-primary)] text-white rounded-xl text-lg font-semibold hover:bg-[var(--color-primary-dark)] transition-colors shadow-lg">
                🗺️ Buka di Google Maps
            </a>
        </div>
        @endif
    </div>
</div>
@endsection