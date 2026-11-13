@props([
    'label',
    'value',
    'note' => null,
    'icon' => null,
    'valueClass' => 'text-[var(--color-secondary)]',
    'iconClass' => 'bg-green-100 text-green-800',
])

{{--
    Kartu statistik ringkas (label + ikon + angka besar + catatan).

    <x-admin-stat-card label="Total Dapur" :value="$stats['total_dapur']" note="Lokasi terdaftar" icon="⌂" />
--}}
<div {{ $attributes->merge(['class' => 'bg-white border border-[var(--color-border)] rounded-[14px] p-[18px]']) }}>
    <div class="flex justify-between items-center mb-1.5">
        <span>{{ $label }}</span>
        @if ($icon)
            <span class="w-[28px] h-[28px] rounded-[9px] {{ $iconClass }} flex items-center justify-center text-[14px]">{!! $icon !!}</span>
        @endif
    </div>
    <div class="text-[32px] font-extrabold leading-[1.1] font-display {{ $valueClass }}">{{ $value }}</div>
    @if ($note)
        <div class="text-[12px] text-[var(--color-text-muted)] mt-1.5">{{ $note }}</div>
    @endif
</div>