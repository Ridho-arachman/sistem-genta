@if ($photoUrl)
<div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2 cursor-pointer" data-action="open-photo-preview" data-photo-url="{{ $photoUrl }}">
    <img src="{{ $photoUrl }}" alt="Foto Laporan" class="w-full h-80 object-cover rounded-md">
    <p class="text-[10px] text-[var(--color-text-muted)] mt-1 text-center">Klik untuk memperbesar</p>
</div>
@else
<p class="text-sm text-[var(--color-text-muted)] italic">Foto belum diambil pengawas.</p>
@endif