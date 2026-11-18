@extends('admin.layouts.app', ['pageTitle' => 'Dashboard Manajerial'])

@section('page_content')
<x-admin::page-heading title="Dashboard Manajerial" subtitle="Ringkasan kondisi seluruh dapur pangan · {{ now()->translatedFormat('l, d F Y') }}" />

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <x-admin::stat-card label="Total Dapur" :value="$stats['total_dapur']" note="Lokasi terdaftar dalam sistem" icon="⌂" />
    <x-admin::stat-card label="Kamera Online" :value="$stats['cctv_online'] . '/' . $stats['total_cctv']" icon="▣" value-class="text-green-800"
        :note="$stats['cctv_offline'] . ' offline · ' . $stats['cctv_maintenance'] . ' maintenance'" />
    <x-admin::stat-card label="Pengawas" :value="$stats['total_pengawas']" note="Pengawas lapangan aktif" icon="☺" icon-class="bg-blue-100 text-blue-800" />
    <x-admin::stat-card label="Laporan Hari Ini" :value="$stats['laporan_hari_ini']" note="Seluruh dapur" icon="▤" icon-class="bg-blue-100 text-blue-800" />
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <x-admin::stat-card label="SP Aktif" :value="$stats['sp_warnings_aktif']" note="Surat peringatan berjalan" icon="⚑" value-class="text-red-800" icon-class="bg-red-100 text-red-800" />
    <x-admin::stat-card label="Temuan Terbuka" :value="$stats['anomaly_reports_open']" note="Anomali CCTV belum selesai" icon="!" value-class="text-red-800" icon-class="bg-red-100 text-red-800" />
    <x-admin::stat-card label="Kepala Dapur" :value="$stats['total_kepala_dapur']" note="Kepala dapur terdaftar" icon="⌂" />
    <x-admin::stat-card label="Manajerial" :value="$stats['total_manajerial']" note="Pimpinan" icon="☺" icon-class="bg-blue-100 text-blue-800" />
</div>

<x-admin::panel title="Dapur Terpantau" subtitle="Status kamera per dapur" class="mb-4">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Dapur</th>
                    <th>Kamera</th>
                    <th>Online</th>
                    <th>Wilayah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dapurs as $dapur)
                    <tr>
                        <td class="font-semibold">{{ $dapur->nama }}</td>
                        <td>{{ $dapur->cctvs->count() }}</td>
                        <td>{{ $dapur->cctvs->where('status', 'online')->count() }}</td>
                        <td>{{ $dapur->kecamatan ?? $dapur->lokasi ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-[var(--color-text-muted)]">Belum ada dapur terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin::panel>

<x-admin::panel title="Laporan Inspeksi Terbaru" subtitle="10 laporan terakhir">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Pengawas</th>
                    <th>Dapur</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentLaporans as $laporan)
                    <tr>
                        <td>{{ $laporan->createdBy?->name ?? '-' }}</td>
                        <td>{{ $laporan->dapur?->nama ?? '-' }}</td>
                        <td>{{ $laporan->created_at->format('d M Y H:i') }}</td>
                        <td>{{ ucfirst($laporan->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-[var(--color-text-muted)]">Belum ada laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin::panel>
@endsection
