// Autocomplete CCTV — dipakai di form anomaly create.
// Standalone: data dikirim dari view (id, label, dapur_name), tanpa scope parent.
// Pencarian mencocokkan nama CCTV atau nama dapur.
document.addEventListener('alpine:init', () => {
    Alpine.data('cctvAutocomplete', (cctvs = [], selectedId = '') => ({
        cctvs,
        selectedId: selectedId ? String(selectedId) : '',
        searchQuery: '',
        showDropdown: false,
        highlightedIndex: -1,

        init() {
            if (this.selectedId) {
                const current = this.cctvs.find((x) => String(x.id) === this.selectedId);
                if (current) this.searchQuery = current.label;
            }
        },

        get filteredCctvs() {
            if (!this.searchQuery) return this.cctvs;
            const q = this.searchQuery.toLowerCase();
            return this.cctvs.filter(
                (x) =>
                    x.label.toLowerCase().includes(q) ||
                    (x.dapur_name || '').toLowerCase().includes(q),
            );
        },

        selectCctv(cctv) {
            this.selectedId = String(cctv.id);
            this.searchQuery = cctv.label;
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },

        focusFirstOption() {
            if (this.filteredCctvs.length > 0) this.highlightedIndex = 0;
        },

        focusLastOption() {
            if (this.filteredCctvs.length > 0) this.highlightedIndex = this.filteredCctvs.length - 1;
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredCctvs.length) {
                this.selectCctv(this.filteredCctvs[this.highlightedIndex]);
            }
        },
    }));
});
