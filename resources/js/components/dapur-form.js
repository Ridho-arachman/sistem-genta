// Cascade kabupaten/kota → kecamatan (dipakai dapurs/create & dapurs/edit)
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurForm', (allKecamatan, kabupaten, kecamatan) => ({
        form: {
            kabupaten_kota: kabupaten || '',
            kecamatan: kecamatan || '',
        },
        kecamatanOptions: [],
        allKecamatan: allKecamatan || {},

        onKabupatenChange(kab) {
            this.form.kabupaten_kota = kab;
            this.form.kecamatan = '';
            this.kecamatanOptions = kab ? this.allKecamatan[kab] || [] : [];
        },

        getKecamatanOptionsHtml() {
            if (this.kecamatanOptions.length === 0) {
                return '<option value="">-- Pilih Kabupaten/Kota dulu --</option>';
            }
            return (
                '<option value="">-- Pilih Kecamatan --</option>' +
                this.kecamatanOptions.map((kec) => `<option value="${kec}">${kec}</option>`).join('')
            );
        },

        init() {
            if (this.form.kabupaten_kota) {
                this.kecamatanOptions = this.allKecamatan[this.form.kabupaten_kota] || [];
            }
        },
    }));
});
