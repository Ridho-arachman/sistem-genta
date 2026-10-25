@extends('kepala-dapur.layouts.app', ['pageTitle' => 'Detail Anomali: ' . $anomaly->cctv->display_name])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div><h2 class="text-2xl font-bold text-[var(--color-secondary)]">Detail Anomali CCTV</h2><p class="text-[var(--color-text-muted)] mt-1">{{ $anomaly->cctv->display_name }} - {{ $anomaly->cctv->dapur->nama }}</p></div>
            <a href="{{ route('kepala-dapur.cctv-anomalies.index') }}" class="btn-secondary">Kembali</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">CCTV</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->cctv->display_name }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Dapur</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->cctv->dapur->nama }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Area / Kamera</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->cctv->section ?? '-' }} / Cam {{ $anomaly->cctv->camera_number ?? '-' }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Severity</p><p class="font-semibold text-[var(--color-secondary)]"><span class="badge @if($anomaly->severity === 'tinggi') badge-danger @elseif($anomaly->severity === 'sedang') badge-warning @else badge-gray @endif">{{ ucfirst($anomaly->severity) }}</span></p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Status</p><p class="font-semibold text-[var(--color-secondary)]"><span class="badge @if($anomaly->status === 'baru') badge-danger @elseif($anomaly->status === 'diproses') badge-warning @else badge-success @endif">{{ ucfirst($anomaly->status) }}</span></p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Dilaporkan Oleh</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->admin->name }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Tanggal Laporan</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->created_at->format('d/m/Y H:i') }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Terakhir Diupdate</p><p class="font-semibold text-[var(--color-secondary)]">{{ $anomaly->updated_at->format('d/m/Y H:i') }}</p></div>
        </div>

        <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)] mb-2">Deskripsi Anomali</p><p class="text-[var(--color-secondary)] whitespace-pre-wrap">{{ $anomaly->description }}</p></div>
    </div>
</div>
@endsection
