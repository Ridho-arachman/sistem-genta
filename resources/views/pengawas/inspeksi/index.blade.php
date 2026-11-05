@extends('pengawas.layouts.app', ['pageTitle' => 'Inspeksi Dapur'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Form Inspeksi Dapur</h1>
        <div class="muted">Isi formulir pemeriksaan dapur (1 foto dilengkapi peta + GPS)</div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999] hidden">
    <div class="bg-[var(--color-surface)] p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-[var(--color-primary)] border-t-transparent rounded-full animate-spin"></div>
        <p class="text-[var(--color-text)] font-medium">Memproses foto…</p>
        <p class="text-xs text-[var(--color-text-muted)]">Harap tunggu, GPS dan peta sedang dimuat</p>
    </div>
</div>

<div class="panel max-w-[720px]">
    <form method="POST" action="{{ route('pengawas.laporan.store') }}" id="laporanForm" class="space-y-6">
        @csrf

        {{-- Status laporan (default diperiksa) --}}
        <input type="hidden" name="status" value="diperiksa">

        {{-- Pilih Dapur --}}
        @include('pengawas.components.dapur-select', ['dapurs' => $dapurs ?? collect()])
        <span id="error-dapur" class="text-red-600 text-sm mt-1 hidden">Pilih dapur dulu</span>

        {{-- Satu card untuk semua indikator (hasil dropdown saja, foto di bawah) --}}
        <div class="card p-4 bg-[var(--color-surface)]/50 space-y-6">

            {{-- Indikator 1: Kebersihan dan sanitasi --}}
            @include('pengawas.components.hasil-indikator', ['namaIndikator' => 'Kebersihan dan sanitasi'])
            <span id="error-hasil-1" class="text-red-600 text-sm mt-1 hidden">Pilih hasil: Kebersihan dan sanitasi</span>

            {{-- Indikator 2: Penyimpanan bahan --}}
            @include('pengawas.components.hasil-indikator', ['namaIndikator' => 'Penyimpanan bahan'])
            <span id="error-hasil-2" class="text-red-600 text-sm mt-1 hidden">Pilih hasil: Penyimpanan bahan</span>

            {{-- Indikator 3: Proses pengolahan --}}
            @include('pengawas.components.hasil-indikator', ['namaIndikator' => 'Proses pengolahan'])
            <span id="error-hasil-3" class="text-red-600 text-sm mt-1 hidden">Pilih hasil: Proses pengolahan</span>

            {{-- Indikator 4: Distribusi makanan --}}
            @include('pengawas.components.hasil-indikator', ['namaIndikator' => 'Distribusi makanan'])
            <span id="error-hasil-4" class="text-red-600 text-sm mt-1 hidden">Pilih hasil: Distribusi makanan</span>

            {{-- Indikator 5: Peralatan dan fasilitas --}}
            @include('pengawas.components.hasil-indikator', ['namaIndikator' => 'Peralatan dan fasilitas'])
            <span id="error-hasil-5" class="text-red-600 text-sm mt-1 hidden">Pilih hasil: Peralatan dan fasilitas</span>
            @error('hasil')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Foto tunggal (Selfie/Foto Utama) --}}
        @include('pengawas.components.foto-upload')
        <span id="error-foto" class="text-red-600 text-sm mt-1 hidden">Foto dapur belum diisi</span>

        {{-- Error GPS --}}
        <span id="error-gps" class="text-red-600 text-sm mt-1 hidden">GPS gagal didapatkan. Lokasi wajib terisi sebelum kirim laporan.</span>

        {{-- Hidden inputs for photo data (base64) and GPS --}}
        <input type="hidden" name="selfie_photo" id="photo_data" value="">
        <input type="hidden" name="latitude" id="latitudeInput" value="">
        <input type="hidden" name="longitude" id="longitudeInput" value="">
        <input type="hidden" name="address" id="addressInput" value="">

        {{-- Error untuk foto dan GPS (validasi server) --}}
        @error('selfie_photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('latitude')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('longitude')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        {{-- Catatan Pengawas --}}
        @include('pengawas.components.catatan-input')

        {{-- Submit --}}
        <div class="flex gap-3 justify-end pt-4 border-t border-[var(--color-border)]">
            <button type="submit" class="btn-primary" id="submitBtn">
                <span id="btn-text">Kirim Laporan</span>
                <span id="btn-spinner" class="hidden flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memproses…
                </span>
            </button>
        </div>
    </form>
</div>

<!-- Photo Preview Modal -->
<div id="photoPreviewModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[1000] hidden">
    <div class="relative max-w-4xl max-h-[90vh] w-full mx-4">
        <button type="button" data-action="close-photo-preview" class="absolute -top-12 right-0 bg-[var(--color-surface)] text-[var(--color-text)] p-2 rounded-full cursor-pointer shadow-lg z-10" aria-label="Tutup preview">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="bg-[var(--color-background)] rounded-lg overflow-hidden w-full max-h-[70vh]">
            <img id="previewImage" src="" alt="Preview foto" class="w-full max-h-[70vh] object-contain">
            <div id="previewInfo" class="p-4 border-t border-[var(--color-border)]">
                <p id="previewGps" class="text-sm text-[var(--color-text-muted)]"></p>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{ asset('js/pengawas-inspeksi.js') }}"></script>
@endpush