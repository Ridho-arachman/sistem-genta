@props([
    'title' => null,
    'icon' => null,
    'subtitle' => null,
    'divider' => true,
])

{{--
    Pembungkus kartu standar halaman admin (card card-bordered + card-body).

    Pemakaian:
    <x-admin-panel title="Data Dapur" icon="home" subtitle="Total 12 dapur">
        <x-slot:actions>...</x-slot:actions>
        ...isi...
    </x-admin-panel>

    Slot:
    - actions : aksi di kanan head
    - iconSvg : ikon custom (svg mentah) kalau tidak pakai prop icon (nama heroicon)
    - head    : override penuh area head (mis. headline justify-between)
--}}
<div {{ $attributes->merge(['class' => 'card card-bordered']) }}>
    <div class="card-body">
        @isset($head)
            {{ $head }}
        @elseif ($title || isset($actions))
            <div class="card-title flex items-center gap-2.5 mb-5 {{ $divider ? 'pb-4 border-b border-[var(--color-border)]' : '' }}">
                @if ($icon || isset($iconSvg))
                    <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
                        @if ($icon)
                            <x-heroicon :name="$icon" class="w-4 h-4" />
                        @else
                            {{ $iconSvg }}
                        @endif
                    </span>
                @endif
                <h2 class="text-sm font-bold text-[var(--color-secondary)]">{{ $title }}</h2>
                @if ($subtitle)
                    <small class="text-xs text-[var(--color-text-muted)]">{{ $subtitle }}</small>
                @endif
                @isset($actions)
                    <div class="ml-auto flex items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>
        @endif
        {{ $slot }}
    </div>
</div>