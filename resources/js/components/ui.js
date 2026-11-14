// State UI global: hamburger ↔ sidebar (dipakai layout admin & pengawas)
document.addEventListener('alpine:init', () => {
    Alpine.store('ui', {
        sidebarOpen: false,
        toggle() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        close() {
            this.sidebarOpen = false;
        },
    });
});
