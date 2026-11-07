<div class="flex flex-col sm:flex-row gap-4 p-4 border-b border-[var(--color-border)] last:border-0">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3">
            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
            <div>
                <div class="text-sm font-semibold text-[var(--color-text)]">{{ $dapur->nama }}</div>
                <div class="text-xs text-[var(--color-text-muted)]">{{ $dapur->lokasi ?? 'Wilayah' }} · Pemeriksaan hari ini</div>
            </div>
        </div>
    </div>
    <div class="flex gap-4 mt-4 sm:mt-0">
        <a href="{{ route('pengawas.dapurs.show', $dapur->id) }}" class="flex-1 px-4 py-2.5 text-center border border-[var(--color-primary)] text-[var(--color-primary)] rounded-lg text-sm font-medium hover:bg-[var(--color-primary)]/5 transition-colors">Lihat Lokasi</a>
        <a href="{{ route('pengawas.inspeksi.create', ['dapur' => $dapur->id]) }}" class="flex-1 px-4 py-2.5 text-center bg-[var(--color-primary)] text-white rounded-lg text-sm font-medium hover:bg-[var(--color-primary-dark)] transition-colors">Mulai Inspeksi</a>
    </div>
</div>