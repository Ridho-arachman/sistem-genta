@props(['dapur', 'laporanStatus' => null])

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
            referrerpolicy="no-referrer-when-downgrade"
            style="pointer-events: none;">
        </iframe>
    </div>
</div>
@endif