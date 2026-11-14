@extends('admin.layouts.app', ['pageTitle' => 'Detail CCTV: ' . $cctv->display_name])

@section('page_content')

<x-admin::page-heading title="Detail CCTV" subtitle="Informasi dan pratinjau kamera CCTV" />

    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-secondary)]">{{ $cctv->display_name }}</h2>
                <p class="text-[var(--color-text-muted)] mt-1">{{ $cctv->dapur->nama }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.cctvs.edit', $cctv) }}" class="btn btn-secondary">Edit</a>
                <a href="{{ route('admin.cctv-anomalies.create') }}?cctv_id={{ $cctv->id }}" class="btn btn-secondary flex items-center gap-2">
                    <span>!</span>
                    <span>Laporkan Anomali</span>
                </a>
                <a href="{{ route('admin.cctvs.index') }}" class="btn btn-secondary">Kembali</a>
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
                        @elseif($cctv->status === 'offline') badge-error
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
            <div class="aspect-video bg-black rounded-lg overflow-hidden" x-data="hlsPlayer(@js($cctv->hls_url), true)">
                <video controls class="block h-full w-full object-cover" crossorigin="anonymous"></video>
            </div>
        </div>
        @endif

        {{-- Anomaly History --}}
        @if ($cctv->anomalyReports->count() > 0)
        <div class="border-t border-[var(--color-border)] pt-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Riwayat Anomali</h3>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Dilaporkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cctv->anomalyReports as $anomaly)
                        <tr>
                            <td>{{ $anomaly->created_at->format('d/m/Y H:i') }}</td>
                            <td class="max-w-xs truncate">{{ $anomaly->description }}</td>
                            <td>
                                <span class="badge 
                                    @if($anomaly->severity === 'tinggi') badge-error
                                    @elseif($anomaly->severity === 'sedang') badge-warning
                                    @else badge-secondary @endif">
                                    {{ ucfirst($anomaly->severity) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($anomaly->status === 'baru') badge-error
                                    @elseif($anomaly->status === 'diproses') badge-warning
                                    @else badge-success @endif">
                                    {{ ucfirst($anomaly->status) }}
                                </span>
                            </td>
                            <td>{{ $anomaly->admin->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

@endsection