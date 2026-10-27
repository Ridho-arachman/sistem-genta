@props([
    'id' => 'public-map',
    'height' => '300px',
    'zoom' => 15,
    'showMarker' => true,
    'interactive' => false,
    'class' => 'rounded-xl border border-[var(--color-border)] overflow-hidden',
])

@php
    $lat = settings('office_lat') ?? '-6.2088';
    $lng = settings('office_lng') ?? '106.8456';
    $address = settings('office_address') ?? '';
    $title = settings('app_name_web') ?? 'Kantor Pusat';
@endphp

@if($lat && $lng)
<div class="{{ $class }}" style="height: {{ $height }}; background: var(--color-surface);" id="{{ $id }}"></div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initLeafletMap('{{ $id }}', {{ $lat }}, {{ $lng }}, {{ $zoom }}, {{ $showMarker ? 'true' : 'false' }}, {{ $interactive ? 'true' : 'false' }}, @js($title), @js($address));
});

function initLeafletMap(id, lat, lng, zoom, showMarker, interactive, title, address) {
    if (typeof L === 'undefined') {
        // Leaflet not loaded yet, wait
        setTimeout(() => initLeafletMap(id, lat, lng, zoom, showMarker, interactive, title, address), 100);
        return;
    }

    const container = document.getElementById(id);
    if (!container || container._leaflet_id) return;

    const map = L.map(id, {
        zoomControl: interactive,
        scrollWheelZoom: interactive,
        dragging: interactive,
        doubleClickZoom: interactive,
        boxZoom: interactive,
        keyboard: interactive,
        tap: interactive,
        touchZoom: interactive,
    }).setView([parseFloat(lat), parseFloat(lng)], zoom);

    // OSM Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    if (showMarker) {
        // Custom marker icon to avoid Leaflet default image path issues
        const icon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="width: 28px; height: 28px; background: #E50000; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><div style="width: 12px; height: 12px; background: white; border-radius: 50%; margin: 5px auto 0; transform: rotate(45deg);"></div></div>',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });

        const marker = L.marker([parseFloat(lat), parseFloat(lng)], { icon }).addTo(map);

        if (address) {
            const popupContent = '<div style="padding: 4px 8px; min-width: 180px;"><strong>' + title + '</strong><br><small>' + address + '</small></div>';
            marker.bindPopup(popupContent);
        }
    }
}
</script>

<style>
/* Fix Leaflet CSS conflicts with Tailwind */
.leaflet-container {
    font-family: inherit !important;
}
.leaflet-control-attribution {
    font-size: 10px !important;
}
.custom-marker {
    background: transparent !important;
    border: none !important;
}
</style>
@endif