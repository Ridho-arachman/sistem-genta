@extends('admin.layouts.app', ['pageTitle' => 'Anomali CCTV'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Laporan Anomali CCTV</h2>
            <p class="text-[var(--color-text-muted)]">Kelola laporan anomali CCTV dari admin</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.cctv-anomalies.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-success">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Excel
            </a>
            <a href="{{ route('admin.cctv-anomalies.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-secondary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                CSV
            </a>
            <a href="{{ route('admin.cctv-anomalies.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Laporan
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="md:w-64">
                <select name="cctv_id" class="input-field">
                    <option value="">Semua CCTV</option>
                    @foreach ($cctvs as $cctv)
                        <option value="{{ $cctv->id }}" {{ request('cctv_id') == $cctv->id ? 'selected' : '' }}>{{ $cctv->display_name }} ({{ $cctv->dapur->nama }})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-32">
                <select name="severity" class="input-field">
                    <option value="">Semua Severity</option>
                    <option value="rendah" {{ request('severity') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="sedang" {{ request('severity') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="tinggi" {{ request('severity') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                </select>
            </div>
            <div class="md:w-32">
                <select name="status" class="input-field">
                    <option value="">Semua Status</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="md:w-48">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-field" placeholder="Tanggal Mulai">
            </div>
            <div class="md:w-48">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-field" placeholder="Tanggal Akhir">
            </div>
            <button type="submit" class="btn-secondary whitespace-nowrap">Filter</button>
            <a href="{{ route('admin.cctv-anomalies.index') }}" class="btn-secondary whitespace-nowrap">Reset</a>
        </form>
    </div>

    {{-- Anomalies Table --}}
    <div class="card overflow-hidden">
        @if ($anomalies->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--color-surface)]">
                        <tr>
                            <th class="table-th">Tanggal</th>
                            <th class="table-th">CCTV</th>
                            <th class="table-th">Dapur</th>
                            <th class="table-th">Area/Kamera</th>
                            <th class="table-th">Deskripsi</th>
                            <th class="table-th">Severity</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Dilaporkan</th>
                            <th class="table-th">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($anomalies as $anomaly)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="table-td">{{ $anomaly->created_at->format('d/m/Y H:i') }}</td>
                            <td class="table-td font-medium">{{ $anomaly->cctv->display_name }}</td>
                            <td class="table-td">{{ $anomaly->cctv->dapur->nama }}</td>
                            <td class="table-td">{{ $anomaly->cctv->section ?? '-' }} / Cam {{ $anomaly->cctv->camera_number ?? '-' }}</td>
                            <td class="table-td max-w-xs truncate">{{ $anomaly->description }}</td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($anomaly->severity === 'tinggi') badge-danger
                                    @elseif($anomaly->severity === 'sedang') badge-warning
                                    @else badge-gray @endif">
                                    {{ ucfirst($anomaly->severity) }}
                                </span>
                            </td>
                            <td class="table-td">
                                <span class="badge 
                                    @if($anomaly->status === 'baru') badge-danger
                                    @elseif($anomaly->status === 'diproses') badge-warning
                                    @else badge-success @endif">
                                    {{ ucfirst($anomaly->status) }}
                                </span>
                            </td>
                            <td class="table-td">{{ $anomaly->admin->name }}</td>
                            <td class="table-td">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.cctv-anomalies.show', $anomaly) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a>
                                    <form action="{{ route('admin.cctv-anomalies.update', $anomaly) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="input-field text-xs py-1">
                                            <option value="baru" {{ $anomaly->status === 'baru' ? 'selected' : '' }}>Baru</option>
                                            <option value="diproses" {{ $anomaly->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                            <option value="selesai" {{ $anomaly->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $anomalies->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Belum Ada Laporan Anomali</h3>
                <p class="text-[var(--color-text-muted)] mt-1">Belum ada anomali CCTV yang dilaporkan</p>
                <a href="{{ route('admin.cctv-anomalies.create') }}" class="btn-primary mt-4 inline-block">Buat Laporan Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection