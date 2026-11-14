// Landing: tandai section [data-reveal] yang masuk viewport
document.addEventListener('alpine:init', () => {
    Alpine.data('revealSections', () => ({
        init() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        entry.target.setAttribute('data-visible', '');
                        observer.unobserve(entry.target);
                    });
                },
                { threshold: 0.1, rootMargin: '0px 0px -10% 0px' },
            );

            this.$el.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
        },
    }));
});
