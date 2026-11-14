// Select status yang minta konfirmasi sebelum submit (sp-warnings & cctv-anomalies)
document.addEventListener('alpine:init', () => {
    Alpine.data('confirmSubmit', () => ({
        confirmStatus(message) {
            return window.confirm(message);
        },
    }));
});
