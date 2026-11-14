{{-- Dipakai di form inspeksi; butuh scope Alpine `inspeksiForm` --}}
<div class="space-y-3">
    <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Foto Dapur <span class="text-red-500">*</span></label>
    <div class="relative group cursor-pointer" @click="openCamera()">
        <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
            <span class="text-sm font-medium text-[var(--color-text)] flex items-center gap-2">
                <svg class="w-5 h-5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Ambil foto dapur <span class="text-red-500">*</span></span>
            </span>
        </div>
        <input type="file" x-ref="photoInput" accept="image/*" capture="environment" class="hidden" @change="handlePhotoSelected($event)">
    </div>

    <div x-show="photo?.dataUrl" x-cloak class="mt-2">
        <div class="relative group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2">
            <img :src="photo?.dataUrl" alt="Foto dapur" class="w-full h-48 object-cover rounded-md cursor-pointer">
            <button type="button" @click.stop="removePhoto()" class="absolute top-2 right-4 z-50 bg-white p-2 rounded-full text-red-600 hover:text-red-800 shadow-lg" aria-label="Hapus foto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            <div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <template x-if="photo?.gpsStatus === 'loading'">
                    <div>
                        <span class="text-[9px] font-medium text-[var(--color-secondary)]">⏳ GPS memproses…</span>
                        <p class="text-[9px] text-[var(--color-text-muted)]">Tunggu hingga selesai sebelum kirim laporan</p>
                    </div>
                </template>
                <template x-if="photo?.gpsStatus !== 'loading' && photo?.lat && photo?.lng">
                    <div>
                        <span class="text-[9px] font-medium text-[var(--color-primary)]">✅ GPS tersimpan</span>
                        <p class="text-[9px] text-[var(--color-text-muted)]" x-text="`${photo.lat}, ${photo.lng}`"></p>
                        <p class="text-[9px] text-[var(--color-text-muted)]" x-text="photo.address || 'GPS tersimpan'"></p>
                    </div>
                </template>
                <template x-if="photo?.gpsStatus === 'error'">
                    <div>
                        <span class="text-[9px] font-medium text-red-600">❌ GPS gagal</span>
                        <p class="text-[9px] text-[var(--color-text-muted)]">Lokasi tidak tersedia. Coba ambil foto ulang.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@error('selfie_photo')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
