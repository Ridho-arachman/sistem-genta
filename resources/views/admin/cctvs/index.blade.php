@extends('admin.layouts.app', ['pageTitle' => 'Monitoring CCTV'])

@section('page_content')
<div class="heading">
    <div>
        <div class="eyebrow">Genta · Monitoring CCTV</div>
        <h1>Monitoring CCTV</h1>
        <div class="muted">Status perangkat dan pratinjau feed kamera</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.cctvs.create') }}" class="btn-action btn-action-primary">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah CCTV</span>
        </a>
        <a href="{{ route('admin.cctv-anomalies.index') }}" class="btn-action btn-action-primary">
            <span>!</span>
            <span>Laporan Anomali</span>
        </a>
    </div>
</div>

<div class="panel">
    <div class="panel-head" style="flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <h2 class="text-lg font-semibold text-[var(--color-secondary)]">Daftar Monitor Kamera</h2>
        <span class="text-sm text-[var(--color-text-muted)]">{{ $cctvs->total() }} kamera terdaftar</span>
    </div>

    <!-- Filter Lokasi -->
    <form method="GET" action="{{ route('admin.cctvs.index') }}" class="px-4 py-3 border-b border-[var(--color-border)] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="form-field">
            <label class="form-label text-xs font-semibold text-[var(--color-text-muted)] uppercase">Provinsi</label>
            <select name="provinsi" id="provinsiFilter" class="input-field select-enhanced text-sm w-full" disabled>
                <option value="Banten" {{ request('provinsi') === 'Banten' ? 'selected' : '' }}>Banten</option>
            </select>
        </div>
        <div class="form-field">
            <label class="form-label text-xs font-semibold text-[var(--color-text-muted)] uppercase">Kabupaten / Kota</label>
            <select name="kabupaten_kota" id="kabupatenKotaFilter" class="input-field select-enhanced text-sm w-full" onchange="this.form.submit()">
                <option value="">Semua Kabupaten/Kota</option>
                @foreach ($kabupatenKotas as $value => $label)
                    <option value="{{ $value }}" {{ request('kabupaten_kota') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="form-label text-xs font-semibold text-[var(--color-text-muted)] uppercase">Kecamatan</label>
            <select name="kecamatan" id="kecamatanFilter" class="input-field select-enhanced text-sm w-full" onchange="this.form.submit()">
                <option value="">Semua Kecamatan</option>
                @foreach ($kecamatans as $value => $label)
                    <option value="{{ $value }}" {{ request('kecamatan') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="form-label text-xs font-semibold text-[var(--color-text-muted)] uppercase">Dapur</label>
            <select name="dapur_id" id="dapurFilter" class="input-field select-enhanced text-sm w-full" onchange="this.form.submit()">
                <option value="">Semua Dapur</option>
                @foreach ($filteredDapurs as $dapur)
                    <option value="{{ $dapur->id }}" {{ request('dapur_id') === $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if ($cctvs->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 min-w-0">
            @foreach ($cctvs as $cctv)
            <div class="card overflow-hidden min-w-0 w-full min-h-[400px]">
                <div class="p-3 flex items-center justify-between">
                    <span class="cam-tag bg-black/70 text-white text-xs font-mono px-2 py-1 rounded shrink-0" style="font-family: monospace;">
                        CAM-{{ str_pad($cctv->camera_number ?? 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-tightest whitespace-nowrap
                        @if($cctv->status === 'online') bg-green-50 text-green-700
                        @elseif($cctv->status === 'offline') bg-red-50 text-red-700
                        @else bg-yellow-50 text-yellow-700 @endif">
                        {{ ucfirst($cctv->status) }}
                    </span>
                </div>
                <div class="aspect-video bg-black relative pb-2">
                    @if ($cctv->hls_url && $cctv->status === 'online' && str_starts_with($cctv->hls_url, 'http://localhost'))
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <video class="absolute inset-0 w-full h-full object-cover" controls crossorigin="anonymous" data-hls="{{ $cctv->hls_url }}" data-cctv-id="{{ $cctv->id }}" preload="none" muted></video>
                            <div class="absolute inset-0 z-10 hidden flex flex-col items-center justify-center bg-black/80" id="video-error-{{ $cctv->id }}">
                                <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <p class="text-xs text-white text-center">Feed tidak tersedia</p>
                                <p class="text-[10px] text-white/40">Gagal memuat stream</p>
                            </div>
                        </div>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
                            <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <p class="text-sm">Feed belum terhubung</p>
                            <p class="text-[10px] text-white/40">Contoh tampilan saja</p>
                        </div>
                    @endif
                </div>
                <div class="p-3 bg-[var(--color-surface)]">
                    <div class="font-semibold text-[var(--color-secondary)]">{{ $cctv->nama }}</div>
                    <div class="text-xs text-[var(--color-text-muted)]">{{ $cctv->dapur->nama }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] mt-1">{{ $cctv->section ?? '-' }}</div>
                </div>
                <div class="p-3 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.cctvs.show', $cctv) }}" class="text-[var(--color-primary)] hover:underline text-sm font-medium">Lihat detail</a>
                    <a href="{{ route('admin.cctv-anomalies.create') }}?cctv_id={{ $cctv->id }}" class="flex items-center gap-1 text-red-600 hover:underline text-sm font-medium ml-1">
                        <span>!</span>
                        <span>Laporkan anomali</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-[var(--color-surface)] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            <h3 class="text-lg font-semibold text-[var(--color-secondary)] mb-1">Belum Ada CCTV</h3>
            <p class="text-[var(--color-text-muted)] mb-4">Mulai dengan menambahkan CCTV pertama</p>
            <a href="{{ route('admin.cctvs.create') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah CCTV
            </a>
        </div>
    @endif

    <div class="px-4 py-4 border-t border-[var(--color-border)]">
        {{ $cctvs->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('video[data-hls]').forEach(function(video) {
            var src = video.dataset.hls;
            var cctvId = video.dataset.cctvId;
            var errorDiv = document.getElementById('video-error-' + cctvId);
            
            function showError() {
                video.classList.add('hidden');
                if (errorDiv) errorDiv.classList.remove('hidden');
            }

            if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(src);
                hls.attachMedia(video);
                hls.on(Hls.Events.ERROR, function (event, data) {
                    if (data.fatal) {
                        switch(data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                hls.recoverMediaError();
                                break;
                            default:
                                hls.destroy();
                                showError();
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = src;
                video.onerror = function() {
                    showError();
                };
            } else {
                showError();
            }
        });
    });
</script>
@endpush