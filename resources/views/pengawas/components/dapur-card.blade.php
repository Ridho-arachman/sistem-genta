@props(['dapur'])

{{--
    Satu baris tugas dapur di dashboard pengawas (nama + lokasi + aksi).

    <x-pengawas::dapur-card :dapur="$dapur" />
--}}
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
    <div class="flex gap-2 mt-4">
        <a href="{{ route('pengawas.dapurs.show', $dapur->id) }}" class="flex-1 px-4 py-2.5 text-center btn btn-outline btn-primary text-sm font-medium">Lihat Lokasi</a>
        <a href="{{ route('pengawas.inspeksi.create', ['dapur' => $dapur->id]) }}" class="flex-1 px-4 py-2.5 text-center btn btn-primary text-sm font-medium">Mulai Inspeksi</a>
    </div>
</div>