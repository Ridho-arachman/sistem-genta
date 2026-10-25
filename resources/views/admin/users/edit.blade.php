@extends('admin.layouts.app', ['pageTitle' => 'Edit User: ' . $user->name])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Edit User: {{ $user->name }}</h2>
            <p class="text-[var(--color-text-muted)] mt-1">Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" 
                       class="input-field" placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" 
                       class="input-field" placeholder="Masukkan email">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Role</label>
                <select id="role" name="role" required class="input-field">
                    <option value="pengawas" {{ $user->role === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                    <option value="manajerial" {{ $user->role === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                    <option value="kepala_dapur" {{ $user->role === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input id="password" type="password" name="password" autocomplete="new-password" 
                       class="input-field" placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" 
                       class="input-field" placeholder="Ulangi password">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1">Perbarui User</button>
                <a href="{{ route('admin.users') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection