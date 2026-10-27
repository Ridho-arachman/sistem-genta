@extends('admin.layouts.app', ['pageTitle' => 'Detail CCTV: ' . $cctv->display_name])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-secondary)]">{{ $cctv->display_name }}</h2>
                <p class="text-[var(--color-text-muted)] mt-1">{{ $cctv->dapur->nama }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kepala-dapur.cctvs.index') }}" class="btn-action btn-action-secondary">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Dapur</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $cctv->dapur->nama }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Area / Section</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $cctv->section ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Nomor Kamera</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $cctv->camera_number ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">IP Address</p>
                <p class="font-semibold text-[var(--color-secondary)] font-mono text-sm">{{ $cctv->ip_address ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Status</p>
                <p class="font-semibold text-[var(--color-secondary)]">
                    <span class="badge 
                        @if($cctv->status === 'online') badge-success
                        @elseif($cctv->status === 'offline') badge-danger
                        @else badge-warning @endif">
                        {{ ucfirst($cctv->status) }}
                    </span>
                </p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">RTSP URL</p>
                <p class="font-semibold text-[var(--color-secondary)] font-mono text-sm truncate">{{ $cctv->rtsp_url ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">HLS URL</p>
                <p class="font-semibold text-[var(--color-secondary)] font-mono text-sm truncate">{{ $cctv->hls_url ?? '-' }}</p>
            </div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)]">Dibuat</p>
                <p class="font-semibold text-[var(--color-secondary)]">{{ $cctv->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Streaming Preview --}}
        @if ($cctv->hls_url)
        <div class="border-t border-[var(--color-border)] pt-6">
            <h3 class="text-lg font-semibold mb-4">Preview Streaming (HLS)</h3>
            <div class="aspect-video bg-black rounded-lg overflow-hidden">
                <video id="hlsPlayer" controls class="w-full h-full" crossorigin="anonymous"></video>
            </div>
        </div>
        @endif

        {{-- Anomaly History --}}
        @if ($cctv->anomalyReports->count() > 0)
        <div class="border-t border-[var(--color-border)] pt-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Riwayat Anomali</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface)]">
                            <th class="table-th">Tanggal</th>
                            <th class="table-th">Deskripsi</th>
                            <th class="table-th">Severity</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Dilaporkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($cctv->anomalyReports as $anomaly)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td">{{ $anomaly->created_at->format('d/m/Y H:i') }}</td>
                            <td class="table-td max-w-xs truncate">{{ $anomaly->description }}</td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($anomaly->severity === 'tinggi') badge-danger
                                    @elseif($anomaly->severity === 'sedang') badge-warning
                                    @else badge-gray @endif">
                                    {{ ucfirst($anomaly->severity) }}
                                </span>
                            </td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($anomaly->status === 'baru') badge-danger
                                    @elseif($anomaly->status === 'diproses') badge-warning
                                    @else badge-success @endif">
                                    {{ ucfirst($anomaly->status) }}
                                </span>
                            </td>
                            <td class="table-td">{{ $anomaly->admin->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@if ($cctv->hls_url)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var video = document.getElementById('hlsPlayer');
        var videoSrc = '{{ $cctv->hls_url }}';
        
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play().catch(function() {
                    // Autoplay blocked, user needs to click play
                });
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', function() {
                video.play().catch(function() {});
            });
        }
    });
</script>
@endpush
@endif