@props([
    'listId' => 'kitchenList',
    'searchId' => 'kitchenSearch',
    'placeholder' => 'Cari nama dapur…',
    'class' => 'w-full max-w-[280px] rounded-lg border border-[var(--color-border)] bg-white px-3 py-2 text-[13px] text-[var(--color-text)] focus:border-[var(--color-primary)] focus:outline-none focus:ring-[3px] focus:ring-[var(--color-primary)]/15',
])

<div x-data="kitchenFilter()" x-init="init('{{ $listId }}', '{{ $searchId }}')">
    <input
        type="text"
        id="{{ $searchId }}"
        placeholder="{{ $placeholder }}"
        class="{{ $class }}"
        x-model="query"
        @input="filter()"
        autocomplete="off"
        aria-label="{{ $placeholder }}"
    >
</div>