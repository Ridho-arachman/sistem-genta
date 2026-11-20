@extends('admin.layouts.app', ['pageTitle' => 'Buat Laporan Anomali CCTV'])

@section('page_content')

    <x-admin::page-heading title="Laporan Anomali CCTV" subtitle="Laporkan kejadian anomali pada kamera CCTV" />

    <form method="POST" action="{{ route('admin.cctv-anomalies.store') }}" class="space-y-6">
        @csrf

        <x-admin::panel title="Data Anomali" icon="warning">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="lg:col-span-2">
                    <x-admin::form-field label="CCTV" name="cctv_id" required>
                        <div class="relative" x-data="cctvAutocomplete(@js($cctvs), @js(old('cctv_id', $cctvId)))">
                            <input type="text"
                                   id="cctv_search"
                                   x-model="searchQuery"
                                   @focus="showDropdown = true"
                                   @keydown.down.prevent="focusFirstOption()"
                                   @keydown.up.prevent="focusLastOption()"
                                   @keydown.enter.prevent="selectHighlighted()"
                                   @keydown.escape="showDropdown = false"
                                   placeholder="Cari nama CCTV atau dapur..."
                                   class="input input-bordered w-full"
                                   autocomplete="off"
                                   role="combobox"
                                   :aria-expanded="showDropdown"
                                   aria-controls="cctv_options"
                                   aria-label="CCTV">
                            <input type="hidden" id="cctv_id" name="cctv_id" x-bind:value="selectedId">

                            {{-- Daftar hasil pencarian --}}
                            <div x-show="showDropdown && filteredCctvs.length > 0"
                                 id="cctv_options"
                                 role="listbox"
                                 class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border border-[var(--color-border)] bg-white shadow-lg"
                                 x-transition
                                 @click.outside="showDropdown = false">
                                <template x-for="(cctv, index) in filteredCctvs" :key="cctv.id">
                                    <button type="button"
                                            role="option"
                                            :aria-selected="highlightedIndex === index"
                                            @click="selectCctv(cctv)"
                                            @mouseover="highlightedIndex = index"
                                            :class="['flex w-full items-center gap-3 px-4 py-2 text-left hover:bg-[var(--color-primary)]/10', highlightedIndex === index ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : '']">
                                        <span class="font-medium" x-text="cctv.label"></span>
                                        <span class="ml-auto text-sm text-[var(--color-text-muted)]" x-text="cctv.dapur_name"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="showDropdown && searchQuery && filteredCctvs.length === 0"
                                 class="absolute z-10 mt-1 w-full rounded-lg border border-[var(--color-border)] bg-white px-4 py-3 text-center text-sm text-[var(--color-text-muted)] shadow-lg">
                                Tidak ada CCTV yang cocok
                            </div>
                        </div>
                    </x-admin::form-field>
                </div>

                <x-admin::form-field label="Tingkat Keparahan" name="severity" required>
                    <select id="severity" name="severity" required class="select select-bordered w-full">
                        <option value="">Pilih tingkat</option>
                        <option value="rendah" @selected(old('severity') === 'rendah')>Rendah - Gangguan minor</option>
                        <option value="sedang" @selected(old('severity', 'sedang') === 'sedang')>Sedang - Perlu perhatian</option>
                        <option value="tinggi" @selected(old('severity') === 'tinggi')>Tinggi - Kritis, butuh tindakan cepat</option>
                    </select>
                </x-admin::form-field>

                <div class="lg:col-span-2">
                    <x-admin::form-field label="Deskripsi Anomali" name="description" required>
                        <textarea id="description" name="description" required rows="4" class="textarea textarea-bordered w-full" placeholder="Jelaskan anomali yang terdeteksi...">{{ old('description') }}</textarea>
                    </x-admin::form-field>
                </div>
            </div>

        </x-admin::panel>

        <div class="flex flex-wrap gap-3 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Kirim Laporan</span>
            </button>
            <a href="{{ route('admin.cctv-anomalies.index') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>

@endsection
