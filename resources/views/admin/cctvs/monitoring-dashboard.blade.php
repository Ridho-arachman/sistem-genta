@extends('admin.layouts.app', ['pageTitle' => 'Monitoring CCTV'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Pengawasan MBG</div>
            <h1>Monitoring CCTV</h1>
            <div class="muted">Status perangkat dan pratinjau feed kamera</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)] font-medium">Total Kamera</p>
                    <p class="text-3xl font-bold text-[var(--color-secondary)] mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)] font-medium">Online</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['online'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)] font-medium">Offline</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['offline'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[var(--color-text-muted)] font-medium">Maintenance</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['maintenance'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="cctvSearch" placeholder="Cari nama kamera, area, atau dapur…"
                           class="w-full pl-10 pr-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                           x-model="searchQuery"
                           @input="filterCctvs()">
                </div>
                <select id="filterStatus" class="px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                        x-model="filter.status"
                        @change="filterCctvs()">
                    <option value="">Semua Status</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <select id="filterDapur" class="px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                        x-model="filter.dapur_id"
                        @change="filterCctvs()">
                    <option value="">Semua Dapur</option>
                    <template x-for="dapur in dapurs" :key="dapur.id">
                        <option :value="dapur.id" x-text="dapur.nama"></option>
                    </template>
                </select>
            </div>
            <span class="text-sm text-[var(--color-text-muted)]" x-text="filteredCctvs.length + ' dari ' + allCctvs.length + ' kamera'"></span>
        </div>
    </div>

    <!-- Camera Grid -->
    <div x-data="cctvMonitoring()" x-init="init(@json($stats), @json($cctvs), @json($dapurs))">
        <template x-if="filteredCctvs.length > 0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" x-ref="cameraGrid">
                <template x-for="cctv in filteredCctvs" :key="cctv.id">
                    <article class="card overflow-hidden cctv-card" :class="{'ring-2 ring-[var(--color-primary)]': selectedId === cctv.id}" @click="selectCctv(cctv.id)">
                        <!-- Camera Feed Placeholder -->
                        <div class="aspect-video bg-[#0a0f0d] relative overflow-hidden">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-white/60" x-show="!cctv.hls_url || cctv.status !== 'online'">
                                <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <p class="text-sm text-center px-4" x-text="cctv.status === 'online' ? 'HLS URL belum diset' : getStatusText(cctv.status)"></p>
                            </div>
                            <video class="w-full h-full" controls crossorigin="anonymous" 
                                   x-show="cctv.hls_url && cctv.status === 'online'"
                                   :data-hls="cctv.hls_url"></video>
                            
                            <!-- Live indicator for online -->
                            <div class="absolute top-3 left-3 flex items-center gap-1.5" x-show="cctv.status === 'online'">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                <span class="text-xs font-medium text-white/90 bg-black/50 px-2 py-0.5 rounded">LIVE</span>
                            </div>
                            
                            <!-- Offline/Maintenance overlay -->
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center" x-show="cctv.status !== 'online'">
                                <span class="text-white/70 text-sm font-medium px-3 py-1.5 rounded-full bg-black/50" x-text="getStatusText(cctv.status)"></span>
                            </div>

                            <!-- Camera tag -->
                            <div class="absolute top-3 right-3 bg-black/70 text-white/90 text-xs font-mono px-2 py-1 rounded" x-text="'CAM-' + String(cctv.camera_number).padStart(2, '0')"></div>
                        </div>

                        <!-- Camera Info -->
                        <div class="p-4 border-t border-[var(--color-border)]">
                            <h3 class="font-semibold text-[var(--color-secondary)] truncate" x-text="cctv.nama"></h3>
                            <div class="flex items-center gap-2 mt-2 text-xs text-[var(--color-text-muted)]">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span x-text="cctv.dapur?.nama"></span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-2 text-xs text-[var(--color-text-muted)]">
                                <span x-text="cctv.section || 'Area tidak diset'"></span>
                                <span class="font-mono" x-text="'Cam ' + cctv.camera_number"></span>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <span class="status-badge" :class="getStatusClass(cctv.status)" x-text="getStatusText(cctv.status)"></span>
                                <a :href="route('admin.cctvs.show', cctv.id)" class="text-xs text-[var(--color-primary)] hover:underline font-medium" @click.stop>Detail</a>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </template>

        <template x-if="filteredCctvs.length === 0">
            <div class="card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Tidak Ada Kamera</h3>
                <p class="text-[var(--color-text-muted)] mt-1">Tidak ada kamera yang cocok dengan filter</p>
            </div>
        </template>

        <!-- Notice -->
        <div class="notice mt-6 p-4 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[var(--color-text-muted)] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-[var(--color-text-muted)]">
                    <p class="font-medium text-[var(--color-secondary)]">Live view sesungguhnya memerlukan integrasi</p>
                    <p class="mt-1">Pratinjau feed hanya tersedia untuk kamera dengan HLS URL yang valid dan status Online. Integrasi penuh memerlukan NVR/IP camera, dukungan stream yang sesuai, akses jaringan aman, dan pengujian perangkat.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cctvMonitoring', () => ({
        searchQuery: '',
        filter: {
            status: '',
            dapur_id: '',
        },
        dapurs: [],
        allCctvs: [],
        filteredCctvs: [],
        selectedId: null,
        
        init(stats, cctvsByDapur, dapurs) {
            this.dapurs = dapurs;
            // Flatten grouped cctvs
            this.allCctvs = Object.values(cctvsByDapur).flat();
            this.filteredCctvs = [...this.allCctvs];
            
            // Initialize HLS players after DOM ready
            this.$nextTick(() => {
                this.initHlsPlayers();
            });
        },
        
        filterCctvs() {
            let result = [...this.allCctvs];
            
            // Search filter
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(c => 
                    c.nama.toLowerCase().includes(q) ||
                    (c.section && c.section.toLowerCase().includes(q)) ||
                    (c.dapur && c.dapur.nama.toLowerCase().includes(q)) ||
                    (c.ip_address && c.ip_address.includes(q))
                );
            }
            
            // Status filter
            if (this.filter.status) {
                result = result.filter(c => c.status === this.filter.status);
            }
            
            // Dapur filter
            if (this.filter.dapur_id) {
                result = result.filter(c => c.dapur_id == this.filter.dapur_id);
            }
            
            this.filteredCctvs = result;
            
            // Re-init HLS players after filter
            this.$nextTick(() => {
                this.initHlsPlayers();
            });
        },
        
        selectCctv(id) {
            this.selectedId = this.selectedId === id ? null : id;
        },
        
        getStatusClass(status) {
            switch(status) {
                case 'online': return 'ok';
                case 'offline': return 'off';
                case 'maintenance': return 'warn';
                default: return 'off';
            }
        },
        
        getStatusText(status) {
            switch(status) {
                case 'online': return 'Online';
                case 'offline': return 'Offline';
                case 'maintenance': return 'Maintenance';
                default: return status;
            }
        },
        
        initHlsPlayers() {
            if (typeof Hls === 'undefined') return;
            
            document.querySelectorAll('video[data-hls]').forEach(video => {
                // Skip if already initialized
                if (video.dataset.hlsInitialized) return;
                
                const src = video.dataset.hls;
                if (Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(src);
                    hls.attachMedia(video);
                    video.dataset.hlsInitialized = 'true';
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = src;
                    video.dataset.hlsInitialized = 'true';
                }
            });
        },
    }));
});
</script>
@endpush

@push('styles')
<style>
.cctv-card {
    transition: all 0.2s ease;
}
.cctv-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.cctv-card:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.status-badge.ok {
    background: #dcfce7;
    color: #166534;
}
.status-badge.warn {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.off {
    background: #fee2e2;
    color: #991b1b;
}
.notice {
    background: var(--color-surface);
    border-color: var(--color-border);
}
</style>
@endpush