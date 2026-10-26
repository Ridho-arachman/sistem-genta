@extends('layouts.app', ['pageTitle' => 'Lupa Password'])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--color-background)] px-4 py-12">
    <div class="w-full max-w-md">
        <div class="card p-8">
            <div class="text-center mb-8">
                <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-14 h-14 object-contain mx-auto mb-4">
                <h1 class="text-3xl font-bold text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></h1>
                <p class="text-[var(--color-text-muted)] mt-2">Masukkan email Anda untuk menerima tautan reset password</p>
            </div>

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Email</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               class="input-field pl-10" placeholder="Masukkan email Anda">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3">
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] font-medium transition-colors">
                    ← Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection