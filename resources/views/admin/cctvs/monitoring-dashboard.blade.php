@extends('admin.layouts.app', ['pageTitle' => 'Monitoring CCTV'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>Monitoring CCTV</h1>
            <div class="muted">Status perangkat dan pratinjau feed kamera</div>
        </div>
    </div>

    <!-- Camera Grid - 2 columns fixed like demo, cards smaller -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach ($cctvs as $cctv)
        <div class="card overflow-hidden max-w-xs">
            <div class="p-2 border-b border-[var(--color-border)]">
                <div class="flex items-center justify-between mb-1">
                    <span class="cam-tag bg-black/70 text-white text-xs font-mono px-2 py-1 rounded" style="font-family: monospace;">
                        CAM-{{ str_pad($cctv->camera_number ?? 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="status-badge 
                        @if($cctv->status === 'online') ok
                        @elseif($cctv->status === 'offline') off
                        @else warn @endif">
                        @if($cctv->status === 'online') Status simulasi online
                        @elseif($cctv->status === 'offline') Status offline
                        @else Status perlu dicek @endif
                    </span>
                </div>
            </div>
            
            <div class="h-32 bg-[#0a0f0d] relative flex flex-col items-center justify-center text-white/60 p-2">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-1.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width: 1.5;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <p class="text-xs font-medium mb-1">Feed CCTV belum terhubung</p>
                    <p class="text-[10px] text-white/40">Contoh tampilan saja</p>
                </div>
                
                <!-- Area label overlay at bottom -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-1.5">
                    <p class="text-[10px] text-white/70 font-medium">{{ $cctv->section ?? 'Area tidak diset' }}</p>
                </div>
            </div>
            
            <div class="p-2 bg-[var(--color-surface)] border-t border-[var(--color-border)]">
                <p class="text-sm font-medium text-[var(--color-secondary)] mb-0.5">{{ $cctv->nama }}</p>
                <p class="text-xs text-[var(--color-text-muted)]">{{ $cctv->dapur->nama ?? 'Dapur tidak diketahui' }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Notice like demo -->
    <div class="notice p-4 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-[var(--color-text-muted)] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-[var(--color-text-muted)]">
                <p class="font-medium text-[var(--color-secondary)]">Live view sesungguhnya memerlukan integrasi</p>
                <p class="mt-1">Pratinjau feed hanya tersedia untuk kamera dengan HLS URL yang valid dan status Online. Integrasi penuh memerlukan NVR/IP camera, dukungan stream yang sesuai, akses jaringan aman, dan pengujian perangkat.</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.cam-tag {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-weight: 500;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.status-badge.ok {
    background: #dcfce7;
    color: #166534;
}
.status-badge.warn {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.off {
    background: #fee2e2;
    color: #991b1b;
}
.notice {
    background: var(--color-surface);
    border-color: var(--color-border);
}
</style>
@endpush