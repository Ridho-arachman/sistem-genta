@extends('admin.layouts.app', ['pageTitle' => 'Dashboard Admin'])

@section('content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Admin Panel</div>
        <h1>Dashboard Admin</h1>
        <div class="muted">Ringkasan kondisi sistem dan pengawasan</div>
    </div>
    <div class="heading-actions">
        <a href="{{ route('admin.dapurs.create') }}" class="btn-action btn-action-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Dapur</span>
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn-action btn-action-secondary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Tambah User</span>
        </a>
    </div>
</div>

<div class="stats">
    {{-- Total Dapur --}}
    <div class="stat">
        <div class="stat-top">
            <span>Total Dapur</span>
            <span class="stat-icon" style="background:#22C55E15;color:#166534">⌂</span>
        </div>
        <div class="stat-value">{{ $stats['total_dapur'] }}</div>
        <div class="stat-foot">Lokasi terdaftar dalam sistem</div>
    </div>

    {{-- Kamera Online --}}
    <div class="stat">
        <div class="stat-top">
            <span>Kamera Online</span>
            <span class="stat-icon" style="background:#22C55E15;color:#166534">▣</span>
        </div>
        <div class="stat-value" style="color:#166534">
            {{ $stats['cctv_online'] }}/{{ $stats['total_cctv'] }}
        </div>
        <div class="stat-foot">
            {{ $stats['total_cctv'] > 0 ? number_format($stats['cctv_online'] / $stats['total_cctv'] * 100, 1, ',', '.') : '0' }}% status konektivitas
        </div>
    </div>

    {{-- Laporan Hari Ini --}}
    <div class="stat">
        <div class="stat-top">
            <span>Laporan Hari Ini</span>
            <span class="stat-icon" style="background:#3B82F615;color:#1E40AF">▤</span>
        </div>
        <div class="stat-value">{{ $stats['laporan_hari_ini'] }}</div>
        <div class="stat-foot">
            {{ $stats['laporan_hari_ini_selesai'] }} selesai · {{ $stats['laporan_hari_ini'] - $stats['laporan_hari_ini_selesai'] }} perlu tindak lanjut
        </div>
    </div>

    {{-- Temuan Terbuka --}}
    <div class="stat">
        <div class="stat-top">
            <span>Temuan Terbuka</span>
            <span class="stat-icon" style="background:#EF444415;color:#991B1B">⚑</span>
        </div>
        <div class="stat-value" style="color:#991B1B">{{ $stats['anomaly_reports_open'] }}</div>
        <div class="stat-foot">Perlu ditinjau oleh admin</div>
    </div>

    {{-- Pengawas Aktif --}}
    <div class="stat">
        <div class="stat-top">
            <span>Pengawas Aktif</span>
            <span class="stat-icon" style="background:#A855F715;color:#6B21A8">♙</span>
        </div>
        <div class="stat-value" style="color:#6B21A8">{{ $stats['total_pengawas'] }}</div>
        <div class="stat-foot">
            {{ $stats['pengawas_without_dapur'] }} belum diassign dapur
        </div>
    </div>

    {{-- Kepala Dapur --}}
    <div class="stat">
        <div class="stat-top">
            <span>Kepala Dapur</span>
            <span class="stat-icon" style="background:#3B82F615;color:#1E40AF">♚</span>
        </div>
        <div class="stat-value" style="color:#1E40AF">{{ $stats['total_kepala_dapur'] }}</div>
        <div class="stat-foot">
            {{ $stats['kepala_dapur_without_dapur'] }} belum diassign dapur
        </div>
    </div>
</div>

<div class="panel export-panel" style="margin-bottom: 16px;">
    <div class="panel-head">
        <h2>Ekspor Data Absensi</h2>
        <small>Filter dengan range tanggal, pilih format</small>
    </div>
    <form method="GET" action="{{ route('admin.absensi.export') }}" class="export-form" id="exportForm">
        {{-- Date Presets --}}
        <div class="export-presets" role="group" aria-label="Pilihan cepat rentang tanggal">
            <button type="button" class="preset-btn" data-preset="today" title="Hari ini">Hari ini</button>
            <button type="button" class="preset-btn" data-preset="week" title="7 hari terakhir">Minggu ini</button>
            <button type="button" class="preset-btn" data-preset="month" title="Bulan ini">Bulan ini</button>
            <button type="button" class="preset-btn" data-preset="last_month" title="Bulan lalu">Bulan lalu</button>
            <button type="button" class="preset-btn" data-preset="custom" title="Rentang kustom">Kustom</button>
        </div>

        {{-- Date Inputs --}}
        <div class="export-row" id="dateFields">
            <div class="export-field date-field">
                <label for="start_date" class="export-label">
                    <svg class="export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Tanggal Mulai
                </label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="input-field export-input" placeholder="Pilih tanggal" aria-describedby="startDateHelp">
                </div>
                <span id="startDateHelp" class="field-hint">{{ request('start_date') ? 'Terseleksi: ' . \Carbon\Carbon::parse(request('start_date'))->isoFormat('D MMM YYYY') : 'Belum dipilih' }}</span>
            </div>

            <div class="export-field date-field">
                <label for="end_date" class="export-label">
                    <svg class="export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                    Tanggal Akhir
                </label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="input-field export-input" placeholder="Pilih tanggal" aria-describedby="endDateHelp" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                </div>
                <span id="endDateHelp" class="field-hint">{{ request('end_date') ? 'Terseleksi: ' . \Carbon\Carbon::parse(request('end_date'))->isoFormat('D MMM YYYY') : 'Belum dipilih' }}</span>
            </div>

            <div class="export-field">
                <label for="format" class="export-label">
                    <svg class="export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Format
                </label>
                <div class="input-wrapper">
                    <select name="format" id="format" class="input-field export-input export-select">
                        <option value="xlsx" {{ request('format') === 'xlsx' ? 'selected' : '' }}>Excel (.xlsx)</option>
                        <option value="pdf" {{ request('format') === 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="csv" {{ request('format') === 'csv' ? 'selected' : '' }}>CSV</option>
                    </select>
                </div>
            </div>

            <div class="export-field">
                <label for="dapur_id" class="export-label">
                    <svg class="export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Dapur (opsional)
                </label>
                <div class="input-wrapper">
                    <select name="dapur_id" id="dapur_id" class="input-field export-input export-select">
                        <option value="">Semua Dapur</option>
                        @foreach ($dapurs as $dapur)
                            <option value="{{ $dapur->id }}" {{ request('dapur_id') == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Validation Error --}}
        <div class="export-error hidden" id="dateError" role="alert" aria-live="polite">
            <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>Tanggal mulai tidak boleh melebihi tanggal akhir</span>
        </div>

        {{-- Actions --}}
        <div class="export-actions">
            <button type="submit" class="btn-export btn-export-primary" id="exportBtn">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="btn-text">Ekspor Data</span>
                <span class="btn-loading hidden">
                    <svg class="spinner" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Menyiapkan...
                </span>
            </button>
            <a href="{{ route('admin.absensi.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-export btn-export-secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Excel Cepat</span>
            </a>
            <button type="button" class="btn-export btn-export-ghost" id="clearFilters" title="Bersihkan filter">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>Bersihkan</span>
            </button>
        </div>
    </form>
</div>

<div class="content-grid">
    {{-- Left Column: Dapur Status + Pengawas Assignment --}}
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
                <a href="{{ route('admin.dapurs.index') }}" class="btn-light">Lihat semua dapur →</a>
            </div>
        </div>

        {{-- Pengawas Assignment Overview --}}
        <div class="panel" style="margin-top:16px">
            <div class="panel-head">
                <h2>Penugasan Pengawas</h2>
                <small>{{ $pengawas->count() }} pengawas terdaftar</small>
            </div>
            <div class="kitchen-list" style="gap:8px">
                @forelse ($pengawas as $p)
                    <div class="kitchen-card" style="align-items:center">
                        <div class="kitchen-card-content" style="min-width:0">
                            <div class="kitchen-name">{{ $p['name'] }}</div>
                            <div class="kitchen-meta">{{ $p['email'] }}</div>
                            <div class="small-label" style="margin-top:4px">
                                <span class="badge badge-primary">{{ $p['dapur_count'] }} dapur</span>
                            </div>
                        </div>
                        <div class="kitchen-meta" style="text-align:right;max-width:280px;white-space:normal">
                            {{ $p['dapur_names'] ?: '<span class="text-[var(--color-text-muted)]">Belum diassign</span>' }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-[var(--color-text-muted)]">
                        Belum ada pengawas terdaftar
                    </div>
                @endforelse
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
                    <div><strong>Absensi check-in baru</strong><p>{{ $recentAbsensi->first()->user->name ?? 'Pengawas' }} · {{ $dapurs->first()->nama ?? 'Dapur' }}</p></div>
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

    // Export Form Enhancements
    (function() {
        const form = document.getElementById('exportForm');
        if (!form) return;

        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const exportBtn = document.getElementById('exportBtn');
        const btnText = exportBtn?.querySelector('.btn-text');
        const btnLoading = exportBtn?.querySelector('.btn-loading');
        const dateError = document.getElementById('dateError');
        const startDateHelp = document.getElementById('startDateHelp');
        const endDateHelp = document.getElementById('endDateHelp');
        const clearBtn = document.getElementById('clearFilters');
        const presetBtns = form.querySelectorAll('.preset-btn');
        const dateFields = document.getElementById('dateFields');

        // Preset date ranges
        const presets = {
            today: { start: 0, end: 0 },
            week: { start: -6, end: 0 },
            month: { start: -29, end: 0 },
            last_month: { start: -59, end: -30 }
        };

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function formatDisplay(date) {
            return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
        }

        function applyPreset(preset) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const start = new Date(today);
            start.setDate(today.getDate() + preset.start);

            const end = new Date(today);
            end.setDate(today.getDate() + preset.end);

            startDate.value = formatDate(start);
            endDate.value = formatDate(end);

            updateHints();
            validateDates();
            updatePresetButtons(preset);
        }

        function updatePresetButtons(activePreset) {
            presetBtns.forEach(btn => {
                const isActive = btn.dataset.preset === (activePreset === 'custom' ? '' : activePreset);
                btn.classList.toggle('active', isActive);
            });
        }

        function updateHints() {
            if (startDateHelp && startDate.value) {
                startDateHelp.textContent = 'Terseleksi: ' + formatDisplay(new Date(startDate.value + 'T00:00:00'));
            } else if (startDateHelp) {
                startDateHelp.textContent = 'Belum dipilih';
            }
            if (endDateHelp && endDate.value) {
                endDateHelp.textContent = 'Terseleksi: ' + formatDisplay(new Date(endDate.value + 'T00:00:00'));
            } else if (endDateHelp) {
                endDateHelp.textContent = 'Belum dipilih';
            }
        }

        function validateDates() {
            if (startDate.value && endDate.value && startDate.value > endDate.value) {
                dateError.classList.remove('hidden');
                exportBtn.disabled = true;
                return false;
            } else {
                dateError.classList.add('hidden');
                exportBtn.disabled = false;
                return true;
            }
        }

        function setLoading(isLoading) {
            exportBtn.disabled = isLoading || !validateDates();
            if (btnText) btnText.classList.toggle('hidden', isLoading);
            if (btnLoading) btnLoading.classList.toggle('hidden', !isLoading);
        }

        // Preset button handlers
        presetBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const preset = btn.dataset.preset;
                if (preset === 'custom') {
                    presetBtns.forEach(b => b.classList.remove('active'));
                    startDate.focus();
                    return;
                }
                applyPreset(presets[preset]);
            });
        });

        // Date change handlers
        [startDate, endDate].forEach(input => {
            input.addEventListener('change', () => {
                updateHints();
                validateDates();
                // Clear preset selection when manually editing
                presetBtns.forEach(b => b.classList.remove('active'));
            });
        });

        // Form submit - show loading
        form.addEventListener('submit', (e) => {
            if (!validateDates()) {
                e.preventDefault();
                return;
            }
            setLoading(true);
            // Allow form to submit normally
        });

        // Clear filters
        clearBtn?.addEventListener('click', () => {
            startDate.value = '';
            endDate.value = '';
            document.getElementById('format').value = 'xlsx';
            document.getElementById('dapur_id').value = '';
            updateHints();
            validateDates();
            presetBtns.forEach(b => b.classList.remove('active'));
            startDate.focus();
        });

        // Initialize: set active preset if dates already selected
        if (startDate.value && endDate.value) {
            const today = new Date(); today.setHours(0,0,0,0);
            const start = new Date(startDate.value + 'T00:00:00');
            const end = new Date(endDate.value + 'T00:00:00');
            const diffStart = Math.round((start - today) / 86400000);
            const diffEnd = Math.round((end - today) / 86400000);

            let matched = 'custom';
            for (const [key, range] of Object.entries(presets)) {
                if (diffStart === range.start && diffEnd === range.end) {
                    matched = key;
                    break;
                }
            }
            if (matched !== 'custom') {
                updatePresetButtons(matched);
            }
        }
        updateHints();
    })();
</script>
@endpush