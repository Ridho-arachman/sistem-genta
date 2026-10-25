@extends('manajerial.layouts.app', ['pageTitle' => 'SP Warning'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Surat Peringatan (SP)</h2>
            <p class="text-[var(--color-text-muted)]">Monitoring SP pengawas (Read Only)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manajerial.sp-warnings.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-success"><svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>Excel</a>
            <a href="{{ route('manajerial.sp-warnings.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-secondary"><svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>CSV</a>
        </div>
    </div>

    <div class="card p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="md:w-64"><select name="user_id" class="input-field"><option value="">Semua Pengawas</option>@foreach ($pengawas as $p)<option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach</select></div>
            <div class="md:w-32"><select name="level" class="input-field"><option value="">Semua Level</option><option value="1" {{ request('level') == '1' ? 'selected' : '' }}>SP-1</option><option value="2" {{ request('level') == '2' ? 'selected' : '' }}>SP-2</option><option value="3" {{ request('level') == '3' ? 'selected' : '' }}>SP-3</option></select></div>
            <div class="md:w-32"><select name="status" class="input-field"><option value="">Semua Status</option><option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option><option value="dicabut" {{ request('status') === 'dicabut' ? 'selected' : '' }}>Dicabut</option><option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option></select></div>
            <div class="md:w-48"><input type="date" name="start_date" value="{{ request('start_date') }}" class="input-field" placeholder="Tanggal Mulai"></div>
            <div class="md:w-48"><input type="date" name="end_date" value="{{ request('end_date') }}" class="input-field" placeholder="Tanggal Akhir"></div>
            <button type="submit" class="btn-secondary whitespace-nowrap">Filter</button>
            <a href="{{ route('manajerial.sp-warnings.index') }}" class="btn-secondary whitespace-nowrap">Reset</a>
        </form>
    </div>

    <div class="card overflow-hidden">
        @if ($warnings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full"><thead class="bg-[var(--color-surface)]"><tr>
                    <th class="table-th">Tanggal</th><th class="table-th">Pengawas</th><th class="table-th">Level</th><th class="table-th">Alasan</th><th class="table-th">Status</th><th class="table-th">Diterbitkan Oleh</th><th class="table-th">Aksi</th>
                </tr></thead><tbody class="divide-y divide-[var(--color-border)]">
                    @foreach ($warnings as $warning)
                    <tr class="hover:bg-[var(--color-surface)]">
                        <td class="table-td">{{ $warning->issued_at->format('d/m/Y H:i') }}</td>
                        <td class="table-td font-medium">{{ $warning->user->name }}</td>
                        <td class="table-td"><span class="badge @if($warning->level == 1) badge-warning @elseif($warning->level == 2) badge-danger @else bg-red-500/20 text-red-800 font-bold @endif">SP-{{ $warning->level }}</span></td>
                        <td class="table-td max-w-xs truncate">{{ $warning->reason }}</td>
                        <td class="table-td"><span class="badge @if($warning->status === 'aktif') badge-danger @elseif($warning->status === 'dicabut') badge-gray @else badge-success @endif">{{ ucfirst($warning->status) }}</span></td>
                        <td class="table-td">{{ $warning->admin->name }}</td>
                        <td class="table-td"><a href="{{ route('manajerial.sp-warnings.show', $warning) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a></td>
                    </tr>
                    @endforeach
                </tbody></table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">{{ $warnings->links() }}</div>
        @else
            <div class="p-12 text-center"><svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Surat Peringatan</h3></div>
        @endif
    </div>
</div>
@endsection
