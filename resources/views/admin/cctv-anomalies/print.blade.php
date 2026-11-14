@extends('admin.layouts.app', ['pageTitle' => 'Cetak Laporan Anomali CCTV'])

@php
    $severityBadge = [
        'tinggi' => 'badge-error',
        'sedang' => 'badge-warning',
        'rendah' => 'badge-neutral',
    ];
    $statusBadge = [
        'baru' => 'badge-error',
        'diproses' => 'badge-warning',
        'selesai' => 'badge-success',
    ];
@endphp

@section('page_content')
<div class="max-w-5xl mx-auto" x-data="autoPrint">
    {{-- Header --}}
    <div class="text-center mb-8 pb-6 border-b-2 border-[var(--color-primary)]">
        <h1 class="text-2xl font-bold text-[var(--color-secondary)]">GENTA Pangan Banten</h1>
        <p class="text-[var(--color-text-muted)] mt-1">Laporan Anomali CCTV</p>
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-1 mt-3 text-sm text-[var(--color-text-muted)]">
            <span>Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card overflow-x-auto print:border print:border-[var(--color-border)] print:shadow-none">
        <table class="table table-sm w-full">
            <thead class="bg-[var(--color-surface)] border-b-2 border-[var(--color-border)]">
                <tr>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">No</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Tanggal</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">CCTV</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Dapur</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Area/Kamera</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Deskripsi</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Severity</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Status</th>
                    <th class="text-left font-semibold text-[var(--color-secondary)]">Dilaporkan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
                @forelse ($anomalies as $index => $anomaly)
                <tr>
                    <td class="text-[var(--color-text-muted)]">{{ $index + 1 }}</td>
                    <td class="text-[var(--color-secondary)]">{{ $anomaly->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-[var(--color-secondary)]">{{ $anomaly->cctv->display_name }}</td>
                    <td class="text-[var(--color-secondary)]">{{ $anomaly->cctv->dapur->nama }}</td>
                    <td class="text-[var(--color-secondary)]">{{ $anomaly->cctv->section ?? '-' }} / Cam {{ $anomaly->cctv->camera_number ?? '-' }}</td>
                    <td class="text-[var(--color-secondary)] max-w-xs">{{ $anomaly->description }}</td>
                    <td><span class="badge {{ $severityBadge[$anomaly->severity] ?? 'badge-ghost' }}">{{ ucfirst($anomaly->severity) }}</span></td>
                    <td><span class="badge {{ $statusBadge[$anomaly->status] ?? 'badge-ghost' }}">{{ ucfirst($anomaly->status) }}</span></td>
                    <td class="text-[var(--color-secondary)]">{{ $anomaly->admin->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-10 text-center text-[var(--color-text-muted)]">Tidak ada anomali pada periode yang dipilih</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="mt-8 pt-6 border-t border-[var(--color-border)] text-center text-sm text-[var(--color-text-muted)]">
        <p>Dicetak otomatis oleh sistem GENTA Pangan Banten</p>
    </div>
</div>
@endsection