@extends('admin.layouts.app', ['pageTitle' => 'Detail SP: ' . $spWarning->user->name])

@section('page_content')
<div class="max-w-3xl mx-auto">
    <x-admin::page-heading title="Detail Surat Peringatan" subtitle="SP-{{ $spWarning->level }} untuk {{ $spWarning->user->name }}">
        <x-slot:actions>
            <a href="{{ route('admin.sp-warnings.index') }}" class="btn btn-secondary">Kembali</a>
        </x-slot:actions>
    </x-admin::page-heading>

    <x-admin::panel title="Informasi Surat Peringatan" icon="warning">

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Pengawas</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->user->name }}</p>
                </div>
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Email</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->user->email }}</p>
                </div>
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Level SP</p>
                    <p class="font-semibold text-[var(--color-secondary)]">
                        <span class="badge
                            @if($spWarning->level == 1) badge-warning
                            @elseif($spWarning->level == 2) badge-error
                            @else bg-red-500/20 text-red-800 font-bold @endif">
                            SP-{{ $spWarning->level }}
                        </span>
                    </p>
                </div>
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Status</p>
                    <p class="font-semibold text-[var(--color-secondary)]">
                        <span class="badge
                            @if($spWarning->status === 'aktif') badge-error
                            @elseif($spWarning->status === 'dicabut') badge-secondary
                            @else badge-success @endif">
                            {{ ucfirst($spWarning->status) }}
                        </span>
                    </p>
                </div>
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Diterbitkan Oleh</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->admin->name }}</p>
                </div>
                <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                    <p class="text-sm text-[var(--color-text-muted)]">Tanggal Terbit</p>
                    <p class="font-semibold text-[var(--color-secondary)]">{{ $spWarning->issued_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="p-4 bg-[var(--color-surface)] rounded-lg">
                <p class="text-sm text-[var(--color-text-muted)] mb-2">Alasan</p>
                <p class="text-[var(--color-secondary)] whitespace-pre-wrap">{{ $spWarning->reason }}</p>
            </div>

            @if ($spWarning->status === 'aktif')
            <div class="border-t border-[var(--color-border)] pt-6">
                <form action="{{ route('admin.sp-warnings.update', $spWarning) }}" method="POST" class="flex gap-4" x-data="confirmSubmit">
                    @csrf
                    @method('PUT')
                    <select name="status" @change="confirmStatus('Yakin ubah status?') && $event.target.form.submit()" class="input input-bordered flex-1">
                        <option value="aktif" selected>Aktif</option>
                        <option value="dicabut">Cabut SP</option>
                        <option value="selesai">Tandai Selesai</option>
                    </select>
                </form>
            </div>
            @endif
        </div>
    </x-admin::panel>
</div>
@endsection