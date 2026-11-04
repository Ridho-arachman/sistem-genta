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
            class="border-0"
            src="{{ $dapur->map_embed_url }}"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        @if ($lat && $lng)
        <a href="geo:0,0?q={{ $lat }},{{ $lng }}"
           class="absolute top-2 right-2 bg-white text-black px-3 py-1 rounded text-sm font-medium shadow-md hover:bg-gray-100 transition-colors">
            Buka di Google Maps
        </a>
        @if ($laporanStatus ?? null)
        <div class="absolute top-2 right-10 bg-green-100 text-green-700 px-3 py-1 rounded text-sm font-medium shadow-sm">
            ✅ GPS tersimpan: {{ $lat }}, {{ $lng }}
        </div>
        @endif
        @endif
    </div>
</div>
@endif