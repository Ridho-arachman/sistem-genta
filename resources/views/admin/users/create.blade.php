@extends('admin.layouts.app', ['pageTitle' => 'Tambah User Baru'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Tambah User Baru</h1>
            <div class="muted">Hanya bisa mendaftarkan role: Pengawas, Manajerial, Kepala Dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">♙</span>
                Data Pengguna
            </div>

            <div class="form-field">
                <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" 
                           class="input-field select-enhanced" placeholder="Masukkan nama lengkap">
                </div>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="email">Email <span class="required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" 
                           class="input-field select-enhanced" placeholder="Masukkan email">
                </div>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="role">Role <span class="required">*</span></label>
                <select id="role" name="role" required class="input-field select-enhanced">
                    <option value="">Pilih role</option>
                    <option value="pengawas" {{ old('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                    <option value="manajerial" {{ old('role') === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                    <option value="kepala_dapur" {{ old('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                </select>
                @error('role')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                           class="input-field select-enhanced" placeholder="Minimal 8 karakter">
                </div>
                <p class="form-hint">Minimal 8 karakter, kombinasikan huruf & angka</p>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                           class="input-field select-enhanced" placeholder="Ulangi password">
                </div>
                @error('password_confirmation')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan User</span>
            </button>
            <a href="{{ route('admin.users') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>
@endsection