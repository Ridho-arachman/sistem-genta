document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('[data-action="toggle-password"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.querySelector('svg:not(.hidden)').classList.add('hidden');
                this.querySelector('svg.hidden').classList.remove('hidden');
            } else {
                input.type = 'password';
                this.querySelector('svg:not(.hidden)').classList.add('hidden');
                this.querySelector('svg.hidden').classList.remove('hidden');
            }
        });
    });

    // Open photo preview (laporan/show.blade.php)
    const modal = document.getElementById('laporanPhotoPreviewModal');
    const previewImage = document.getElementById('laporanPreviewImage');

    if (modal && previewImage) {
        document.querySelectorAll('[data-action="open-photo-preview"]').forEach(el => {
            el.addEventListener('click', function() {
                const src = this.dataset.photoUrl;
                previewImage.src = src;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeLaporanPhotoPreview() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on modal backdrop click
        document.getElementById('laporanPhotoPreviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLaporanPhotoPreview();
            }
        });

        // Close button
        document.querySelectorAll('[data-action="close-photo-preview"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeLaporanPhotoPreview();
            });
        });
    }

    // Handle table row click for navigation (laporan/index.blade.php)
    document.querySelectorAll('[data-action="navigate-to-report"]').forEach(row => {
        row.addEventListener('click', function() {
            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });
});
