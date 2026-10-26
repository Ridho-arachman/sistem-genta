<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $cctv->nama }}</h1>
                <p class="text-zinc-400 text-sm">Dapur: {{ $cctv->dapur->nama }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.cctvs.index') }}" class="text-zinc-400 hover:text-white px-3 py-1 rounded text-sm border border-zinc-700">Kembali</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.cctvs.edit', $cctv) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Edit</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-white mb-4">Detail Kamera</h3>
                <p class="text-zinc-300 text-sm mb-2"><strong>IP Address:</strong> {{ $cctv->ip_address ?? '-' }}</p>
                <p class="text-zinc-300 text-sm mb-2"><strong>RTSP URL:</strong> {{ $cctv->rtsp_url ?? '-' }}</p>
                @if ($cctv->hls_url)
                    <p class="text-zinc-300 text-sm mb-2"><strong>HLS URL:</strong> {{ $cctv->hls_url }}</p>
                @endif
                <p class="text-zinc-300 text-sm"><strong>Status:</strong>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        @if($cctv->status === 'online') bg-green-500/20 text-green-400
                        @elseif($cctv->status === 'offline') bg-red-500/20 text-red-400
                        @else bg-yellow-500/20 text-yellow-400 @endif">
                        {{ ucfirst($cctv->status) }}
                    </span>
                </p>
            </div>

            <div class="bg-surface border border-zinc-800 rounded-xl p-6 shadow-sm md:col-span-2">
                <h3 class="text-lg font-bold text-white mb-4">Live Preview</h3>
                <div class="bg-black rounded-lg aspect-video">
                    @if ($cctv->status === 'online')
                        <video
                            id="player-detail-{{ $cctv->id }}"
                            class="video-js vjs-big-play-centered w-full h-full"
                            controls
                            playsinline
                            muted
                            data-rtsp-url="{{ $cctv->rtsp_url }}"
                            data-hls-url="{{ $cctv->hls_url ?? '' }}"
                        ></video>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-zinc-600 h-full">
                            <svg class="w-16 h-16 mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14.5M22 13h-4v-4m0 0l-3-3m3 3l-3 3M2 13h4v4m0 0l3-3m-3 3l3 3"></path></svg>
                            <p class="text-sm text-zinc-400">{{ ucfirst($cctv->status) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://vjs.zencdn.net/8.21.0/video-js.css" rel="stylesheet">
        <script src="https://vjs.zencdn.net/8.21.0/video.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@videojs/http-streaming@3.16.2/dist/videojs-http-streaming.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const videoEl = document.querySelector('video.video-js[data-rtsp-url]');
                if (!videoEl) return;
                
                const rtspUrl = videoEl.dataset.rtspUrl;
                const hlsUrl = videoEl.dataset.hlsUrl;
                const playerId = videoEl.id;
                
                if (hlsUrl) {
                    const player = videojs(playerId, {
                        fluid: true,
                        sources: [{
                            src: hlsUrl,
                            type: 'application/x-mpegURL'
                        }],
                        playbackRates: [0.5, 1, 1.5, 2],
                        controlBar: {
                            children: [
                                'playToggle',
                                'volumePanel',
                                'currentTimeDisplay',
                                'progressControl',
                                'durationDisplay',
                                'fullscreenToggle'
                            ]
                        }
                    });
                } else if (rtspUrl) {
                    const container = videoEl.parentElement;
                    container.innerHTML = `
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-zinc-400 p-4 text-center h-full">
                            <svg class="w-12 h-12 mb-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm mb-2">Stream RTSP</p>
                            <p class="text-xs text-zinc-500 max-w-xs">
                                RTSP tidak didukung browser langsung. 
                                Konversi ke HLS via MediaMTX/SRS, lalu isi kolom HLS URL.
                            </p>
                        </div>
                    `;
                }
            });
        </script>
    @endpush
</x-layout>