@props(['status'])

@php
    $badgeColors = [
        'diperiksa' => 'info',
        'temuan' => 'danger',
        'tindak lanjut' => 'warning',
        'selesai' => 'success',
    ];
    $label = ucfirst(str_replace('_', ' ', $status));
@endphp

<span class="badge badge-{{ $badgeColors[$status] ?? 'info' }}">{{ $label }}</span>