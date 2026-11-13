@props([
    'title' => null,
    'message' => null,
])

{{--
    Empty state standar halaman admin (ikon + judul + pesan + aksi opsional).

    <x-admin-empty-state title="Tidak Ada User" message="Belum ada user yang terdaftar">
        <x-slot:icon>
            <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" ...>...</svg>
        </x-slot:icon>
        <x-slot:action>
            <a href="..." class="btn btn-primary mt-4 inline-block">Tambah User Pertama</a>
        </x-slot:action>
    </x-admin-empty-state>
--}}
<div class="py-16 text-center">
    @isset($icon)
        {{ $icon }}
    @else
        <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
    @endisset
    @if ($title)
        <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">{{ $title }}</h3>
    @endif
    @if ($message)
        <p class="text-[var(--color-text-muted)] mt-1">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>