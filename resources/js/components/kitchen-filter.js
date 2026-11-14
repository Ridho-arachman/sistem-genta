// Pencarian kartu dapur (komponen x-kitchen-filter)
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
            const cards = list.querySelectorAll('[data-kitchen="card"]');
            const q = this.query.toLowerCase();
            cards.forEach((card) => {
                const name = card.querySelector('[data-kitchen="name"]')?.textContent.toLowerCase() || '';
                const meta = card.querySelector('[data-kitchen="meta"]')?.textContent.toLowerCase() || '';
                card.style.display = name.includes(q) || meta.includes(q) ? 'flex' : 'none';
            });
        },
    }));
});
