@extends('admin.layouts.app', ['pageTitle' => 'Edit Dapur: ' . $dapur->nama])

@section('page_content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Edit Dapur: {{ $dapur->nama }}</h1>
            <div class="muted">Perbarui data dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.dapurs.update', $dapur) }}" class="space-y-6" id="dapur-form">
        @csrf
        @method('PUT')

        {{-- Row 1: Nama Dapur --}}
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

            <div class="form-field">
                <label class="form-label" for="map_embed_url">Embed Google Maps</label>
                <textarea id="map_embed_url" name="map_embed_url" rows="4" class="input-field" placeholder="Tempelkan src URL iframe Google Maps…">{{ old('map_embed_url', $dapur->map_embed_url) }}</textarea>
                <p class="text-[11px] text-[var(--color-text-muted)] mt-1">Contoh: https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.12…</p>
            </div>
        </div>

        {{-- Row 2: Wilayah --}}
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
                            class="input-field" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach(\App\Http\Controllers\DapurController::BANTEN_KABUPATEN_KOTA as $kab)
                            <option value="{{ $kab }}" {{ old('kabupaten_kota', $dapur->kabupaten_kota) === $kab ? 'selected' : '' }}>{{ $kab }}</option>
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
                            id="kecamatan-select">
                        <option value="">-- Pilih Kecamatan --</option>
                        @php
                            $controller = app(\App\Http\Controllers\DapurController::class);
                            $selectedKab = old('kabupaten_kota', $dapur->kabupaten_kota);
                            $selectedKec = old('kecamatan', $dapur->kecamatan);
                            $kecsForSelectedKab = $controller->getKnownKecamatan($selectedKab) ?? [];
                        @endphp
                        @foreach($kecsForSelectedKab as $kec)
                            <option value="{{ $kec }}" {{ $selectedKec === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                        @endforeach
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
document.addEventListener('DOMContentLoaded', function () {
    const kabupatenSelect = document.getElementById('kabupaten_kota');
    const kecamatanSelect = document.getElementById('kecamatan-select');
    const allKecamatan = @json($kecamatanMapping);

    function updateKecamatanOptions(kabupaten) {
        const currentValue = kecamatanSelect.value;
        kecamatanSelect.innerHTML = '';

        if (!kabupaten) {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '-- Pilih Kabupaten/Kota dulu --';
            kecamatanSelect.appendChild(emptyOpt);
            kecamatanSelect.disabled = true;
            return;
        }

        const options = allKecamatan[kabupaten] || [];
        kecamatanSelect.disabled = false;

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '-- Pilih Kecamatan --';
        kecamatanSelect.appendChild(defaultOpt);

        let found = false;
        options.forEach(function (kec) {
            const opt = document.createElement('option');
            opt.value = kec;
            opt.textContent = kec;
            if (kec === currentValue) {
                opt.selected = true;
                found = true;
            }
            kecamatanSelect.appendChild(opt);
        });

        // If current value not in new options, reset to empty
        if (!found && currentValue) {
            kecamatanSelect.value = '';
        }
    }

    kabupatenSelect.addEventListener('change', function () {
        updateKecamatanOptions(this.value);
    });
});
</script>
@endpush
@endsection