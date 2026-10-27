@extends('pengawas.layouts.app', ['pageTitle' => 'Dashboard Pengawas'])

@section('content')
<div class="role-switch" aria-label="Pilih tampilan">
    <a href="{{ route('admin.dashboard') }}" class="role-btn">Admin Website</a>
    <button class="role-btn active" data-role="pengawas">Pengawas App</button>
    <a href="{{ route('manajerial.dashboard') }}" class="role-btn">Pimpinan Website</a>
</div>

<div class="heading">
    <div>
        <div class="eyebrow">Genta · Pengawasan MBG</div>
        <h1>Dashboard Pengawas</h1>
        <div class="muted">Kondisi operasional dapur dan pelaporan · {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('pengawas.absensi.create') }}" class="btn-primary">＋ Isi Checklist</a>
        <a href="{{ route('pengawas.laporan.create') }}" class="btn-secondary">＋ Buat Laporan</a>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat-top">
            <span>Total Dapur Ditugaskan</span>
            <span class="stat-icon" style="background:var(--color-primary)/10;color:var(--color-primary)">⌂</span>
        </div>
        <div class="stat-value">{{ $stats['total_dapur'] ?? $dapurs->count() }}</div>
        <div class="stat-foot">Lokasi dalam penugasan Anda</div>
    </div>

    <div class="stat">
        <div class="stat-top">
            <span>Kamera Online</span>
            <span class="stat-icon" style="background:#22C55E15;color:#166534">▣</span>
        </div>
        <div class="stat-value" style="color:#166534">{{ $stats['cctv_online'] ?? 0 }}<span style="font-size:15px;color:#8a9a92">/{{ $stats['total_cctv'] ?? 0 }}</span></div>
        <div class="stat-foot"><span style="color:#166534">{{ $stats['total_cctv'] > 0 ? number_format(($stats['cctv_online'] ?? 0) / ($stats['total_cctv'] ?? 1) * 100, 1) : 0 }}%</span> status konektivitas</div>
    </div>

    <div class="stat">
        <div class="stat-top">
            <span>Laporan Hari Ini</span>
            <span class="stat-icon" style="background:#3B82F615;color:#1E40AF">▤</span>
        </div>
        <div class="stat-value">{{ $stats['laporan_hari_ini'] ?? 0 }}</div>
        <div class="stat-foot">Checklist & laporan dikirim</div>
    </div>

    <div class="stat">
        <div class="stat-top">
            <span>Temuan Terbuka</span>
            <span class="stat-icon" style="background:#F59E0B15;color:#854D0E">⚑</span>
        </div>
        <div class="stat-value">{{ $stats['temuan_terbuka'] ?? 0 }}</div>
        <div class="stat-foot">Perlu tindak lanjut</div>
    </div>
</div>

<div class="content-grid">
    {{-- Left Column: Dapur Status --}}
    <div>
        <div class="panel">
            <div class="panel-head">
                <h2>Status Dapur</h2>
                <input type="text" class="search" placeholder="Cari nama dapur…" oninput="filterKitchens(this.value)" id="kitchenSearch">
            </div>
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
                <a href="{{ route('pengawas.absensi.index') }}" class="btn-light">Lihat semua dapur →</a>
            </div>
        </div>
    </div>

    {{-- Right Column: CCTV + Activity --}}
    <div style="display:grid;gap:16px">
        {{-- Camera Panel --}}
        <div class="panel">
            <div class="panel-head">
                <h2>Pratinjau CCTV</h2>
                <small>Status terkini perangkat</small>
            </div>
            <div class="camera">
                <span class="cam-tag">
                    <span class="live-dot"></span> LIVE
                </span>
                <div class="camera-art">
                    <div>▥</div>
                    <strong>Pratinjau Kamera Dapur</strong>
                    <small>Stream CCTV akan tampil setelah integrasi perangkat</small>
                </div>
                <span class="cam-time">CAM-01 · {{ $dapurs->first()->nama ?? 'Dapur' }}</span>
            </div>
            <div class="camera-list">
                <div class="cam-row">
                    <div class="cam-left">
                        <div class="cam-mini ok">▣</div>
                        <div><b>Area persiapan</b><div class="kitchen-meta">{{ $dapurs->first()->nama ?? 'Dapur' }}</div></div>
                    </div>
                    <span class="status-badge status-ok">Online</span>
                </div>
                <div class="cam-row">
                    <div class="cam-left">
                        <div class="cam-mini warn">▣</div>
                        <div><b>Area memasak</b><div class="kitchen-meta">{{ $dapurs->skip(1)->first()->nama ?? 'Dapur' }}</div></div>
                    </div>
                    <span class="status-badge status-warn">Periksa</span>
                </div>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="panel">
            <div class="panel-head">
                <h2>Aktivitas Terbaru</h2>
                <small>Hari ini</small>
            </div>
            <div class="activity">
                <div class="activity-row">
                    <div class="activity-icon ok">✓</div>
                    <div><strong>Checklist dapur dikirim</strong><p>{{ auth()->user()->name ?? 'Pengawas' }} · {{ $dapurs->first()->nama ?? 'Dapur' }}</p></div>
                    <time>{{ now()->format('H:i') }}</time>
                </div>
                <div class="activity-row">
                    <div class="activity-icon warn">⚑</div>
                    <div><strong>Temuan perlu tindak lanjut</strong><p>Area penyimpanan</p></div>
                    <time>{{ now()->subMinutes(30)->format('H:i') }}</time>
                </div>
                <div class="activity-row">
                    <div class="activity-icon info">▣</div>
                    <div><strong>Status kamera berubah</strong><p>Kamera CAM-03</p></div>
                    <time>{{ now()->subHour()->format('H:i') }}</time>
                </div>
                <div class="activity-row">
                    <div class="activity-icon purple">▤</div>
                    <div><strong>Laporan inspeksi diperiksa</strong><p>Admin pusat</p></div>
                    <time>{{ now()->subHours(2)->format('H:i') }}</time>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Kitchen search filter
    function filterKitchens(query) {
        const list = document.getElementById('kitchenList');
        if (!list) return;
        const cards = list.querySelectorAll('.kitchen-card');
        const q = query.toLowerCase();
        cards.forEach(card => {
            const name = card.querySelector('.kitchen-name')?.textContent.toLowerCase() || '';
            const meta = card.querySelector('.kitchen-meta')?.textContent.toLowerCase() || '';
            card.style.display = (name.includes(q) || meta.includes(q)) ? 'flex' : 'none';
        });
    }

    // Role switch buttons (links already handle navigation)
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (btn.tagName === 'BUTTON') {
                e.preventDefault();
                document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                toast('Tampilan Pengawas aktif');
            }
        });
    });

    function toast(msg) {
        let el = document.getElementById('toast');
        el.textContent = msg;
        el.style.display = 'block';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.style.display = 'none', 2800);
    }
    let toastTimer;
</script>
@endpush