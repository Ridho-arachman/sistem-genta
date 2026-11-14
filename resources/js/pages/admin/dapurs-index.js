// Halaman daftar dapur: pencarian, filter wilayah, dan pindah ke detail
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurList', (allKecamatan = {}, dapurs = [], detailUrl = '') => ({
        searchQuery: '',
        filter: {
            kabupaten_kota: '',
            kecamatan: '',
        },
        allKecamatan,
        dapurs,
        detailUrl,

        get kecamatanOptions() {
            const kab = this.filter.kabupaten_kota;
            return kab ? this.allKecamatan[kab] || [] : [];
        },

        getKecamatanOptionsHtml() {
            let html = '<option value="">-- Pilih Kecamatan --</option>';
            for (const kec of this.kecamatanOptions) {
                const selected = this.filter.kecamatan === kec ? 'selected' : '';
                html += `<option value="${kec}" ${selected}>${kec}</option>`;
            }
            return html;
        },

        onKabupatenChange(kab) {
            this.filter.kabupaten_kota = kab;
            this.filter.kecamatan = '';
        },

        get filteredDapurs() {
            let result = this.dapurs;

            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(
                    (d) =>
                        d.nama.toLowerCase().includes(q) ||
                        (d.lokasiPengawas && d.lokasiPengawas.toLowerCase().includes(q)) ||
                        (d.provinsi && d.provinsi.toLowerCase().includes(q)) ||
                        (d.kabupaten_kota && d.kabupaten_kota.toLowerCase().includes(q)) ||
                        (d.kecamatan && d.kecamatan.toLowerCase().includes(q)) ||
                        d.pengawas.some((p) => p.name.toLowerCase().includes(q)) ||
                        (d.kepalaDapur && d.kepalaDapur.name.toLowerCase().includes(q)),
                );
            }

            if (this.filter.kabupaten_kota) {
                result = result.filter((d) => d.kabupaten_kota === this.filter.kabupaten_kota);
            }
            if (this.filter.kecamatan) {
                result = result.filter((d) => d.kecamatan === this.filter.kecamatan);
            }

            return result;
        },

        goToDetail(id) {
            window.location.href = this.detailUrl.replace(':id', id);
        },

        // Filter reaktif lewat getter; dipanggil dari @input/@change view.
        filterDapurs() {},
        applyFilters() {},
    }));
});
