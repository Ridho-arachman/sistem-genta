// Autocomplete dapur (kepala dapur) — dipakai users/create & users/edit.
// Komponen ini bersarang di dalam scope form (userForm), jadi data induk
// diambil dari scope elemen induknya.
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurAutocomplete', () => ({
        searchQuery: '',
        showDropdown: false,
        highlightedIndex: -1,

        get parent() {
            return Alpine.$data(this.$el.parentElement);
        },

        init() {
            const current = this.parent.kepalaDapurId;
            if (current && !this.searchQuery) {
                const d = (this.parent.dapurs || []).find((x) => x.id == current);
                if (d) this.searchQuery = d.nama;
            }
        },

        get filteredDapurs() {
            if (!this.searchQuery) return this.parent.dapurs || [];
            const q = this.searchQuery.toLowerCase();
            return (this.parent.dapurs || []).filter(
                (d) => d.nama.toLowerCase().includes(q) || (d.lokasi || '').toLowerCase().includes(q),
            );
        },

        selectDapur(dapur) {
            this.parent.kepalaDapurId = dapur.id;
            this.searchQuery = dapur.nama;
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },

        focusFirstOption() {
            if (this.filteredDapurs.length > 0) this.highlightedIndex = 0;
        },

        focusLastOption() {
            if (this.filteredDapurs.length > 0) this.highlightedIndex = this.filteredDapurs.length - 1;
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredDapurs.length) {
                this.selectDapur(this.filteredDapurs[this.highlightedIndex]);
            }
        },
    }));
});