@extends('admin.layouts.app', ['pageTitle' => 'Tambah User Baru'])

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Tambah User Baru</h1>
            <div class="muted">Hanya bisa mendaftarkan role: Pengawas, Manajerial, Kepala Dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" x-data="userForm()" x-cloak>
        @csrf

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
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" 
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
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" 
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
                        <option value="">Pilih role</option>
                        <option value="pengawas">Pengawas</option>
                        <option value="manajerial">Manajerial</option>
                        <option value="kepala_dapur">Kepala Dapur</option>
                    </select>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dapur Assignment (full width) --}}
                <div class="form-field lg:col-span-2">
                    <!-- Kepala Dapur -->
                    <template x-if="selectedRole === 'kepala_dapur'">
                        <div x-transition>
                            <label class="form-label" for="dapur_id">Dapur <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <select id="dapur_id" name="dapur_id" required class="input-field select-enhanced" x-model="kepalaDapurId">
                                    <option value="">Pilih dapur</option>
                                    @foreach($dapurs as $dapur)
                                        <option value="{{ $dapur->id }}" :data-has-kepala="{{ $dapur->kepalaDapur ? 'true' : 'false' }}" :data-kepala-name="{{ $dapur->kepalaDapur?->name }}">
                                            {{ $dapur->nama }} ({{ $dapur->lokasi }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="selectedKepalaDapur" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm" x-transition>
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
                        <div x-transition>
                            <label class="form-label" for="dapur_ids">Dapur yang Diawasi <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
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

                            <!-- Checkbox list -->
                            <div class="mt-2 max-h-60 overflow-y-auto border border-[var(--color-border)] rounded-lg p-3 bg-[var(--color-surface)]/50">
                                <template x-for="dapur in dapurs" :key="dapur.id">
                                    <label class="flex items-center gap-3 p-2 hover:bg-[var(--color-surface)] rounded cursor-pointer">
                                        <input type="checkbox" 
                                               :value="dapur.id" 
                                               :checked="selectedDapurIds.includes(dapur.id)"
                                               @change="toggleDapur(dapur.id)"
                                               class="w-4 h-4 text-[var(--color-primary)] border-[var(--color-border)] rounded focus:ring-[var(--color-primary)] focus:ring-2">
                                        <span class="text-sm" x-text="dapur.nama + ' (' + dapur.lokasi + ')'"></span>
                                    </label>
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

                {{-- Password --}}
                <div class="form-field lg:col-span-1">
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

                {{-- Konfirmasi Password --}}
                <div class="form-field lg:col-span-1">
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
        </div>

        {{-- Form Actions (full width) --}}
        <div class="form-actions lg:col-span-2 flex justify-end gap-3 pt-4 border-t border-[var(--color-border)]">
            <a href="{{ route('admin.users') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan User</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userForm', () => ({
        selectedRole: '',
        selectedDapurIds: [],
        kepalaDapurId: '',
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