@props([
    'id' => 'public-map',
    'height' => '300px',
    'zoom' => 15,
    'showMarker' => true,
    'interactive' => false,
    'class' => 'rounded-xl border border-[var(--color-border)] overflow-hidden',
])

@php
    $lat = (float) (settings('office_lat') ?? '-6.2088');
    $lng = (float) (settings('office_lng') ?? '106.8456');
    $address = settings('office_address') ?? '';
    $title = settings('app_name_web') ?? 'Kantor Pusat';
    $config = [
        'id' => $id,
        'lat' => $lat,
        'lng' => $lng,
        'zoom' => (int) $zoom,
        'showMarker' => (bool) $showMarker,
        'interactive' => (bool) $interactive,
        'title' => $title,
        'address' => $address,
    ];
@endphp

@if ($lat && $lng)
<div class="{{ $class }}" style="height: {{ $height }}; background: var(--color-surface);" id="{{ $id }}"
     x-data="publicMap(@js($config))"></div>
@endif