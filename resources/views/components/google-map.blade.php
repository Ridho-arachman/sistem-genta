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

<script>
document.addEventListener('DOMContentLoaded', function() {
    initPublicMap('{{ $id }}', {{ $lat }}, {{ $lng }}, {{ $zoom }}, {{ $showMarker ? 'true' : 'false' }}, {{ $interactive ? 'true' : 'false' }}, @js($title), @js($address));
});

function initPublicMap(id, lat, lng, zoom, showMarker, interactive, title, address) {
    if (typeof google === 'undefined') {
        loadGoogleMapsPublic(() => initPublicMap(id, lat, lng, zoom, showMarker, interactive, title, address));
        return;
    }

    const map = new google.maps.Map(document.getElementById(id), {
        center: { lat: parseFloat(lat), lng: parseFloat(lng) },
        zoom: zoom,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: interactive,
        zoomControl: interactive,
        scrollwheel: interactive,
        draggable: interactive,
        styles: [
            { elementType: 'geometry', stylers: [{ color: '#f5f5f5' }] },
            { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
            { elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
            { elementType: 'labels.text.stroke', stylers: [{ color: '#f5f5f5' }] },
            { featureType: 'administrative.land_parcel', elementType: 'labels.text.fill', stylers: [{ color: '#bdbdbd' }] },
            { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#eeeeee' }] },
            { featureType: 'poi', elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
            { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#e5e5e5' }] },
            { featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] },
            { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
            { featureType: 'road.arterial', elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
            { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#dadada' }] },
            { featureType: 'road.highway', elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
            { featureType: 'road.local', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] },
            { featureType: 'transit.line', elementType: 'geometry', stylers: [{ color: '#e5e5e5' }] },
            { featureType: 'transit.station', elementType: 'geometry', stylers: [{ color: '#eeeeee' }] },
            { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c9c9c9' }] },
            { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] }
        ]
    });

    if (showMarker) {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(lat), lng: parseFloat(lng) },
            map: map,
            title: title,
            icon: {
                url: 'https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png',
                scaledSize: new google.maps.Size(32, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(16, 32)
            }
        });

        if (address) {
            const infoWindow = new google.maps.InfoWindow({
                content: '<div style="padding: 8px; max-width: 200px;"><strong>' + title + '</strong><br><small>' + address + '</small></div>'
            });
            
            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        }
    }
}

function loadGoogleMapsPublic(callback) {
    if (document.querySelector('script[src*="maps.googleapis.com"]')) {
        // Already loading, wait for it
        const checkMaps = setInterval(() => {
            if (typeof google !== 'undefined') {
                clearInterval(checkMaps);
                callback();
            }
        }, 100);
        return;
    }

    const script = document.createElement('script');
    const apiKey = document.querySelector('meta[name="google-maps-key"]')?.content || '';
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initPublicMapCallback`;
    script.async = true;
    script.defer = true;
    window.initPublicMapCallback = callback;
    document.head.appendChild(script);
}
</script>
@endif