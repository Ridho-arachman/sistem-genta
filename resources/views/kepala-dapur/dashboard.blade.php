@extends('admin.layouts.app', ['pageTitle' => 'Dashboard Kepala Dapur'])

@section('page_content')
<x-admin::page-heading title="Dashboard Kepala Dapur" subtitle="Kondisi dapur dan kamera yang menjadi tanggung jawab Anda · {{ now()->translatedFormat('l, d F Y') }}" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @forelse ($dapurs as $dapur)
        @php
            $total = $dapur->cctvs->count();
            $online = $dapur->cctvs->where('status', 'online')->count();
        @endphp
        <article class="bg-white border border-[var(--color-border)] rounded-[14px] p-[18px]">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="min-w-0">
                    <h3 class="font-bold text-[var(--color-secondary)]">{{ $dapur->nama }}</h3>
                    <p class="text-sm text-[var(--color-text-muted)]">{{ $dapur->kecamatan ?? '' }}{{ $dapur->kabupaten_kota ? ', ' . $dapur->kabupaten_kota : '' }}</p>
                </div>
                <span class="badge {{ $online === $total && $total > 0 ? 'badge-success' : 'badge-warning' }}">{{ $online }}/{{ $total }} kamera online</span>
            </div>
            <div class="text-sm text-[var(--color-text-muted)] mt-2">
                <p>Kamera maintenance: {{ $dapur->cctvs->where('status', 'maintenance')->count() }}</p>
                <p>Kamera offline: {{ $dapur->cctvs->where('status', 'offline')->count() }}</p>
            </div>
            <a href="{{ route('kepala-dapur.dapurs.show', $dapur) }}" class="btn btn-sm btn-secondary mt-3">Lihat Detail Dapur</a>
        </article>
    @empty
        <div class="col-span-full text-center text-[var(--color-text-muted)] py-10">
            <p>Belum ada dapur yang ditugaskan ke Anda. Hubungi admin.</p>
        </div>
    @endforelse
</div>
@endsection
