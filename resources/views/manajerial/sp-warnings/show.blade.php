@extends('manajerial.layouts.app', ['pageTitle' => 'Detail SP: ' . $spWarning->user->name])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6">
            <div><h2 class="text-2xl font-bold text-[var(--color-secondary)]">Detail Surat Peringatan</h2><p class="text-[var(--color-text-muted)] mt-1">SP-{{ $spWarning->level }} untuk {{ $spWarning->user->name }}</p></div>
            <a href="{{ route('manajerial.sp-warnings.index') }}" class="btn-secondary">Kembali</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Pengawas</p><p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->user->name }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Email</p><p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->user->email }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Level SP</p><p class="font-semibold text-[var(--color-secondary)]"><span class="badge @if($spWarning->level == 1) badge-warning @elseif($spWarning->level == 2) badge-danger @else bg-red-500/20 text-red-800 font-bold @endif">SP-{{ $spWarning->level }}</span></p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Status</p><p class="font-semibold text-[var(--color-secondary)]"><span class="badge @if($spWarning->status === 'aktif') badge-danger @elseif($spWarning->status === 'dicabut') badge-gray @else badge-success @endif">{{ ucfirst($spWarning->status) }}</span></p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Diterbitkan Oleh</p><p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->admin->name }}</p></div>
            <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)]">Tanggal Terbit</p><p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->issued_at->format('d/m/Y H:i') }}</p></div>
        </div>

        <div class="p-4 bg-[var(--color-surface)] rounded-lg"><p class="text-sm text-[var(--color-text-muted)] mb-2">Alasan</p><p class="text-[var(--color-secondary)] whitespace-pre-wrap">{{ $spWarning->reason }}</p></div>
    </div>
</div>
@endsection
