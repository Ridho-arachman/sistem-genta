@extends('layouts.app', ['pageTitle' => 'Login'])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--color-background)] px-4 py-12">
    <div class="w-full max-w-md">
        <div class="card p-8 sm:p-10">
            <div class="text-center mb-10">
                <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-16 h-16 object-contain mx-auto mb-5">
                <h1 class="text-4xl font-bold text-[var(--color-secondary)] tracking-tight">GENTA <span class="text-[var(--color-primary)]">Banten</span></h1>
                <p class="text-[var(--color-text-muted)] mt-3 text-lg">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6 flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 12.586 7.707 11.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPassword: false, loading: false, emailError: @json($errors->has('email')), passwordError: @json($errors->has('password')) }">
                @csrf

                <div x-class="{ 'input-field-error': emailError }">
                    <label for="email" class="block text-sm font-medium text-[var(--color-secondary)] mb-2">Email</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               class="input-field pl-11" placeholder="Masukkan email Anda" :class="{ 'input-field-error': emailError }" autofocus>
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5 animate-in fade-in slide-in-from-top-2 duration-200">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div x-class="{ 'input-field-error': passwordError }">
                    <label for="password" class="block text-sm font-medium text-[var(--color-secondary)] mb-2">Password</label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="input-field pl-11 pr-12" placeholder="Masukkan password" :class="{ 'input-field-error': passwordError }">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 11-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" aria-label="Toggle password visibility" aria-pressed="false" :aria-pressed="showPassword">
                            <template x-if="!showPassword">
                                <svg x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform rotate(-90deg) opacity-0 scale-90" x-transition:enter-end="transform rotate(0) opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform rotate(0) opacity-100 scale-100" x-transition:leave-end="transform rotate(90deg) opacity-0 scale-90" class="w-5 h-5 text-[var(--color-text-muted)] hover:text-[var(--color-secondary)] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </template>
                            <template x-if="showPassword">
                                <svg x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform rotate(-90deg) opacity-0 scale-90" x-transition:enter-end="transform rotate(0) opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform rotate(0) opacity-100 scale-100" x-transition:leave-end="transform rotate(90deg) opacity-0 scale-90" class="w-5 h-5 text-[var(--color-text-muted)] hover:text-[var(--color-secondary)] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m0 0l18 18"></path>
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5 animate-in fade-in slide-in-from-top-2 duration-200">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember" class="checkbox-custom" aria-describedby="remember-desc" @change="$dispatch('remember-changed', { checked: $event.target.checked })">
                        <span id="remember-desc" class="text-sm text-[var(--color-text-muted)]">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] font-medium transition-colors hover:underline">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit" :disabled="loading" class="btn-primary w-full py-3.5 text-base font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-all relative overflow-hidden">
                    <template x-if="!loading">
                        <span x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-1">
                            Masuk
                        </span>
                    </template>
                    <template x-if="loading">
                        <span x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-1" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </template>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-[var(--color-text-muted)]">
                    Hanya admin yang bisa mendaftarkan user
                </p>
            </div>
        </div>
    </div>
</div>
@endsection