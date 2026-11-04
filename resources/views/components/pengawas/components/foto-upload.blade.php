<div class="space-y-3">
    <label class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Foto Dapur <span class="text-red-500">*</span></label>
    <div class="relative group cursor-pointer" @click="toggleCamera()">
        <div class="flex items-center justify-between p-3 bg-[var(--color-surface)]/50 rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] transition-colors">
            <span class="text-sm font-medium text-[var(--color-text)] flex items-center gap-2">
                <svg class="w-5 h-5 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Ambil foto dapur <span class="text-red-500">*</span></span>
            </span>
        </div>
        <input type="file" id="photo_input" accept="image/*" capture="environment" class="opacity-0 absolute inset-0 w-0 h-0" data-action="handle-photo-selected">
        <div id="photo_preview" class="mt-2 hidden"></div>
    </div>
</div>

@error('selfie_photo')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror