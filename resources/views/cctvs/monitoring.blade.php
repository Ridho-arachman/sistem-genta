<x-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Monitoring CCTV</h1>
                <p class="text-zinc-400 text-sm">{{ $dapur->nama }} &mdash; {{ $dapur->cctvs->count() }} kamera</p>
            </div>
            <a href="{{ route('admin.dapurs.show', $dapur) }}" class="text-zinc-400 hover:text-white text-sm">← Kembali ke Dapur</a>
        </div>

        @if ($dapur->cctvs->isEmpty())
            <div class="bg-surface border border-zinc-800 rounded-xl p-12 text-center">
                <p class="text-zinc-500">Belum ada CCTV terdaftar untuk dapur ini.</p>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.cctvs.create', ['dapur_id' => $dapur->id]) }}" class="mt-4 inline-block text-green-400 hover:text-green-300 font-medium">+ Tambah CCTV</a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($dapur->cctvs as $cctv)
                    <div class="bg-surface border border-zinc-800 rounded-xl overflow-hidden shadow-sm flex flex-col h-[300px]">
                        <div class="relative bg-black aspect-video">
                            @if ($cctv->status === 'online')
                                <video
                                    id="player-{{ $cctv->id }}"
                                    class="video-js vjs-big-play-centered w-full h-full"
                                    controls
                                    playsinline
                                    muted
                                    data-rtsp-url="{{ $cctv->rtsp_url }}"
                                    data-hls-url="{{ $cctv->hls_url ?? '' }}"
                                ></video>
                                <div class="absolute top-3 left-3 bg-green-500/20 border border-green-500/40 text-green-400 text-xs px-2 py-1 rounded">
                                    ● LIVE
                                </div>
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-zinc-600">
                                    <svg class="w-16 h-16 mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14.5M22 13h-4v-4m0 0l-3-3m3 3l-3 3M2 13h4v4m0 0l3-3m-3 3l3 3"></path></svg>
                                    <p class="text-sm text-zinc-400">{{ ucfirst($cctv->status) }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-medium text-white truncate">{{ $cctv->nama }}</h3>
                            <p class="text-xs text-zinc-500">{{ $cctv->ip_address ?? '-' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
        <link href="https://vjs.zencdn.net/8.21.0/video-js.css" rel="stylesheet">
        <script src="https://vjs.zencdn.net/8.21.0/video.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@videojs/http-streaming@3.16.2/dist/videojs-http-streaming.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const players = document.querySelectorAll('video.video-js[data-rtsp-url]');
                
                players.forEach(function(videoEl) {
                    const rtspUrl = videoEl.dataset.rtspUrl;
                    const hlsUrl = videoEl.dataset.hlsUrl;
                    const playerId = videoEl.id;
                    
                    // For now, we prioritize HLS if provided, otherwise show RTSP notice
                    // RTSP requires a media server (MediaMTX, SRS, etc.) to convert to HLS/WebRTC
                    if (hlsUrl) {
                        // Initialize Video.js with HLS
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
                        // RTSP not natively supported in browsers
                        // Show informative placeholder
                        const container = videoEl.parentElement;
                        container.innerHTML = `
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-zinc-400 p-4 text-center">
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
            });
        </script>
    @endpush
</x-layout>