@props([
    'status' => 'ok',
    'label' => null,
])

@php
    $label = $label ?? ucfirst($status);
    $classes = [
        'ok' => 'bg-green-100 text-green-800',
        'warn' => 'bg-yellow-100 text-yellow-800',
        'off' => 'bg-red-100 text-red-800',
    ];
    $class = $classes[$status] ?? $classes['ok'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider whitespace-nowrap {{ $class }}">
    {{ $label }}
</span>