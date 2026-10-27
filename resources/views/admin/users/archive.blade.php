@extends('admin.layouts.app', ['pageTitle' => 'Arsip User'])

@section('content')
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
    <div>
        <div class="text-[var(--color-primary)] text-xs font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
        <h1 class="text-2xl font-extrabold text-[var(--color-secondary)] mt-1">Arsip User (Soft Deleted)</h1>
        <div class="text-[var(--color-text-muted)] text-sm mt-1">Kelola user yang telah dihapus — bisa dipulihkan atau dihapus permanen</div>
    </div>
    <a href="{{ route('admin.users') }}" 
       class="btn-action inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition shrink-0 self-start lg:self-start">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span>Kembali ke Manajemen User</span>
    </a>
</div>

<div class="form-section mb-6">
    <div class="form-section-title flex items-center gap-2.5 mb-5 pb-4 border-b border-[var(--color-border)]">
        <span class="section-icon w-6 h-6 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center text-xs">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        <h2 class="text-sm font-bold text-[var(--color-secondary)]">Filter & Pencarian</h2>
    </div>
    <form method="GET" class="space-y-4">
        <div class="flex gap-4 flex-wrap items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="search">Cari</label>
                <div class="input-with-icon relative">
                    <svg class="input-icon-inner absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="input-field w-full pl-10" placeholder="Cari nama atau email..." aria-label="Cari user">
                </div>
            </div>
            <div class="min-w-[180px]">
                <label class="form-label block text-xs font-semibold text-[var(--color-secondary)] mb-2" for="role">Role</label>
                <div class="input-with-icon relative">
                    <select id="role" name="role" class="input-field select-enhanced w-full pr-10" aria-label="Filter role">
                        <option value="">Semua Role</option>
                        <option value="pengawas" {{ request('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="manajerial" {{ request('role') === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                        <option value="kepala_dapur" {{ request('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex gap-3 pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="btn-action inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--color-primary)] text-white rounded-lg text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293.707L3.293 7.293A1 1 0 003 8V4z"></path></svg>
                <span>Filter</span>
            </button>
            <a href="{{ route('admin.users.archive') }}" class="btn-action inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold hover:bg-[var(--color-surface-hover)] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Reset</span>
            </a>
        </div>
    </form>
</div>

<div class="bg-white border border-[var(--color-border)] rounded-2xl overflow-hidden mb-6">
    @if ($users->count() > 0)
        <div class="overflow-visible">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[var(--color-surface)]">
                        <th class="table-th min-w-[180px] max-w-[300px]">NAMA</th>
                        <th class="table-th min-w-[100px] max-w-[140px]">PERAN</th>
                        <th class="table-th min-w-[160px] hidden lg:table-cell">AKSES</th>
                        <th class="table-th min-w-[120px] hidden md:table-cell">TERAKHIR AKTIF</th>
                        <th class="table-th min-w-[120px]">DIHAPUS PADA</th>
                        <th class="table-th min-w-[100px] text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @foreach ($users as $user)
                    <tr class="hover:bg-[var(--color-surface)] transition-colors">
                        <td class="table-td min-w-0 max-w-[300px]">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="font-semibold text-[var(--color-secondary)] truncate line-clamp-1 min-w-0">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="table-td min-w-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold truncate
                                @if($user->role === 'admin') bg-[var(--color-primary)]/15 text-[var(--color-primary)]
                                @elseif($user->role === 'pengawas') bg-[#22C55E15] text-[#166534]
                                @elseif($user->role === 'manajerial') bg-[#F59E0B15] text-[#854D0E]
                                @else bg-[var(--color-border)] text-[var(--color-text-muted)]
                                @endif">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="table-td min-w-0 hidden lg:table-cell line-clamp-2 text-[13px] text-[var(--color-text)]">
                            {{ $user->access_description }}
                        </td>
                        <td class="table-td min-w-0 hidden md:table-cell text-[13px] text-[var(--color-text-muted)]">
                            @if ($user->deactivated_at)
                                {{ $user->deactivated_at->format('d M Y H:i') }}
                            @elseif ($user->updated_at)
                                {{ $user->updated_at->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="table-td min-w-0 text-[13px] text-[var(--color-text-muted)]">
                            {{ $user->deleted_at->format('d M Y H:i') }}
                        </td>
                        <td class="table-td min-w-0 text-center">
                            <div class="inline-flex gap-2 justify-center">
                                {{-- Restore Button --}}
                                <button type="button" 
                                        x-on:click="$dispatch('open-restore-modal', { url: '{{ route('admin.users.restore', ['id' => $user->id]) }}', name: @js($user->name) })"
                                        class="w-9 h-9 grid place-items-center rounded-lg bg-[#22C55E15] text-[#166534] hover:bg-[#22C55E] hover:text-white transition-colors" 
                                        title="Pulihkan" aria-label="Pulihkan user {{ $user->name }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>
                                {{-- Force Delete Button --}}
                                <button type="button" 
                                        x-on:click="$dispatch('open-force-delete-modal', { url: '{{ route('admin.users.force', ['id' => $user->id]) }}', name: @js($user->name) })"
                                        class="w-9 h-9 grid place-items-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-colors" 
                                        title="Hapus Permanen" aria-label="Hapus permanen user {{ $user->name }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-[var(--color-border)]">
            {{ $users->links() }}
        </div>
    @else
        <div class="py-16 text-center">
            <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Arsip Kosong</h3>
            <p class="text-[var(--color-text-muted)] mt-1">Tidak ada user yang dihapus</p>
        </div>
    @endif
</div>

<!-- Alpine Restore Modal -->
<div x-data="{ show: false, url: '', name: '' }" 
     x-on:open-restore-modal.window="show = true; url = $event.detail.url; name = $event.detail.name"
     x-show="show" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-xl animate-scale-in" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 grid place-items-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Pulihkan User</h3>
        </div>
        <p class="text-gray-600 mb-6">User <span x-text="name" class="font-semibold"></span> akan dipulihkan dan muncul kembali di Manajemen User. Status aktif (is_active) akan dikembalikan seperti sebelum dihapus.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" x-on:click="show = false" class="btn-action px-4 py-2.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold">Batal</button>
            <form :action="url" method="POST" class="inline">
                @csrf
                @method('POST')
                <button type="submit" class="btn-action px-4 py-2.5 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 transition">Pulihkan</button>
            </form>
        </div>
    </div>
</div>

<!-- Alpine Force Delete Modal -->
<div x-data="{ show: false, url: '', name: '' }" 
     x-on:open-force-delete-modal.window="show = true; url = $event.detail.url; name = $event.detail.name"
     x-show="show" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-xl animate-scale-in" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 grid place-items-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Hapus Permanen</h3>
        </div>
        <p class="text-gray-600 mb-6">PERINGATAN: User <span x-text="name" class="font-semibold text-red-600"></span> akan dihapus PERMANEN dari database. Semua data terkait (absensi, laporan, riwayat, dll) yang berkaitan dengan user ini tidak bisa dipulihkan. Tindakan ini TIDAK DAPAT DIBATALKAN.</p>
        <div class="flex gap-3 justify-end">
            <button type="button" x-on:click="show = false" class="btn-action px-4 py-2.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm font-semibold">Batal</button>
            <form :action="url" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action px-4 py-2.5 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition">Hapus Permanen</button>
            </form>
        </div>
    </div>
</div>
@endsection