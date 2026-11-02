@props([
    'listId' => 'kitchenList',
    'searchId' => 'kitchenSearch',
    'placeholder' => 'Cari nama dapur…',
    'class' => 'search',
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

@pushOnce('kitchen-filter')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kitchenFilter', () => ({
        query: '',
        listId: '',
        searchId: '',

        init(listId, searchId) {
            this.listId = listId;
            this.searchId = searchId;
        },

        filter() {
            const list = document.getElementById(this.listId);
            if (!list) return;
            const cards = list.querySelectorAll('.kitchen-card');
            const q = this.query.toLowerCase();
            cards.forEach(card => {
                const name = card.querySelector('.kitchen-name')?.textContent.toLowerCase() || '';
                const meta = card.querySelector('.kitchen-meta')?.textContent.toLowerCase() || '';
                card.style.display = (name.includes(q) || meta.includes(q)) ? 'flex' : 'none';
            });
        },
    }));
});
</script>
@endPushOnce