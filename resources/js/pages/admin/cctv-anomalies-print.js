// Halaman cetak laporan anomali CCTV: buka dialog cetak setelah halaman siap
document.addEventListener('alpine:init', () => {
    Alpine.data('autoPrint', () => ({
        init() {
            if (document.readyState === 'complete') {
                window.print();
                return;
            }
            window.addEventListener('load', () => window.print(), { once: true });
        },
    }));
});