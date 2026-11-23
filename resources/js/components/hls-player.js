// Pemutar CCTV (cctvs/index & cctvs/show) — pakai WebRTC/WHEP lebih dulu,
// lalu fallback ke HLS (hls.js dimuat lazy agar halaman lain tetap ringan).
document.addEventListener('alpine:init', () => {
    Alpine.data('cctvPlayer', (src, autoplay = false, initialStatus = 'offline', webRtcSrc = '') => ({
        error: false,
        playing: false,
        autoplay,
        initialStatus,
        webRtcSrc,
        pc: null,
        whepSessionUrl: '',

        get status() {
            if (this.error) return 'offline';
            if (this.initialStatus === 'maintenance') return 'maintenance';
            if (! this.src) return this.initialStatus;
            return this.playing ? 'online' : 'connecting';
        },

        destroyed() {
            this.stopWebRtc();
        },

        showError() {
            this.error = true;
            const video = this.$el.querySelector('video');
            if (video) video.classList.add('hidden');
        },

        async init() {
            const video = this.$el.querySelector('video');
            if (! video) return;

            video.addEventListener('playing', () => {
                this.playing = true;
            });

            if (this.webRtcSrc && typeof RTCPeerConnection !== 'undefined') {
                this.startWebRtc().catch(() => this.startHls());
            } else {
                this.startHls();
            }
        },

        async startWebRtc() {
            const video = this.$el.querySelector('video');
            const pc = new RTCPeerConnection();
            this.pc = pc;
            pc.addTransceiver('video', { direction: 'recvonly' });
            pc.addTransceiver('audio', { direction: 'recvonly' });

            pc.ontrack = (event) => {
                if (video && event.streams[0]) {
                    video.srcObject = event.streams[0];
                }
            };
            pc.oniceconnectionstatechange = () => {
                if (pc.iceConnectionState === 'failed') {
                    this.showError();
                }
            };

            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);
            await new Promise((resolve) => {
                const onComplete = () => resolve();
                pc.addEventListener('icegatheringstatechange', () => {
                    if (pc.iceGatheringState === 'complete') onComplete();
                });
                if (pc.iceGatheringState === 'complete') onComplete();
            });

            const response = await fetch(this.webRtcSrc, {
                method: 'POST',
                headers: { 'Content-Type': 'application/sdp' },
                body: pc.localDescription.sdp,
            });

            if (! response.ok) {
                this.pc = null;
                throw new Error(`WHEP status ${response.status}`);
            }

            this.whepSessionUrl = response.headers.get('Location') || '';
            const answer = await response.text();
            await pc.setRemoteDescription({ type: 'answer', sdp: answer });
            video && video.play().catch(() => {});
        },

        stopWebRtc() {
            if (this.pc) {
                this.pc.close();
                this.pc = null;
            }
            if (this.whepSessionUrl) {
                fetch(this.whepSessionUrl, { method: 'DELETE' }).catch(() => {});
                this.whepSessionUrl = '';
            }
        },

        async startHls() {
            const video = this.$el.querySelector('video');
            const nativeHls = video.canPlayType('application/vnd.apple.mpegurl');

            let Hls = null;
            try {
                ({ default: Hls } = await import('hls.js'));
            } catch (e) {
                if (! nativeHls) {
                    this.showError();
                    return;
                }
            }

            if (Hls?.isSupported()) {
                const hls = new Hls();
                let networkRetries = 0;
                hls.loadSource(this.src);
                hls.attachMedia(video);

                if (this.autoplay) {
                    hls.on(Hls.Events.MANIFEST_PARSED, () => {
                        video.play().catch(() => {});
                    });
                }

                hls.on(Hls.Events.ERROR, (event, data) => {
                    if (! data.fatal) return;
                    if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                        // Source MediaMTX butuh waktu sampai stream siap;
                        // beri kesempatan sebelum menampilkan "Feed tidak tersedia".
                        if (networkRetries++ < 20) {
                            setTimeout(() => hls.startLoad(), 1000);
                        } else {
                            hls.destroy();
                            this.showError();
                        }
                    } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                        hls.recoverMediaError();
                    } else {
                        hls.destroy();
                        this.showError();
                    }
                });
            } else if (nativeHls) {
                video.src = this.src;
                video.onerror = () => this.showError();
                if (this.autoplay) {
                    video.addEventListener('loadedmetadata', () => {
                        video.play().catch(() => {});
                    });
                }
            } else {
                this.showError();
            }
        },
    }));
});
