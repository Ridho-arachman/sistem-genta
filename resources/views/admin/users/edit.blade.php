@extends('admin.layouts.app', ['pageTitle' => 'Edit User: ' . $user->name])

@section('page_content')
<div class="max-w-5xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Edit User: {{ $user->name }}</h1>
            <div class="muted">Perbarui data pengguna</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6" id="user-form">
        @csrf
        @method('PUT')

        {{-- Data Pengguna --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">♙</span>
                Data Pengguna
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" 
                               class="input-field select-enhanced" placeholder="Masukkan nama lengkap">
                    </div>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" 
                               class="input-field select-enhanced" placeholder="Masukkan email">
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="role">Role <span class="required">*</span></label>
                    <select id="role" name="role" required class="input-field select-enhanced" onchange="showSection(this.value)">
                        <option value="pengawas" {{ $user->role === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="manajerial" {{ $user->role === 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                        <option value="kepala_dapur" {{ $user->role === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                    </select>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dapur Assignment (full width) --}}
                <div class="form-field lg:col-span-2">
                    <!-- Kepala Dapur -->
                    <div id="dapur-section-kepala_dapur" class="dapur-section hidden" style="display: none;">
                        <div>
                            <label class="form-label" for="dapur_id">Dapur <span class="required">*</span></label>
                            <div class="relative">
                                <div class="input-with-icon">
                                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input type="text" id="dapur_search" placeholder="Cari dapur..." class="input-field select-enhanced" autocomplete="off">
                                </div>
                                <input type="hidden" id="dapur_id" name="dapur_id" required>
                                <!-- Dropdown -->
                                <div id="kepala-dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-[var(--color-border)] rounded-lg shadow-lg hidden">
                                </div>
                            </div>
                            @error('dapur_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pengawas -->
                    <div id="dapur-section-pengawas" class="dapur-section hidden" style="display: none;">
                        <label class="form-label" for="dapur_ids">Dapur yang Diawasi <span class="required">*</span></label>

                        <!-- Search input -->
                        <div class="input-with-icon mb-2">
                            <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" id="dapur-search" placeholder="Cari dapur..." class="input-field select-enhanced" autocomplete="off">
                        </div>

                        <!-- Selected chips -->
                        <div id="dapur-chips" class="mt-2 flex flex-wrap gap-2 hidden"></div>

                        <!-- Dapur list (card-based) -->
                        <div id="dapur-list" class="mt-2 max-h-60 overflow-y-auto border border-[var(--color-border)] rounded-lg bg-white">
                            @foreach ($dapurs as $dapur)
                                <label class="dapur-card group relative cursor-pointer" data-id="{{ $dapur->id }}" data-nama="{{ $dapur->nama }}" data-lokasi="{{ $dapur->lokasi }}">
                                    <input type="checkbox" name="dapur_ids[]" value="{{ $dapur->id }}" class="sr-only peer" {{ $user->dapurs->contains($dapur->id) ? 'checked' : '' }}>
                                    <div class="dapur-card-body p-4 border-b border-[var(--color-border)] last:border-0 hover:bg-[var(--color-surface)]/50 transition-colors peer-checked:border-[var(--color-primary)] peer-checked:bg-[var(--color-primary)]/5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="text-sm font-semibold text-[var(--color-secondary)] line-clamp-2">{{ $dapur->nama }}</h3>
                                                    <span class="dapur-badge inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]">Dapur</span>
                                                </div>
                                                <p class="text-xs text-[var(--color-text-muted)] truncate">{{ $dapur->lokasi }}</p>
                                            </div>
                                            <div class="w-6 h-6 shrink-0"></div>
                                        </div>
                                    </div>
                                    <div class="dapur-check absolute right-4 top-4 flex items-center justify-center w-6 h-6 rounded-full transition-all duration-200 peer-checked:bg-[var(--color-primary)] peer-checked:text-white">
                                        <svg class="w-5 h-5 text-[var(--color-primary)] opacity-0 stroke-[2.5] transition-opacity duration-200 group-hover:opacity-20 peer-checked:opacity-100 peer-checked:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                            @endforeach
                            <div id="dapur-empty" class="text-center text-[var(--color-text-muted)] text-sm py-8 hidden">Tidak ada dapur yang cocok</div>
                            <div id="dapur-all-selected" class="text-center text-[var(--color-text-muted)] text-sm py-8 hidden">Semua dapur sudah dipilih</div>
                        </div>

                        <p id="dapur-count" class="form-hint mt-2 hidden">Dipilih: <span id="dapur-count-num">0</span> dapur</p>
                        @error('dapur_ids')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Manajerial - no dapur assignment -->
                    <div id="dapur-section-manajerial" class="dapur-section hidden" style="display: none;">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="font-medium text-blue-800">Role Manajerial tidak memerlukan assignment dapur</p>
                                    <p class="text-sm text-blue-700">Manajerial memiliki akses read-only ke semua dapur dan laporan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Default: select role first -->
                    <div id="dapur-section-default" class="dapur-section hidden" style="display: none;">
                        <div class="p-4 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg">
                            <div class="flex items-center gap-3 text-[var(--color-text-muted)]">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Pilih role terlebih dahulu untuk melihat opsi assignment dapur</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="password">Password Baru <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input id="password" type="password" name="password" autocomplete="new-password" 
                               class="input-field select-enhanced" placeholder="Minimal 8 karakter (kosongkan jika tidak diubah)">
                    </div>
                    <p class="form-hint">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</p>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div class="form-field lg:col-span-1">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" 
                               class="input-field select-enhanced" placeholder="Ulangi password baru">
                    </div>
                    @error('password_confirmation')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Informasi Audit (Read Only) --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">⚙</span>
                Informasi Audit
            </div>

            <div class="p-4 bg-[var(--color-surface)]/50 border border-[var(--color-border)] rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    {{-- Created --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Dibuat</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->createdBy ? $user->createdBy->name : ($user->registered_by ? $user->registeredBy?->name : 'Sistem') }}
                        </p>
                    </div>

                    {{-- Updated --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Terakhir Diupdate</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->updatedBy ? $user->updatedBy->name : '-' }}
                        </p>
                    </div>

                    {{-- Deleted --}}
                    <div>
                        <p class="text-[var(--color-text-muted)]">Dihapus (Soft Delete)</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->deleted_at ? $user->deleted_at->format('d M Y H:i') : '-' }}
                        </p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Oleh: {{ $user->deletedBy ? $user->deletedBy->name : '-' }}
                        </p>
                    </div>

                    {{-- Registered By (legacy) --}}
                    <div class="md:col-span-3">
                        <p class="text-[var(--color-text-muted)]">Didaftarkan Oleh (Legacy)</p>
                        <p class="font-medium text-[var(--color-secondary)]">
                            {{ $user->registeredBy ? $user->registeredBy->name : 'Sistem' }}
                            @if ($user->registered_by)
                                <span class="text-[var(--color-text-muted)] text-xs"> (ID: {{ $user->registered_by }})</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions (full width) --}}
        <div class="form-actions lg:col-span-2 flex justify-end gap-3 pt-4 border-t border-[var(--color-border)]">
            <a href="{{ route('admin.users') }}" class="btn-action btn-action-secondary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Batal</span>
            </a>
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Perbarui User</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var roleSelect = document.getElementById('role');
    var sections = document.querySelectorAll('.dapur-section');

    function showSection(role) {
        sections.forEach(function (el) { el.style.display = 'none'; });
        var target = document.getElementById('dapur-section-' + role);
        if (target) target.style.display = 'block';
    }

    // Show correct section on load
    var currentRole = roleSelect ? roleSelect.value : '';
    showSection(currentRole);

    // Show correct section on change
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            showSection(this.value);
        });
    }

    // === Pengawas multi-dapur (plain JS) ===
    var searchInput = document.getElementById('dapur-search');
    var listEl = document.getElementById('dapur-list');
    var chipsEl = document.getElementById('dapur-chips');
    var emptyEl = document.getElementById('dapur-empty');
    var allSelectedEl = document.getElementById('dapur-all-selected');
    var countEl = document.getElementById('dapur-count');
    var countNumEl = document.getElementById('dapur-count-num');
    var items = listEl ? listEl.querySelectorAll('.dapur-card') : [];

    if (items.length === 0) return;

    var selectedIds = [];
    var checkboxMap = {};

    items.forEach(function (item) {
        var cb = item.querySelector('input[type="checkbox"]');
        if (!cb) return;
        checkboxMap[cb.value] = { cb: cb, item: item };
        if (cb.checked) selectedIds.push(cb.value);
    });

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderChips() {
        if (!chipsEl) return;
        chipsEl.innerHTML = '';
        selectedIds.forEach(function (id) {
            var entry = checkboxMap[id];
            var nama = entry ? entry.item.getAttribute('data-nama') : id;
            var chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]';
            chip.innerHTML =
                '<span>' + escapeHtml(nama) + '</span>' +
                '<button type="button" data-remove="' + escapeHtml(id) + '" class="hover:text-[var(--color-primary)]/70 focus:outline-none p-1" aria-label="Hapus">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                '</button>';
            chipsEl.appendChild(chip);
        });

        chipsEl.querySelectorAll('button[data-remove]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                toggleDapur(this.getAttribute('data-remove'));
            });
        });
    }

    function toggleDapur(id) {
        var idx = selectedIds.indexOf(id);
        if (idx === -1) {
            selectedIds.push(id);
        } else {
            selectedIds.splice(idx, 1);
        }
        var entry = checkboxMap[id];
        if (entry && entry.cb) entry.cb.checked = selectedIds.includes(id);
        updateUI();
    }

    function updateUI() {
        items.forEach(function (item) {
            var cb = item.querySelector('input[type="checkbox"]');
            if (!cb) return;
            cb.checked = selectedIds.includes(cb.value);
        });

        renderChips();

        if (countEl) {
            countEl.classList.toggle('hidden', selectedIds.length === 0);
            if (countNumEl) countNumEl.textContent = selectedIds.length;
        }

        var total = items.length;
        if (emptyEl) emptyEl.classList.toggle('hidden', selectedIds.length > 0 || total === 0);
        if (allSelectedEl) allSelectedEl.classList.toggle('hidden', selectedIds.length !== total || total === 0);
    }

    function filterList() {
        var q = (searchInput.value || '').toLowerCase();
        var visible = 0;
        items.forEach(function (item) {
            var nama = (item.getAttribute('data-nama') || '').toLowerCase();
            var lokasi = (item.getAttribute('data-lokasi') || '').toLowerCase();
            var match = nama.includes(q) || lokasi.includes(q);
            item.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0 || selectedIds.length > 0 || q === '');
        if (allSelectedEl) allSelectedEl.classList.add('hidden');
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterList);
    }

    items.forEach(function (item) {
        var cb = item.querySelector('input[type="checkbox"]');
        if (!cb) return;
        cb.addEventListener('change', function () {
            toggleDapur(cb.value);
        });
        item.addEventListener('click', function (e) {
            if (e.target.tagName === 'INPUT') return;
            e.preventDefault();
            cb.click();
        });
    });

    updateUI();
});
</script>
@endpush