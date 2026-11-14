@props([
    'eyebrow' => 'Genta · Pengawasan MBG',
    'title',
    'subtitle' => null,
])

{{--
    Judul halaman pengawas (eyebrow + h1 + subtitle + aksi opsional).
    Pengganti kelas CSS .heading/.eyebrow/.muted yang sudah dihapus dari app.css.
--}}
<div class="mb-[23px] flex flex-wrap items-start justify-between gap-4 max-[900px]:flex-col">
    <div class="min-w-0">
        <div class="text-[11px] font-extrabold uppercase tracking-[1.4px] text-[var(--color-primary)]">{{ $eyebrow }}</div>
        <h1 class="mt-1 mb-0.5 text-2xl font-extrabold tracking-[-0.5px] text-[var(--color-secondary)] min-[901px]:text-[28px]">{{ $title }}</h1>
        @if ($subtitle)
            <div class="text-sm text-[var(--color-text-muted)]">{{ $subtitle }}</div>
        @endif
    </div>

    @isset($actions)
        <div class="flex w-full gap-2 max-[520px]:flex-col sm:w-auto sm:flex-row [&_.btn]:w-full min-[521px]:[&_.btn]:w-auto">
            {{ $actions }}
        </div>
    @endisset
</div>