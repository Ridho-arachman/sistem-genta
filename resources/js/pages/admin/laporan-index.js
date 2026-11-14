// Halaman daftar laporan: filter pencarian, status, lokasi, dan tanggal
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanFilter', (allRows = []) => ({
        search: '',
        statusFilter: '',
        lokasiFilter: '',
        dateFrom: '',
        dateTo: '',
        allRows,
        filteredRows: [],

        init() {
            this.filteredRows = [...this.allRows];
        },

        get uniqueStatuses() {
            return [...new Set(this.allRows.map((r) => r.status))];
        },

        get uniqueLokasi() {
            return [...new Set(this.allRows.map((r) => r.lokasi))];
        },

        get hasActiveFilters() {
            return this.search || this.statusFilter || this.lokasiFilter || this.dateFrom || this.dateTo;
        },

        filterRows() {
            let rows = [...this.allRows];

            if (this.search) {
                const q = this.search.toLowerCase();
                rows = rows.filter(
                    (r) => r.lokasi.toLowerCase().includes(q) || r.pengawas.toLowerCase().includes(q),
                );
            }

            if (this.statusFilter) {
                rows = rows.filter((r) => r.status === this.statusFilter);
            }

            if (this.lokasiFilter) {
                rows = rows.filter((r) => r.lokasi === this.lokasiFilter);
            }

            if (this.dateFrom) {
                rows = rows.filter((r) => r.date >= this.dateFrom);
            }
            if (this.dateTo) {
                rows = rows.filter((r) => r.date <= this.dateTo);
            }

            this.filteredRows = rows;
        },

        resetFilters() {
            this.search = '';
            this.statusFilter = '';
            this.lokasiFilter = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.filteredRows = [...this.allRows];
        },
    }));
});
