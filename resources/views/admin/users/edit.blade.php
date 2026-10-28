@extends('admin.layouts.app', ['pageTitle' => 'Detail User: ' . $user->name])

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Detail User: {{ $user->name }}</h1>
            <div class="muted">Informasi pengguna (read-only)</div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Data Pengguna --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">♙</span>
                Data Pengguna
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input id="name" type="text" value="{{ $user->name }}" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                    </div>
                </div>

                {{-- Email --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <input id="email" type="email" value="{{ $user->email }}" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                    </div>
                </div>

                {{-- Role --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="role">Role</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <input id="role" type="text" value="{{ $user->role_label }}" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                    </div>
                </div>

                {{-- Dapur Assignment (full width) --}}
                <div class="form-field lg:col-span-2">
                    @if ($user->isPengawas())
                        <label class="form-label">Dapur yang Diawasi</label>
                        @if ($user->dapurs->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($user->dapurs as $dapur)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                        {{ $dapur->nama }} ({{ $dapur->lokasi }})
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[var(--color-text-muted)]">-</p>
                        @endif
                    @elseif ($user->isKepalaDapur())
                        <label class="form-label">Dapur</label>
                        @if ($user->dapur)
                            <div class="input-with-icon">
                                <input type="text" value="{{ $user->dapur->nama }} ({{ $user->dapur->lokasi }})" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                            </div>
                        @else
                            <p class="text-[var(--color-text-muted)]">Belum ditugaskan</p>
                        @endif
                    @elseif ($user->isManajerial())
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="font-medium text-blue-800">Role Manajerial tidak memerlukan assignment dapur</p>
                                    <p class="text-sm text-blue-700">Manajerial memiliki akses read-only ke semua dapur dan laporan.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-[var(--color-text-muted)]">-</p>
                    @endif
                </div>

                {{-- Status --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="status">Status</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <input id="status" type="text" value="{{ $user->status_label }}" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                    </div>
                </div>

                {{-- Email Verified --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="email_verified">Email Terverifikasi</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <input id="email_verified" type="text" value="{{ $user->email_verified_at ? 'Ya (' . $user->email_verified_at->format('d M Y H:i') . ')' : 'Belum' }}" readonly class="input-field select-enhanced bg-[var(--color-surface)] cursor-not-allowed">
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Audit --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">⚙</span>
                Informasi Audit
            </div>

            <div class="p-4 bg-[var(--color-surface)]/50 border border-[var(--color-border)] rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    {{-- Created --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Dibuat</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->createdBy ? $user->createdBy->name : ($user->registered_by ? $user->registeredBy?->name : 'Sistem') }}
                        </p>
                    </div>

                    {{-- Updated --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Terakhir Diupdate</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->updatedBy ? $user->updatedBy->name : '-' }}
                        </p>
                    </div>

                    {{-- Deleted --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Dihapus (Soft Delete)</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->deleted_at ? $user->deleted_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->deletedBy ? $user->deletedBy->name : '-' }}
                        </p>
                    </div>

                    {{-- Registered By (legacy) --}}
                    <div class="md:col-span-3">
                        <p class="text-[var(--color-text-muted)]">Didaftarkan Oleh (Legacy)</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->registeredBy ? $user->registeredBy->name : 'Sistem' }}
                            @if ($user->registered_by)
                                <span class="text-[var(--color-text-muted)] text-xs"> (ID: {{ $user->registered_by }})</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions (full width) --}}
        <div class="form-actions flex justify-end gap-3 pt-4 border-t border-[var(--color-border)]">
            <a href="{{ route('admin.users') }}" class="btn-action btn-action-secondary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Daftar</span>
            </a>
            @if (! $user->trashed())
                <a href="{{ route('admin.users.destroy', $user) }}" class="btn-action btn-action-primary bg-[#EF4444] hover:bg-[#DC2626]" data-confirm="Yakin ingin menghapus (soft delete) user ini?">
                    <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus (Soft Delete)</span>
                </a>
            @else
                <a href="{{ route('admin.users.restore', $user) }}" class="btn-action btn-action-primary bg-[#22C55E] hover:bg-[#16A34A]">
                    <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Pulihkan</span>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection