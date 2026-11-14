@extends('admin.layouts.app', ['pageTitle' => 'Tambah User Baru'])

@section('page_content')
<div class="max-w-5xl mx-auto">
    <x-admin::page-heading title="Tambah User Baru" subtitle="Hanya bisa mendaftarkan role: Pengawas, Manajerial, Kepala Dapur" />

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" x-data="userForm(@js($dapurData))" x-cloak>
        @csrf

        <x-admin::panel title="Data Pengguna">
            <x-slot:iconSvg>♙</x-slot:iconSvg>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <x-admin::form-field label="Nama Lengkap" name="name" required class="lg:col-span-1">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                               class="input input-bordered" placeholder="Masukkan nama lengkap">
                    </div>
                </x-admin::form-field>

                {{-- Email --}}
                <x-admin::form-field label="Email" name="email" required class="lg:col-span-1">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               class="input input-bordered" placeholder="Masukkan email">
                    </div>
                </x-admin::form-field>

                {{-- Role --}}
                <x-admin::form-field label="Role" name="role" required class="lg:col-span-1">
                    <select id="role" name="role" required class="input input-bordered" x-model="selectedRole" @change="handleRoleChange()">
                        <option value="">Pilih role</option>
                        <option value="pengawas">Pengawas</option>
                        <option value="manajerial">Manajerial</option>
                        <option value="kepala_dapur">Kepala Dapur</option>
                    </select>
                </x-admin::form-field>

                {{-- Dapur Assignment (full width) --}}
                <div class="flex flex-col gap-1.5 lg:col-span-2">
                    <!-- Kepala Dapur -->
                    <template x-if="selectedRole === 'kepala_dapur'">
                        <div x-transition x-data="dapurAutocomplete()">
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="dapur_id">Dapur <span class="text-xs text-[var(--color-primary)]">*</span></label>
                            <div class="relative">
                                <div class="relative">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input type="text" 
                                           id="dapur_search" 
                                           x-model="searchQuery" 
                                           @focus="showDropdown = true"
                                           @keydown.down.prevent="focusFirstOption()"
                                           @keydown.up.prevent="focusLastOption()"
                                           @keydown.enter.prevent="selectHighlighted()"
                                           @keydown.escape="showDropdown = false"
                                           placeholder="Cari dapur..."
                                           class="input input-bordered"
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
                            
                            <!-- Warning if dapur already has kepala -->
                            <div x-show="selectedKepalaDapur" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm" x-transition>
                                <span class="font-medium">Perhatian: </span>
                                <span x-text="'Dapur ini sudah memiliki Kepala Dapur: ' + selectedKepalaDapur"></span>
                                <br><span class="font-medium">Menimpah akan mengubah assignment kepala dapur.</span>
                            </div>
                            @error('dapur_id')
                                <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>

                    <!-- Pengawas -->
                    <template x-if="selectedRole === 'pengawas'">
                        <div x-transition x-data="dapurMultiSelect()">
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="dapur_ids">Dapur yang Diawasi <span class="text-xs text-[var(--color-primary)]">*</span></label>
                            
                            <!-- Search input -->
                            <div class="relative mb-2">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" 
                                       x-model="searchQuery" 
                                       placeholder="Cari dapur..."
                                       class="input input-bordered"
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

                            <p class="text-[11px] text-[var(--color-text-muted)] mt-2" x-text="'Dipilih: ' + selectedDapurIds.length + ' dapur'"></p>
                            @error('dapur_ids')
                                <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
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

                {{-- Password --}}
                <x-admin::form-field label="Password" name="password" required hint="Minimal 8 karakter, kombinasikan huruf & angka" class="lg:col-span-1">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="input input-bordered" placeholder="Minimal 8 karakter">
                    </div>
                </x-admin::form-field>

                {{-- Konfirmasi Password --}}
                <x-admin::form-field label="Konfirmasi Password" name="password_confirmation" required class="lg:col-span-1">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="input input-bordered" placeholder="Ulangi password">
                    </div>
                </x-admin::form-field>
            </div>
        </x-admin::panel>

        {{-- Form Actions (full width) --}}
        <div class="lg:col-span-2 flex justify-end gap-3 pt-4 border-t border-[var(--color-border)]">
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan User</span>
            </button>
        </div>
    </form>
</div>
@endsection
