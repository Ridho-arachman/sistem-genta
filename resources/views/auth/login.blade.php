@extends('layouts.app', ['pageTitle' => 'Login'])

@section('content')
<div class="max-w-md mx-auto">
    <div class="card p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-12 h-12 object-contain mx-auto mb-4">
            <h1 class="text-2xl font-bold text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></h1>
            <p class="text-[var(--color-text-muted)] mt-2">Masuk ke akun Anda</p>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" 
                       class="input-field" placeholder="Masukkan email Anda">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                       class="input-field" placeholder="Masukkan password">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    <span class="text-sm text-[var(--color-text-muted)]">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full py-3">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Hanya admin yang bisa mendaftarkan user
            </p>
        </div>
    </div>
</div>
@endsection