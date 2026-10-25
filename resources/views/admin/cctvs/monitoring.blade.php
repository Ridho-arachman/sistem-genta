@extends('admin.layouts.app', ['pageTitle' => 'Monitoring CCTV: ' . $dapur->nama])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Monitoring CCTV: {{ $dapur->nama }}</h2>
            <p class="text-[var(--color-text-muted)]">{{ $dapur->cctvs->count() }} kamera</p>
        </div>
        <a href="{{ route('admin.cctvs.index') }}" class="btn-secondary">Kembali ke Daftar</a>
    </div>

    @if ($dapur->cctvs->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($dapur->cctvs as $cctv)
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-[var(--color-border)] flex items-center justify-between">
                    <h3 class="font-semibold text-[var(--color-secondary)]">{{ $cctv->display_name }}</h3>
                    <span class="badge 
                        @if($cctv->status === 'online') badge-success
                        @elseif($cctv->status === 'offline') badge-danger
                        @else badge-warning @endif">
                        {{ ucfirst($cctv->status) }}
                    </span>
                </div>
                <div class="aspect-video bg-black relative">
                    @if ($cctv->hls_url && $cctv->status === 'online')
                        <video class="w-full h-full" controls crossorigin="anonymous" data-hls="{{ $cctv->hls_url }}"></video>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-white">
                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <p class="text-sm">{{ $cctv->status === 'online' ? 'HLS URL tidak diset' : ucfirst($cctv->status) }}</p>
                        </div>
                    @endif
                </div>
                <div class="p-4 bg-[var(--color-surface)] border-t border-[var(--color-border)]">
                    <div class="flex items-center justify-between text-sm text-[var(--color-text-muted)]">
                        <span>{{ $cctv->section ?? 'Area tidak diset' }}</span>
                        <span>Cam {{ $cctv->camera_number ?? '-' }}</span>
                    </div>
                    @if ($cctv->ip_address)
                    <div class="text-xs text-[var(--color-text-muted)] font-mono mt-1">{{ $cctv->ip_address }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Tidak Ada CCTV</h3>
            <p class="text-[var(--color-text-muted)] mt-1">Dapur ini belum memiliki kamera CCTV</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('video[data-hls]').forEach(function(video) {
            var src = video.dataset.hls;
            if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(src);
                hls.attachMedia(video);
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = src;
            }
        });
    });
</script>
@endpush