@extends('admin.layouts.app', ['pageTitle' => 'Manajemen User'])

@section('page_content')
<x-admin::page-heading title="Manajemen User" subtitle="Kelola user pengawas, manajerial, dan kepala dapur" title-class="text-2xl font-extrabold text-[var(--color-secondary)]">
    <x-slot:actions>
        <a href="{{ route('admin.users.archive') }}"
           class="btn btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span>Arsip User</span>
        </a>
        <a href="{{ route('admin.users.create') }}"
           class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah User</span>
        </a>
    </x-slot:actions>
</x-admin::page-heading>

<x-admin::panel class="mb-6" title="Filter & Pencarian" icon="search" :divider="false">
    <form method="GET" class="space-y-4">
        <div class="flex gap-4 flex-wrap items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="search">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="input input-bordered w-full pl-10" placeholder="Cari nama atau email..." aria-label="Cari user">
                </div>
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="role">Role</label>
                <div class="relative">
                    <select id="role" name="role" class="input input-bordered w-full pr-10" aria-label="Filter role">
                        <option value="">Semua Role</option>
                        <option value="pengawas" {{ request('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="manajerial" {{ request('role') === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                        <option value="kepala_dapur" {{ request('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="status">Status</label>
                <div class="relative">
                    <select id="status" name="status" class="input input-bordered w-full pr-10" aria-label="Filter status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                        <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex gap-3 pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293.707L3.293 7.293A1 1 0 003 8V4z"></path></svg>
                <span>Filter</span>
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Reset</span>
            </a>
        </div>
    </form>
</x-admin::panel>

<x-admin::panel class="overflow-hidden mb-6" flush>
    @if ($users->count() > 0)
        <div class="overflow-visible">
            <x-admin::table>
                <x-slot:head>
                    <th class="min-w-[180px] max-w-[300px]">NAMA</th>
                    <th class="min-w-[100px] max-w-[140px]">PERAN</th>
                    <th class="min-w-[160px] hidden lg:table-cell">AKSES</th>
                    <th class="min-w-[70px] text-center">AKSI</th>
                </x-slot:head>
                    @foreach ($users as $user)
                    <tr>
                        <td class="min-w-0 max-w-[300px]">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="font-semibold text-[var(--color-secondary)] truncate line-clamp-1 min-w-0">{{ $user->name }}</span>
                                @if ($user->id === auth()->id())
                                    <span class="text-[10px] font-bold bg-[var(--color-primary)] text-white px-2 py-0.5 rounded-full flex-shrink-0">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="min-w-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold truncate
                                @if($user->role === 'admin') bg-[var(--color-primary)]/15 text-[var(--color-primary)]
                                @elseif($user->role === 'pengawas') bg-[#22C55E15] text-[#166534]
                                @elseif($user->role === 'manajerial') bg-[#F59E0B15] text-[#854D0E]
                                @else bg-[var(--color-border)] text-[var(--color-text-muted)]
                                @endif">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="min-w-0 hidden lg:table-cell line-clamp-2 text-[13px] text-[var(--color-text)]">
                            {{ $user->access_description }}
                        </td>
                        <td class="min-w-0 text-center">
                            <div class="inline-flex gap-2 justify-center">
                                @if ($user->id !== auth()->id() && !$user->isAdmin())
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="w-9 h-9 grid place-items-center rounded-lg bg-[#EFF6FF] text-[#2563EB] hover:bg-[#2563EB] hover:text-white transition-colors" 
                                       title="Edit" aria-label="Edit user {{ $user->name }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    @if (! $user->trashed())
                                        <button type="button" 
                                                x-on:click="$dispatch('open-delete-modal', { url: '{{ route('admin.users.destroy', $user) }}', name: @js($user->name) })"
                                                class="w-9 h-9 grid place-items-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-colors" 
                                                title="Hapus (Soft Delete)" aria-label="Hapus user {{ $user->name }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-[var(--color-text-muted)] text-sm">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
            </x-admin::table>
        </div>
        <div class="px-4 py-4 border-t border-[var(--color-border)]">
            {{ $users->links() }}
        </div>
    @else
        <x-admin::empty-state title="Tidak Ada User" message="Belum ada user yang terdaftar">
            <x-slot:icon>
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </x-slot:icon>
            <x-slot:action>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah User Pertama</span>
                </a>
            </x-slot:action>
        </x-admin::empty-state>
    @endif
</x-admin::panel>

<!-- Alpine Delete Modal -->
<div x-data="{ show: false, url: '', name: '' }" 
     x-on:open-delete-modal.window="show = true; url = $event.detail.url; name = $event.detail.name"
     x-show="show" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-xl" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 grid place-items-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Hapus User (Soft Delete)</h3>
        </div>
        <p class="text-gray-600 mb-6">User <span x-text="name" class="font-semibold"></span> akan dipindahkan ke trash. Data riwayat (absensi, laporan, dll) tetap terjaga. Tindakan ini bisa dibatalkan via restore.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" x-on:click="show = false" class="btn btn-secondary">Batal</button>
            <form :action="url" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection