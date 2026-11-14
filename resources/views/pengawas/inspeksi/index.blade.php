@extends('pengawas.layouts.app', ['pageTitle' => 'Inspeksi Dapur'])

@section('page_content')
<x-pengawas::page-heading
    title="Form Inspeksi Dapur"
    subtitle="Isi formulir pemeriksaan dapur (1 foto dilengkapi peta + GPS)" />

<div x-data="inspeksiForm" @keydown.escape.window="closePreview()">
    {{-- Loading Overlay --}}
    <div x-show="loading" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]">
        <div class="bg-[var(--color-surface)] p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-[var(--color-primary)] border-t-transparent rounded-full animate-spin"></div>
            <p class="text-[var(--color-text)] font-medium">Memproses foto…</p>
            <p class="text-xs text-[var(--color-text-muted)]">Harap tunggu, GPS dan peta sedang dimuat</p>
        </div>
    </div>

    <div class="card card-bordered max-w-[720px]">
        <div class="card-body">
            <form method="POST" action="{{ route('pengawas.laporan.store') }}" id="laporanForm" x-ref="form" @submit.prevent="submit()" class="space-y-6">
                @csrf

                {{-- Status laporan (default diperiksa) --}}
                <input type="hidden" name="status" value="diperiksa">

                {{-- Pilih Dapur --}}
                <div @change="errors.dapur = ''">
                    @include('pengawas.components.dapur-select', ['dapurs' => $dapurs ?? collect(), 'selectedDapurId' => $selectedDapurId ?? null])
                    <span x-show="errors.dapur" x-text="errors.dapur" class="text-red-600 text-sm mt-1"></span>
                </div>

                {{-- Satu card untuk semua indikator (hasil dropdown saja, foto di bawah) --}}
                <div class="card p-4 bg-[var(--color-surface)]/50 space-y-6">
                    @foreach (['Kebersihan dan sanitasi', 'Penyimpanan bahan', 'Proses pengolahan', 'Distribusi makanan', 'Peralatan dan fasilitas'] as $i => $indikator)
                        <div @change="errors.hasil[{{ $i }}] = ''">
                            @include('pengawas.components.hasil-indikator', ['namaIndikator' => $indikator])
                            <span x-show="errors.hasil[{{ $i }}]" x-text="errors.hasil[{{ $i }}]" class="text-red-600 text-sm mt-1"></span>
                        </div>
                    @endforeach
                    @error('hasil')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto tunggal (Selfie/Foto Utama) --}}
                @include('pengawas.components.foto-upload')
                <span x-show="errors.foto" x-text="errors.foto" class="text-red-600 text-sm mt-1"></span>

                {{-- Error GPS --}}
                <span x-show="errors.gps" x-text="errors.gps" class="text-red-600 text-sm mt-1"></span>

                {{-- Hidden inputs for photo data (base64) and GPS --}}
                <input type="hidden" name="selfie_photo" :value="photo?.dataUrl || ''">
                <input type="hidden" name="latitude" :value="photo?.lat || ''">
                <input type="hidden" name="longitude" :value="photo?.lng || ''">
                <input type="hidden" name="address" :value="photo?.address || ''">

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
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <span x-show="!submitting">Kirim Laporan</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Photo Preview Modal --}}
    <div x-show="previewOpen" x-cloak class="fixed inset-0 bg-black/80 flex items-center justify-center z-[1000]" @click.self="closePreview()">
        <div class="relative max-w-4xl max-h-[90vh] w-full mx-4">
            <button type="button" @click="closePreview()" class="absolute -top-12 right-0 bg-[var(--color-surface)] text-[var(--color-text)] p-2 rounded-full cursor-pointer shadow-lg z-10" aria-label="Tutup preview">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="bg-[var(--color-background)] rounded-lg overflow-hidden w-full max-h-[70vh]">
                <img :src="photo?.dataUrl" alt="Preview foto" class="w-full max-h-[70vh] object-contain">
                <div class="p-4 border-t border-[var(--color-border)]">
                    <p class="text-sm text-[var(--color-text-muted)]" x-text="previewGps"></p>
                    <p x-show="photo?.address" class="text-[0.7rem] text-[var(--color-text-muted)]" x-text="photo?.address"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
