// Multi-select dapur (pengawas) — dipakai users/create & users/edit.
// Komponen ini bersarang di dalam scope form (userForm), jadi data induk
// diambil dari scope elemen induknya.
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurMultiSelect', () => ({
        searchQuery: '',

        get parent() {
            return Alpine.$data(this.$el.parentElement);
        },

        get filteredDapurs() {
            if (!this.searchQuery) return this.parent.dapurs || [];
            const q = this.searchQuery.toLowerCase();
            return (this.parent.dapurs || []).filter(
                (d) => d.nama.toLowerCase().includes(q) || (d.lokasi || '').toLowerCase().includes(q),
            );
        },

        toggleDapur(id) {
            this.parent.toggleDapur(id);
        },

        removeDapur(id) {
            this.parent.removeDapur(id);
        },

        getDapurName(id) {
            return this.parent.getDapurName(id);
        },
    }));
});