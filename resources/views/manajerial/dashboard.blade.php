@extends('manajerial.layouts.app', ['pageTitle' => 'Dashboard Manajerial'])

@section('content')
<div class="role-switch flex gap-2 mb-6" aria-label="Pilih tampilan" style="display:flex;gap:8px;flex-wrap:wrap">
    <button class="btn-primary active" data-role="admin" style="background:var(--color-primary);color:white">Admin Website</button>
    <button class="btn-secondary" data-role="pengawas" style="background:white;border:1px solid var(--color-border);color:var(--color-secondary)">Pengawas App</button>
    <button class="btn-secondary" data-role="pimpinan" style="background:white;border:1px solid var(--color-border);color:var(--color-secondary)">Pimpinan Website</button>
</div>

<div class="heading">
    <div>
        <div class="eyebrow">Genta · Manajerial Panel</div>
        <h1 class="text-2xl font-bold">Dashboard Manajerial</h1>
        <div class="muted text-[var(--color-text-muted)] mt-1">Ringkasan kondisi sistem dan pengawasan (Read Only)</div>
    </div>
</div>

<div class="content-grid">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="stat-card">
            <div class="stat-top">
                <span>Total Pengawas</span>
                <span class="stat-icon bg-blue-100 text-blue-600">⌂</span>
            </div>
            <div class="stat-value">{{ $stats['total_pengawas'] }}</div>
            <div class="stat-foot">User dengan peran pengawas</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Total Manajerial</span>
                <span class="stat-icon bg-purple-100 text-purple-600">⚙</span>
            </div>
            <div class="stat-value">{{ $stats['total_manajerial'] }}</div>
            <div class="stat-foot">User dengan peran manajerial</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Total Kepala Dapur</span>
                <span class="stat-icon bg-orange-100 text-orange-600">⌂</span>
            </div>
            <div class="stat-value">{{ $stats['total_kepala_dapur'] }}</div>
            <div class="stat-foot">User dengan peran kepala dapur</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Total Dapur</span>
                <span class="stat-icon bg-green-100 text-green-600">⌂</span>
            </div>
            <div class="stat-value">{{ $stats['total_dapur'] }}</div>
            <div class="stat-foot">Lokasi terdaftar dalam sistem</div>
        </div>
    </div>

    {{-- CCTV Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card">
            <div class="stat-top">
                <span>Total CCTV</span>
                <span class="stat-icon bg-[var(--color-primary)]/10 text-[var(--color-primary)]">▣</span>
            </div>
            <div class="stat-value">{{ $stats['total_cctv'] }}</div>
            <div class="stat-foot">Total perangkat terdaftar</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Online</span>
                <span class="stat-icon bg-green-100 text-green-600">▣</span>
            </div>
            <div class="stat-value text-green-600">{{ $stats['cctv_online'] }}</div>
            <div class="stat-foot">Perangkat online</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Offline</span>
                <span class="stat-icon bg-red-100 text-red-600">▣</span>
            </div>
            <div class="stat-value text-red-600">{{ $stats['cctv_offline'] }}</div>
            <div class="stat-foot">Perlu perhatian</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Maintenance</span>
                <span class="stat-icon bg-yellow-100 text-yellow-600">▣</span>
            </div>
            <div class="stat-value text-yellow-600">{{ $stats['cctv_maintenance'] }}</div>
            <div class="stat-foot">Sedang maintenance</div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card">
            <div class="stat-top">
                <span>Absensi Hari Ini</span>
                <span class="stat-icon bg-indigo-100 text-indigo-600">▣</span>
            </div>
            <div class="stat-value">{{ $stats['absensi_hari_ini'] }}</div>
            <div class="stat-foot">Check-in hari ini</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>SP Warning Aktif</span>
                <span class="stat-icon bg-red-100 text-red-600">⚠</span>
            </div>
            <div class="stat-value">{{ $stats['sp_warnings_aktif'] }}</div>
            <div class="stat-foot">Perlu ditindaklanjuti</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-top">
                <span>Anomali CCTV (Open)</span>
                <span class="stat-icon bg-orange-100 text-orange-600">▣</span>
            </div>
            <div class="stat-value">{{ $stats['anomaly_reports_open'] }}</div>
            <div class="stat-foot">Belum diselesaikan</div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid" style="display:grid;gap:16px;grid-template-columns:1fr">
        {{-- Dapur Status Cards --}}
        <div class="panel">
            <div class="panel-head"><h2>Status Dapur</h2><input class="search" placeholder="Cari nama dapur…" oninput="filterKitchens(this.value)"></div>
            <div id="kitchenList" class="kitchen-list">
                @forelse ($dapurs as $dapur)
                    <div class="kitchen-card">
                        <div class="kitchen-card-content">
                            <div class="kitchen-name">{{ $dapur->nama }}</div>
                            <div class="kitchen-meta">{{ $dapur->lokasi ?? '-' }}</div>
                            <div class="progress-bar">
                                <i class="progress-fill progress-{{ $dapur->status === 'operasional' ? 'ok' : ($dapur->status === 'perlu_perhatian' ? 'warn' : 'off') }}" style="width:{{ $dapur->progress ?? 0 }}%"></i>
                            </div>
                            <div class="small-label">
                                <span>{{ $dapur->cctvs_count ?? 0 }} kamera</span>
                                <span>{{ $dapur->progress ?? 0 }}%</span>
                            </div>
                        </div>
                        <span class="status-badge @if($dapur->status === 'operasional') status-ok @elseif($dapur->status === 'perlu_perhatian') status-warn @else status-off @endif">
                            {{ ucfirst($dapur->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-[var(--color-text-muted)]">
                        Belum ada dapur terdaftar
                    </div>
                @endforelse
            </div>
            <div style="margin-top:13px;text-align:right">
                <a href="{{ route('manajerial.dapurs.index') }}" class="btn btn-light">Lihat semua dapur →</a>
            </div>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:1fr">
            {{-- Camera Panel --}}
            <div class="panel">
                <div class="panel-head"><h2>Pratinjau CCTV</h2><small>Status terkini perangkat</small></div>
                <div class="camera">
                    <span class="cam-tag inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span class="live-dot" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:green;margin-right:6px;animation:pulse 2s infinite"></span> LIVE
                    </span>
                    <div class="camera-art" style="background:#1a1a1a;border-radius:8px;padding:24px;text-align:center;min-height:200px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:12px;margin-top:8px">
                        <div style="font-size:48px;color:#444">▥</div>
                        <strong style="color:#fff">Pratinjau Kamera Dapur</strong>
                        <small style="color:#666">Stream CCTV akan tampil setelah integrasi perangkat</small>
                    </div>
                    <span class="cam-time">CAM-01 · {{ $dapurs->first()->nama ?? 'Dapur' }}</span>
                </div>
                <div class="camera-list mt-4 space-y-2">
                    <div class="cam-row flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
                        <div class="cam-left flex items-center gap-3">
                            <div class="cam-mini w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">▣</div>
                            <div><b>Area persiapan</b><div class="kitchen-meta">{{ $dapurs->first()->nama ?? 'Dapur' }}</div></div>
                        </div>
                        <span class="status-badge status-ok">Online</span>
                    </div>
                    <div class="cam-row flex items-center justify-between p-3 bg-[var(--color-surface)] rounded-lg">
                        <div class="cam-left flex items-center gap-3">
                            <div class="cam-mini w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center">▣</div>
                            <div><b>Area memasak</b><div class="kitchen-meta">{{ $dapurs->skip(1)->first()->nama ?? 'Dapur' }}</div></div>
                        </div>
                        <span class="status-badge status-warn">Periksa</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="panel">
            <div class="panel-head"><h2>Aktivitas Terbaru</h2><small>Hari ini</small></div>
            <div class="activity space-y-4">
                <div class="activity-row flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="activity-icon w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">✓</div>
                    <div class="flex-1"><strong>Absensi check-in baru</strong><p class="text-sm text-[var(--color-text-muted)]">{{ $recentAbsensi->first()->user->name ?? 'Pengawas' }} · {{ $dapurs->first()->nama ?? 'Dapur' }}</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->format('H:i') }}</time>
                </div>
                <div class="activity-row flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="activity-icon w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600">⚑</div>
                    <div class="flex-1"><strong>Temuan perlu tindak lanjut</strong><p class="text-sm text-[var(--color-text-muted)]">Area penyimpanan</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subMinutes(30)->format('H:i') }}</time>
                </div>
                <div class="activity-row flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="activity-icon w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">▣</div>
                    <div class="flex-1"><strong>Status kamera berubah</strong><p class="text-sm text-[var(--color-text-muted)]">Kamera CAM-03</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subHour()->format('H:i') }}</time>
                </div>
                <div class="activity-row flex items-start gap-3 p-3 bg-[var(--color-surface)] rounded-lg">
                    <div class="activity-icon w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">▤</div>
                    <div class="flex-1"><strong>Laporan inspeksi diperiksa</strong><p class="text-sm text-[var(--color-text-muted)]">Admin pusat</p></div>
                    <time class="text-sm text-[var(--color-text-muted)]">{{ now()->subHour(2)->format('H:i') }}</time>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection