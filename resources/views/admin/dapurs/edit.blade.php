@extends('admin.layouts.app', ['pageTitle' => 'Edit Dapur: ' . $dapur->nama])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Edit Dapur: {{ $dapur->nama }}</h1>
            <div class="muted">Perbarui data dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.dapurs.update', $dapur) }}" class="space-y-6" x-data="dapurForm()">
        @csrf
        @method('PUT')

        {{-- Basic Info Section --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">⌂</span>
                Informasi Dasar
            </div>

            <div class="form-field">
                <label class="form-label" for="nama">
                    Nama Dapur <span class="required">*</span>
                </label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $dapur->nama) }}" required 
                       class="input-field" placeholder="Contoh: Dapur Pusat Kota" autofocus>
                @error('nama')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="3" class="input-field" placeholder="Alamat lengkap dapur">{{ old('alamat', $dapur->alamat) }}</textarea>
            </div>

            {{-- Wilayah --}}
            <div class="form-section">
                <div class="form-section-title">
                    <span class="section-icon">🗺️</span>
                    Wilayah
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-field">
                        <label class="form-label" for="kabupaten_kota">
                            Kabupaten / Kota <span class="required">*</span>
                        </label>
                        <select id="kabupaten_kota" name="kabupaten_kota" 
                                class="input-field" required
                                x-model="form.kabupaten_kota"
                                @change="onKabupatenChange()">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            @foreach(\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab)
                                <option value="{{ $kab }}" {{ old('kabupaten_kota', $dapur->kabupaten_kota) == $kab ? 'selected' : '' }}>
                                    {{ $kab }}
                                </option>
                            @endforeach
                        </select>
                        @error('kabupaten_kota')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="kecamatan">
                            Kecamatan <span class="required">*</span>
                        </label>
                        <select id="kecamatan" name="kecamatan" 
                                class="input-field" required
                                x-model="form.kecamatan"
                                :disabled="kecamatanOptions.length === 0">
                            <option value="">-- Pilih Kabupaten/Kota dulu --</option>
                            <template x-for="kec in kecamatanOptions" :key="kec">
                                <option :value="kec" :selected="kec === form.kecamatan">@{{ kec }}</option>
                            </template>
                        </select>
                        @error('kecamatan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="provinsi">Provinsi</label>
                        <input id="provinsi" type="text" name="provinsi" value="Banten" 
                               class="input-field" readonly>
                        <input type="hidden" name="provinsi" value="Banten">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Perbarui Dapur</span>
            </button>
            <a href="{{ route('admin.dapurs.index') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>

@php
$kecamatanMapping = [];
$controller = app(\App\Http\Controllers\DapurController::class);
foreach (\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab) {
    $kecs = $controller->getKnownKecamatan($kab);
    if (!empty($kecs)) {
        $kecamatanMapping[$kab] = $kecs;
    }
}
@endphp

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dapurForm', () => ({
        form: {
            kabupaten_kota: '@js(old("kabupaten_kota", $dapur->kabupaten_kota))',
            kecamatan: '@js(old("kecamatan", $dapur->kecamatan))',
        },
        kecamatanOptions: [],
        allKecamatan: @json($kecamatanMapping),

        onKabupatenChange() {
            const kabupaten = this.form.kabupaten_kota;
            if (!kabupaten) {
                this.kecamatanOptions = [];
                this.form.kecamatan = '';
                return;
            }
            
            this.kecamatanOptions = this.allKecamatan[kabupaten] || [];
            
            // Reset kecamatan if not in new options
            if (!this.kecamatanOptions.includes(this.form.kecamatan)) {
                this.form.kecamatan = '';
            }
        },

        init() {
            // Initialize kecamatan options if kabupaten already selected (e.g. after validation error)
            if (this.form.kabupaten_kota) {
                this.onKabupatenChange();
            }
        }
    }));
});
</script>
@endpush
@endsection