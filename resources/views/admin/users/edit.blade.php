@extends('admin.layouts.app', ['pageTitle' => 'Edit User: ' . $user->name])

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Edit User: {{ $user->name }}</h1>
            <div class="muted">Perbarui data pengguna</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6" x-data="userForm(@json($user->role), @json($user->dapurs->pluck('id')->toArray()), @json($user->dapur_id))" x-cloak>
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
                    <select id="role" name="role" required class="input-field select-enhanced" x-model="selectedRole" @change="handleRoleChange()">
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
                    <template x-if="selectedRole === 'kepala_dapur'">
                        <div x-transition x-data="dapurAutocomplete()">
                            <label class="form-label" for="dapur_id">Dapur <span class="required">*</span></label>
                            <div class="relative">
                                <div class="input-with-icon">
                                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input type="text" 
                                           id="dapur_search" 
                                           x-model="searchQuery" 
                                           @focus="showDropdown = true"
                                           @keydown.down.prevent="focusFirstOption()"
                                           @keydown.up.prevent="focusLastOption()"
                                           @keydown.enter.prevent="selectHighlighted()"
                                           @keydown.escape="showDropdown = false"
                                           placeholder="Cari dapur..."
                                           class="input-field select-enhanced"
                                           autocomplete="off">
                                </div>
                                <input type="hidden" id="dapur_id" name="dapur_id" :value="kepalaDapurId" required>
                                
                                <!-- Dropdown -->
                                <div x-show="showDropdown && filteredDapurs.length > 0" 
                                     class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-[var(--color-border)] rounded-lg shadow-lg"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-y-95"
                                     x-transition:enter-end="transform opacity-100 scale-y-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-y-100"
                                     x-transition:leave-end="transform opacity-0 scale-y-95"
                                     @click.outside="showDropdown = false">
                                    <template x-for="(dapur, index) in filteredDapurs" :key="dapur.id">
                                        <button type="button" 
                                                @click="selectDapur(dapur)"
                                                @mouseover="highlightedIndex = index"
                                                :class="['w-full px-4 py-2 text-left hover:bg-[var(--color-primary)]/10', highlightedIndex === index ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : '']"
                                                class="flex items-center gap-3">
                                            <span class="font-medium" x-text="dapur.nama"></span>
                                            <span class="text-sm text-[var(--color-text-muted)]" x-text="dapur.lokasi"></span>
                                            <template x-if="dapur.kepala_dapur_name">
                                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-800">
                                                    Kepala: <span x-text="dapur.kepala_dapur_name"></span>
                                                </span>
                                            </template>
                                        </button>
                                    </template>
                                    <template x-if="filteredDapurs.length === 0">
                                        <div class="px-4 py-3 text-center text-[var(--color-text-muted)] text-sm">
                                            Tidak ada dapur yang cocok
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Warning if dapur already has kepala (only when changing to a different dapur) -->
                            <div x-show="selectedKepalaDapur && kepalaDapurId != {{ $user->dapur_id ?? 'null' }}" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm" x-transition>
                                <span class="font-medium">Perhatian: </span>
                                <span x-text="'Dapur ini sudah memiliki Kepala Dapur: ' + selectedKepalaDapur"></span>
                                <br><span class="font-medium">Menimpah akan mengubah assignment kepala dapur.</span>
                            </div>
                            @error('dapur_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>

                    <!-- Pengawas -->
                    <template x-if="selectedRole === 'pengawas'">
                        <div x-transition x-data="dapurMultiSelect()">
                            <label class="form-label" for="dapur_ids">Dapur yang Diawasi <span class="required">*</span></label>
                            
                            <!-- Search input -->
                            <div class="input-with-icon mb-2">
                                <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" 
                                       x-model="searchQuery" 
                                       placeholder="Cari dapur..."
                                       class="input-field select-enhanced"
                                       autocomplete="off">
                            </div>
                            
                            <!-- Selected chips -->
                            <div x-show="selectedDapurIds.length > 0" class="mt-2 flex flex-wrap gap-2" x-transition>
                                <template x-for="id in selectedDapurIds" :key="id">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                        <span x-text="getDapurName(id)"></span>
                                        <button type="button" @click="removeDapur(id)" class="hover:text-[var(--color-primary)]/70 focus:outline-none" aria-label="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            <!-- Checkbox list with filter -->
                            <div class="mt-2 max-h-60 overflow-y-auto border border-[var(--color-border)] rounded-lg p-3 bg-[var(--color-surface)]/50">
                                <template x-for="dapur in filteredDapurs" :key="dapur.id">
                                    <label class="flex items-center gap-3 p-2 hover:bg-[var(--color-surface)] rounded cursor-pointer">
                                        <input type="checkbox" 
                                               :value="dapur.id" 
                                               :checked="selectedDapurIds.includes(dapur.id)"
                                               @change="toggleDapur(dapur.id)"
                                               class="w-4 h-4 text-[var(--color-primary)] border-[var(--color-border)] rounded focus:ring-[var(--color-primary)] focus:ring-2">
                                        <span class="text-sm" x-text="dapur.nama + ' (' + dapur.lokasi + ')'"></span>
                                    </label>
                                </template>
                                <template x-if="filteredDapurs.length === 0 && searchQuery">
                                    <div class="text-center text-[var(--color-text-muted)] text-sm py-4">
                                        Tidak ada dapur yang cocok dengan "<span x-text="searchQuery"></span>"
                                    </div>
                                </template>
                                <template x-if="!searchQuery && selectedDapurIds.length === dapurs.length">
                                    <div class="text-center text-[var(--color-text-muted)] text-sm py-4">
                                        Semua dapur sudah dipilih
                                    </div>
                                </template>
                            </div>

                            <!-- Hidden inputs for form submission -->
                            <template x-for="id in selectedDapurIds" :key="id">
                                <input type="hidden" name="dapur_ids[]" :value="id">
                            </template>

                            <p class="form-hint mt-2" x-text="'Dipilih: ' + selectedDapurIds.length + ' dapur'"></p>
                            @error('dapur_ids')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>

                    <!-- Manajerial - no dapur assignment -->
                    <template x-if="selectedRole === 'manajerial'">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="font-medium text-blue-800">Role Manajerial tidak memerlukan assignment dapur</p>
                                    <p class="text-sm text-blue-700">Manajerial memiliki akses read-only ke semua dapur dan laporan.</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Default: select role first -->
                    <template x-if="!selectedRole">
                        <div class="p-4 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg">
                            <div class="flex items-center gap-3 text-[var(--color-text-muted)]">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Pilih role terlebih dahulu untuk melihat opsi assignment dapur</span>
                            </div>
                        </div>
                    </template>
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
document.addEventListener('alpine:init', () => {
    // Autocomplete component for Kepala Dapur
    Alpine.data('dapurAutocomplete', () => ({
        searchQuery: '',
        showDropdown: false,
        highlightedIndex: -1,
        
        get filteredDapurs() {
            if (!this.searchQuery) return this.$root.dapurs || [];
            const q = this.searchQuery.toLowerCase();
            return (this.$root.dapurs || []).filter(d => 
                d.nama.toLowerCase().includes(q) || 
                d.lokasi.toLowerCase().includes(q)
            );
        },
        
        selectDapur(dapur) {
            this.$root.kepalaDapurId = dapur.id;
            this.searchQuery = dapur.nama;
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },
        
        focusFirstOption() {
            if (this.filteredDapurs.length > 0) {
                this.highlightedIndex = 0;
            }
        },
        
        focusLastOption() {
            if (this.filteredDapurs.length > 0) {
                this.highlightedIndex = this.filteredDapurs.length - 1;
            }
        },
        
        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredDapurs.length) {
                this.selectDapur(this.filteredDapurs[this.highlightedIndex]);
            }
        }
    }));

    // Multi-select component for Pengawas
    Alpine.data('dapurMultiSelect', () => ({
        searchQuery: '',
        
        get filteredDapurs() {
            if (!this.searchQuery) return this.$root.dapurs || [];
            const q = this.searchQuery.toLowerCase();
            return (this.$root.dapurs || []).filter(d => 
                d.nama.toLowerCase().includes(q) || 
                d.lokasi.toLowerCase().includes(q)
            );
        },
        
        toggleDapur(id) {
            this.$root.toggleDapur(id);
        },
        
        removeDapur(id) {
            this.$root.removeDapur(id);
        },
        
        getDapurName(id) {
            return this.$root.getDapurName(id);
        }
    }));

    // Main form component
    Alpine.data('userForm', (initialRole = '', initialDapurIds = [], initialKepalaDapurId = null) => ({
        selectedRole: initialRole,
        selectedDapurIds: initialDapurIds || [],
        kepalaDapurId: initialKepalaDapurId || '',
        dapurs: @json($dapurData),

        get selectedKepalaDapur() {
            const selected = this.dapurs.find(d => d.id == this.kepalaDapurId);
            return selected?.kepala_dapur_name || '';
        },

        handleRoleChange() {
            // Reset dapur fields when role changes
            this.selectedDapurIds = [];
            this.kepalaDapurId = '';
        },

        toggleDapur(id) {
            const idx = this.selectedDapurIds.indexOf(id);
            if (idx === -1) {
                this.selectedDapurIds.push(id);
            } else {
                this.selectedDapurIds.splice(idx, 1);
            }
        },

        removeDapur(id) {
            this.selectedDapurIds = this.selectedDapurIds.filter(d => d !== id);
        },

        getDapurName(id) {
            const d = this.dapurs.find(d => d.id == id);
            return d ? d.nama : id;
        }
    }));
});
</script>
@endpush