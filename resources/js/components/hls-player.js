// Pemutar HLS (cctvs/index & cctvs/show) — hls.js dimuat lazy agar halaman lain tetap ringan
document.addEventListener('alpine:init', () => {
    Alpine.data('hlsPlayer', (src, autoplay = false, initialStatus = 'offline') => ({
        error: false,
        initialStatus,

        get status() {
            return this.error ? 'offline' : this.initialStatus;
        },

        async init() {
            const video = this.$el.querySelector('video');
            if (! video) {
                return;
            }
            const showError = () => {
                this.error = true;
                video.classList.add('hidden');
            };

            const nativeHls = video.canPlayType('application/vnd.apple.mpegurl');

            let Hls = null;
            try {
                ({ default: Hls } = await import('hls.js'));
            } catch (e) {
                if (!nativeHls) {
                    showError();
                    return;
                }
            }

            if (Hls?.isSupported()) {
                const hls = new Hls();
                let networkRetries = 0;
                hls.loadSource(src);
                hls.attachMedia(video);

                if (autoplay) {
                    hls.on(Hls.Events.MANIFEST_PARSED, () => {
                        video.play().catch(() => {});
                    });
                }

                hls.on(Hls.Events.ERROR, (event, data) => {
                    if (!data.fatal) return;
                    if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                        // URL baru butuh waktu sampai source MediaMTX siap;
                        // beri kesempatan sebelum menampilkan "Feed tidak tersedia".
                        if (networkRetries++ < 8) {
                            setTimeout(() => hls.startLoad(), 1500);
                        } else {
                            hls.destroy();
                            showError();
                        }
                    } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                        hls.recoverMediaError();
                    } else {
                        hls.destroy();
                        showError();
                    }
                });
            } else if (nativeHls) {
                video.src = src;
                video.onerror = showError;
                if (autoplay) {
                    video.addEventListener('loadedmetadata', () => {
                        video.play().catch(() => {});
                    });
                }
            } else {
                showError();
            }
        },
    }));
});
