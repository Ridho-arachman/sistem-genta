@extends('pengawas.layouts.app', ['pageTitle' => 'Absensi'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Absensi Saya</h2>
            <p class="text-[var(--color-text-muted)]">Riwayat kehadiran Anda</p>
        </div>
        <a href="{{ route('pengawas.absensi.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Absensi Baru
        </a>
    </div>

    {{-- Filters --}}
    <div class="card p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="md:w-64">
                <select name="dapur_id" class="input-field">
                    <option value="">Semua Dapur</option>
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}" {{ request('dapur_id') == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-48">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-field" placeholder="Tanggal Mulai">
            </div>
            <div class="md:w-48">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-field" placeholder="Tanggal Akhir">
            </div>
            <button type="submit" class="btn-secondary whitespace-nowrap">Filter</button>
            <a href="{{ route('pengawas.absensi.index') }}" class="btn-secondary whitespace-nowrap">Reset</a>
        </form>
    </div>

    {{-- Absensi Table --}}
    <div class="card overflow-hidden">
        @if ($absensis->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th">Tanggal</th>
                            <th class="table-th">Dapur</th>
                            <th class="table-th">Check-in</th>
                            <th class="table-th">Check-out</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Foto</th>
                            <th class="table-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($absensis as $absensi)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td">{{ $absensi->created_at->format('d/m/Y') }}</td>
                            <td class="table-td">{{ $absensi->dapur->nama }}</td>
                            <td class="table-td">{{ $absensi->check_in_at ? $absensi->check_in_at->format('H:i:s') : '<span class="text-[var(--color-text-muted)]">-</span>' }}</td>
                            <td class="table-td">{{ $absensi->check_out_at ? $absensi->check_out_at->format('H:i:s') : '<span class="text-[var(--color-text-muted)]">-</span>' }}</td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($absensi->status === 'hadir') badge-success
                                    @elseif($absensi->status === 'izin') badge-warning
                                    @elseif($absensi->status === 'cuti') badge-gray
                                    @else badge-danger
                                    @endif">
                                    {{ ucfirst($absensi->status) }}
                                </span>
                            </td>
                            <td class="table-td">
                                @if ($absensi->selfie_path)
                                    <a href="{{ asset('storage/' . $absensi->selfie_path) }}" target="_blank" class="text-[var(--color-primary)] hover:underline text-sm">Lihat Foto</a>
                                @else
                                    <span class="text-[var(--color-text-muted)]">-</span>
                                @endif
                            </td>
                            <td class="table-td">
                                @if (!$absensi->check_out_at)
                                    <form action="{{ route('pengawas.absensi.checkout', $absensi) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-primary text-sm py-1 px-3">Check-out</button>
                                    </form>
                                @else
                                    <span class="badge badge-success">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $absensis->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Riwayat Absensi</h3>
                <p class="text-[var(--color-text-muted)] mt-1">Mulai dengan absensi check-in pertama Anda</p>
                <a href="{{ route('pengawas.absensi.create') }}" class="btn-primary mt-4 inline-block">Absensi Sekarang</a>
            </div>
        @endif
    </div>
</div>
@endsection