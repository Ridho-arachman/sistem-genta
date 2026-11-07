@props(['namaIndikator'])

<div class="space-y-3 pt-2 border-t border-[var(--color-border)]">
    <h3 class="font-medium text-[var(--color-secondary)]">{{ $namaIndikator }} <span class="text-red-500">*</span></h3>
    <input type="hidden" name="kategori[]" value="{{ $namaIndikator }}">
    
    {{-- Select Hasil --}}
    <div class="mt-3">
        <select name="hasil[]" class="input-field w-full">
            <option value="">-- Pilih Hasil --</option>
            <option value="diperiksa">Sesuai standar</option>
            <option value="temuan_sedang">Perlu perbaikan</option>
            <option value="temuan_kritis">Temuan kritis</option>
        </select>
    </div>
</div>