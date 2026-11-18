@props(['status'])

@php
    $badgeColors = [
        'diperiksa' => 'badge-info',
        'temuan' => 'badge-error',
        'tindak lanjut' => 'badge-warning',
        'selesai' => 'badge-success',
    ];
    $label = ucfirst(str_replace('_', ' ', $status));
@endphp

<span class="badge {{ $badgeColors[$status] ?? 'badge-info' }}">{{ $label }}</span>