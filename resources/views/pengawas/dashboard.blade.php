@extends('pengawas.layouts.app', ['pageTitle' => 'Dashboard Pengawas'])

@section('page_content')
<div class="min-h-screen bg-[var(--color-background)]">
    <x-pengawas::page-heading
        title="Dashboard Pengawas"
        subtitle="Kondisi operasional dapur dan pelaporan · {{ now()->translatedFormat('l, d F Y') }}" />

    {{-- Tugas hari ini --}}
    <section class="bg-white rounded-xl shadow-sm mx-2 sm:mx-0 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <span class="text-sm font-bold text-[var(--color-text)]">Tugas hari ini</span>
            <span class="text-sm font-bold text-[var(--color-primary)] bg-[var(--color-primary)]/10 px-3 py-1 rounded-full">{{ $dapurs->count() }} tugas</span>
        </div>

        @forelse ($dapurs as $dapur)
            <x-pengawas::dapur-card :dapur="$dapur" />
        @empty
            <div class="text-center py-8 text-[var(--color-text-muted)]">
                <p>Belum ada tugas hari ini.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection