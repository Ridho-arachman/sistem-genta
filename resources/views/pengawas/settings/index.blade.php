@extends('pengawas.layouts.app', ['pageTitle' => 'Profil Pengawas'])

@section('page_content')
<div class="max-w-xl mx-auto" x-data="{ show: {} }">
    <x-pengawas::page-heading
        title="Profil Pengawas"
        subtitle="Kelola email dan password Anda. Nama tidak bisa diubah untuk menjaga akuntabilitas laporan." />

    {{-- Notifikasi lewat <x-toast /> di layout (hindari pesan ganda) --}}

    {{-- Profil Form --}}
    <form method="POST" action="{{ route('pengawas.settings.profile.update') }}" class="bg-white rounded-xl shadow-sm border border-[var(--color-border)] p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama (Readonly) --}}
        <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nama <span class="text-xs text-[var(--color-text-muted)] font-normal">(tidak bisa diubah)</span></label>
            <input type="text" class="input input-bordered bg-[var(--color-surface)]" value="{{ auth()->user()->name }}" readonly aria-readonly="true">
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">Nama digunakan untuk identitas di laporan pengawasan. Hubungi admin jika perlu perubahan.</p>
        </div>

        {{-- Email (Readonly) --}}
        <div>
            <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-1">Email <span class="text-[var(--color-text-muted)] font-normal">(tidak bisa diubah)</span></label>
            <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" readonly class="input input-bordered" autocomplete="email">
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">Email digunakan untuk identitas dan login. Hubungi admin jika perlu perubahan.</p>
        </div>

        @php
            $passwordFields = [
                'current_password' => ['label' => 'Password Saat Ini', 'required' => true, 'autocomplete' => 'current-password', 'placeholder' => 'Masukkan password saat ini'],
                'password' => ['label' => 'Password Baru', 'required' => false, 'autocomplete' => 'new-password', 'placeholder' => 'Kosongkan jika tidak ingin mengubah'],
                'password_confirmation' => ['label' => 'Konfirmasi Password Baru', 'required' => false, 'autocomplete' => 'new-password', 'placeholder' => 'Ulangi password baru'],
            ];
        @endphp

        @foreach ($passwordFields as $field => $meta)
            <div>
                <label for="{{ $field }}" class="block text-sm font-medium text-[var(--color-text)] mb-1">
                    {{ $meta['label'] }}
                    @if ($meta['required'])
                        <span class="text-red-500">*</span>
                    @else
                        <span class="text-[var(--color-text-muted)]">(opsional)</span>
                    @endif
                </label>
                <div class="relative">
                    <input :type="show.{{ $field }} ? 'text' : 'password'" id="{{ $field }}" name="{{ $field }}" @required($meta['required']) class="input input-bordered pl-11 pr-12" autocomplete="{{ $meta['autocomplete'] }}" placeholder="{{ $meta['placeholder'] }}">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 11-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <button type="button" @click="show.{{ $field }} = !show.{{ $field }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" aria-label="Toggle password visibility">
                        <svg x-show="!show.{{ $field }}" class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path></svg>
                        <svg x-show="show.{{ $field }}" x-cloak class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0-8.268-2.943-9.543-7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m0 0l18 18"></path></svg>
                    </button>
                </div>
                @error($field)
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @unless ($meta['required'])
                    <p class="mt-1 text-xs text-[var(--color-text-muted)]">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
                @endunless
            </div>
        @endforeach

        {{-- Submit --}}
        <div class="flex gap-3 pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="flex-1 btn btn-primary py-3">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
