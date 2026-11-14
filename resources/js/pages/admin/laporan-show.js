// Halaman detail laporan: lightbox foto + editor catatan (Quill dimuat lazy)
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanDetail', (status = '', currentStatus = '') => ({
        photoPreview: false,
        photoSrc: '',
        status,
        currentStatus,
        editor: null,
        labels: {
            diperiksa: 'Catatan Verifikasi Pemeriksaan',
            temuan: 'Catatan Temuan & Tindak Lanjut',
            'tindak lanjut': 'Catatan Tindak Lanjut',
            selesai: 'Catatan Selesai',
        },

        get isSelesai() {
            return this.status === 'selesai';
        },

        get locked() {
            return (this.currentStatus || '').toLowerCase() === 'selesai';
        },

        get catatanLabel() {
            return this.labels[this.status] || 'Catatan Admin';
        },

        openPhoto(src) {
            this.photoSrc = src;
            this.photoPreview = true;
        },

        async init() {
            try {
                const [{ default: Quill }] = await Promise.all([import('quill'), import('quill/dist/quill.snow.css')]);

                this.editor = new Quill('#catatan-admin', {
                    theme: 'snow',
                    placeholder: 'Tuliskan catatan verifikasi, temuan, atau tindak lanjut admin...',
                    modules: {
                        toolbar: [
                            [{ header: [false, 1, 2, 3] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link'],
                        ],
                    },
                });
            } catch (e) {
                console.error('Quill init error:', e);
            }
        },
    }));
});
