// Modal preview foto (laporan detail + form inspeksi)
document.addEventListener('alpine:init', () => {
    Alpine.data('photoPreview', () => ({
        src: '',
        isOpen: false,

        open(url) {
            if (!url) {
                return;
            }
            this.src = url;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },
    }));
});
