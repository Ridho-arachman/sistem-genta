@extends('admin.layouts.app', ['pageTitle' => 'Pengaturan Profil'])

@section('page_content')
<div class="heading mb-8">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Pengaturan Profil</h1>
        <div class="muted">Kelola nama, email, dan password akun Anda</div>
    </div>
</div>

<div class="card p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.settings.profile.update') }}">
        @csrf
        <input type="hidden" name="_method" value="PUT">

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Password Baru <span class="text-[var(--color-text-muted)]">(kosongkan jika tidak diubah)</span></label>
            <input type="password" id="password" name="password" class="input-field" autocomplete="new-password">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Konfirmasi Password Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" autocomplete="new-password">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection