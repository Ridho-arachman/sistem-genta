@extends('pengawas.layouts.app', ['pageTitle' => 'Dashboard Pengawas'])

@section('content')
<div class="min-h-screen bg-[var(--color-background)]">
    {{-- Standard Header --}}
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>Dashboard Pengawas</h1>
            <div class="muted">Kondisi operasional dapur dan pelaporan · {{ now()->translatedFormat('l, d F Y') }}</div>
        </div>
    </div>

    {{-- Tugas hari ini --}}
    <section class="bg-white rounded-xl shadow-sm mx-2 sm:mx-0 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <span class="text-sm font-bold text-[var(--color-text)]">Tugas hari ini</span>
            <span class="text-sm font-bold text-[var(--color-primary)] bg-[var(--color-primary)]/10 px-3 py-1 rounded-full">{{ $dapurs->count() }} tugas</span>
        </div>

        @forelse ($dapurs as $dapur)
            <div class="flex flex-col sm:flex-row gap-4 p-4 border-b border-[var(--color-border)] last:border-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
                        <div>
                            <div class="text-sm font-semibold text-[var(--color-text)]">{{ $dapur->nama }}</div>
                            <div class="text-xs text-[var(--color-text-muted)]">{{ $dapur->lokasi ?? 'Wilayah' }} · Pemeriksaan hari ini</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('pengawas.absensi.create', ['dapur' => $dapur->id]) }}" class="flex-1 px-4 py-2.5 text-center border border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg text-sm font-medium hover:bg-[var(--color-primary)]/5 transition-colors">Lihat Lokasi</a>
                    <a href="{{ route('pengawas.laporan.create') }}" class="flex-1 px-4 py-2.5 text-center bg-[var(--color-primary)] text-white rounded-lg text-sm font-medium hover:bg-[var(--color-primary-dark)] transition-colors">Mulai Inspeksi</a>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-[var(--color-text-muted)]">
                <p>Belum ada tugas hari ini.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection