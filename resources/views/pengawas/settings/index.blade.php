@extends('pengawas.layouts.app', ['pageTitle' => 'Profil Pengawas'])

@section('content')
<div class="max-w-xl mx-auto">
    {{-- Standard Header --}}
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>Profil Pengawas</h1>
            <div class="muted">Kelola email dan password Anda. Nama tidak bisa diubah untuk menjaga akuntabilitas laporan.</div>
        </div>
    </div>

    {{-- Flash Status --}}
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 12.586 7.707 11.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Profil Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-[var(--color-border)] p-6 space-y-6">
        {{-- Nama (Readonly) --}}
        <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nama <span class="text-xs text-[var(--color-text-muted)] font-normal">(tidak bisa diubah)</span></label>
            <input type="text" class="input-field bg-[var(--color-surface)]" value="{{ auth()->user()->name }}" readonly aria-readonly="true">
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">Nama digunakan untuk identitas di laporan pengawasan. Hubungi admin jika perlu perubahan.</p>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="input-field" autocomplete="email" autofocus>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Current Password --}}
        <div>
            <label for="current_password" class="block text-sm font-medium text-[var(--color-text)] mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
            <div class="relative">
                <input type="password" id="current_password" name="current_password" required class="input-field pl-11 pr-12" autocomplete="current-password" placeholder="Masukkan password saat ini">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 11-8 0v4h8z"></path>
                    </svg>
                </div>
                <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('svg').classList.toggle('hidden')" aria-label="Toggle password visibility">
                    <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path></svg>
                    <svg class="w-5 h-5 text-[var(--color-text-muted)] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0-8.268-2.943-9.543-7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m0 0l18 18"></path></svg>
                </button>
            </div>
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password (Optional) --}}
        <div>
            <label for="password" class="block text-sm font-medium text-[var(--color-text)] mb-1">Password Baru <span class="text-[var(--color-text-muted)]">(opsional)</span></label>
            <div class="relative">
                <input type="password" id="password" name="password" class="input-field pl-11 pr-12" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 11-8 0v4h8z"></path>
                    </svg>
                </div>
                <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('svg').classList.toggle('hidden'); this.querySelector('svg.hidden').classList.toggle('hidden')" aria-label="Toggle password visibility">
                    <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path></svg>
                    <svg class="w-5 h-5 text-[var(--color-text-muted)] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0-8.268-2.943-9.543-7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m0 0l18 18"></path></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-text)] mb-1">Konfirmasi Password Baru <span class="text-[var(--color-text-muted)]">(opsional)</span></label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" class="input-field pl-11 pr-12" autocomplete="new-password" placeholder="Ulangi password baru">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 11-8 0v4h8z"></path>
                    </svg>
                </div>
                <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; this.querySelector('svg').classList.toggle('hidden'); this.querySelector('svg.hidden').classList.toggle('hidden')" aria-label="Toggle password visibility">
                    <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path></svg>
                    <svg class="w-5 h-5 text-[var(--color-text-muted)] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0-8.268-2.943-9.543-7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m0 0l18 18"></path></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="flex-1 btn-primary py-3">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // No extra JS needed
</script>
@endpush