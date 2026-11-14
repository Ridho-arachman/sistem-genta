// Dashboard admin: filter rentang tanggal untuk ekspor inspeksi
document.addEventListener('alpine:init', () => {
    Alpine.data('exportForm', (startDate = '', endDate = '') => ({
        startDate,
        endDate,
        loading: false,
        activePreset: '',
        presets: {
            today: { start: 0, end: 0 },
            week: { start: -6, end: 0 },
            month: { start: -29, end: 0 },
            last_month: { start: -59, end: -30 },
        },

        get dateError() {
            return !!(this.startDate && this.endDate && this.startDate > this.endDate);
        },

        get startHint() {
            return this.startDate
                ? 'Terseleksi: ' + this.formatDisplay(new Date(this.startDate + 'T00:00:00'))
                : 'Belum dipilih';
        },

        get endHint() {
            return this.endDate
                ? 'Terseleksi: ' + this.formatDisplay(new Date(this.endDate + 'T00:00:00'))
                : 'Belum dipilih';
        },

        init() {
            if (!this.startDate || !this.endDate) return;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const start = new Date(this.startDate + 'T00:00:00');
            const end = new Date(this.endDate + 'T00:00:00');
            const diffStart = Math.round((start - today) / 86400000);
            const diffEnd = Math.round((end - today) / 86400000);
            for (const [key, range] of Object.entries(this.presets)) {
                if (diffStart === range.start && diffEnd === range.end) {
                    this.activePreset = key;
                    return;
                }
            }
        },

        formatDate(date) {
            return date.toISOString().split('T')[0];
        },

        formatDisplay(date) {
            return date.toLocaleDateString('id-ID', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric',
            });
        },

        presetClass(preset) {
            const base =
                'px-[14px] py-2 rounded-full border text-xs font-medium transition-all cursor-pointer whitespace-nowrap';
            return this.activePreset === preset
                ? base + ' bg-[var(--color-primary)] text-white border-[var(--color-primary)]'
                : base +
                      ' bg-white text-[var(--color-text-muted)] border-[var(--color-border)] hover:text-[var(--color-primary)] hover:border-[var(--color-primary)]/50';
        },

        applyPreset(preset) {
            const range = this.presets[preset];
            if (!range) return;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const start = new Date(today);
            start.setDate(today.getDate() + range.start);
            const end = new Date(today);
            end.setDate(today.getDate() + range.end);
            this.startDate = this.formatDate(start);
            this.endDate = this.formatDate(end);
            this.activePreset = preset;
        },

        setCustom() {
            this.activePreset = 'custom';
            this.$nextTick(() => {
                document.getElementById('start_date')?.focus();
            });
        },

        clearFilters() {
            this.startDate = '';
            this.endDate = '';
            this.activePreset = '';
            const format = document.getElementById('format');
            if (format) format.value = 'xlsx';
            this.$nextTick(() => {
                document.getElementById('start_date')?.focus();
            });
        },
    }));
});
