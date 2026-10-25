@extends('admin.layouts.app', ['pageTitle' => 'Manajemen User'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Manajemen User</h2>
            <p class="text-[var(--color-text-muted)]">Kelola user pengawas, manajerial, dan kepala dapur</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </a>
    </div>

    {{-- Filters --}}
    <div class="card p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="input-field">
            </div>
            <div class="md:w-48">
                <select name="role" class="input-field">
                    <option value="">Semua Role</option>
                    <option value="pengawas" {{ request('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                    <option value="manajerial" {{ request('role') === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                    <option value="kepala_dapur" {{ request('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary whitespace-nowrap">Filter</button>
            <a href="{{ route('admin.users') }}" class="btn-secondary whitespace-nowrap">Reset</a>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="card overflow-hidden">
        @if ($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th">Nama</th>
                            <th class="table-th">Email</th>
                            <th class="table-th">Role</th>
                            <th class="table-th">Didaftarkan Oleh</th>
                            <th class="table-th">Tanggal Daftar</th>
                            <th class="table-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($users as $user)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td font-medium">{{ $user->name }}</td>
                            <td class="table-td text-[var(--color-text-muted)]">{{ $user->email }}</td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($user->role === 'admin') badge-primary
                                    @elseif($user->role === 'pengawas') badge-success
                                    @elseif($user->role === 'manajerial') badge-warning
                                    @elseif($user->role === 'kepala_dapur') badge-gray
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="table-td">
                                @if ($user->registeredBy)
                                    {{ $user->registeredBy->name }}
                                @else
                                    <span class="text-[var(--color-text-muted)]">-</span>
                                @endif
                            </td>
                            <td class="table-td text-[var(--color-text-muted)]">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="table-td">
                                <div class="flex items-center gap-2">
                                    @if ($user->id !== auth()->id() && !$user->isAdmin())
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-[var(--color-primary)] hover:underline text-sm">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                    </form>
                                    @else
                                    <span class="text-[var(--color-text-muted)] text-sm">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $users->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Tidak Ada User</h3>
                <p class="text-[var(--color-text-muted)] mt-1">Belum ada user yang terdaftar</p>
                <a href="{{ route('admin.users.create') }}" class="btn-primary mt-4 inline-block">Tambah User Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection