@props([
    'eyebrow' => 'Genta · Admin Panel',
    'title',
    'subtitle' => null,
    'titleClass' => 'text-[28px] font-extrabold tracking-[-0.5px]',
])

{{--
    Heading standar halaman admin (eyebrow + judul + subjudul + aksi kanan).

    <x-admin-page-heading title="Manajemen User" subtitle="Kelola user...">
        <x-slot:actions>
            <a href="..." class="btn btn-primary">Tambah User</a>
        </x-slot:actions>
    </x-admin-page-heading>
--}}
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
    <div>
        @if ($eyebrow)
            <div class="text-[var(--color-primary)] text-xs font-extrabold uppercase tracking-[1.4px]">{{ $eyebrow }}</div>
        @endif
        <h1 class="{{ $titleClass }} mt-1 mb-1">{{ $title }}</h1>
        @if ($subtitle)
            <div class="text-[var(--color-text-muted)] text-sm mt-1">{{ $subtitle }}</div>
        @endif
    </div>
    @isset($actions)
        <div class="flex gap-3 shrink-0 self-start flex-wrap">{{ $actions }}</div>
    @endisset
</div>