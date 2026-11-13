@extends('admin.layouts.app', ['pageTitle' => 'Edit CCTV: ' . $cctv->nama])

@section('page_content')

    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <div class="text-[var(--color-primary)] text-[11px] font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
            <h1 class="text-[28px] font-extrabold tracking-[-0.5px] mt-1 mb-1">Edit CCTV: {{ $cctv->nama }}</h1>
            <div class="text-[var(--color-text-muted)] text-sm">Perbarui data kamera CCTV</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cctvs.update', $cctv) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card card-bordered">
            <div class="card-body">
                <div class="card-title">
                    <span class="section-icon grid h-6 w-6 place-items-center rounded-lg bg-[var(--color-primary)]/10 text-xs text-[var(--color-primary)]">▣</span>
                    Data Kamera
                </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="dapur_id">Dapur <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <select id="dapur_id" name="dapur_id" required class="input input-bordered">
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}" {{ $cctv->dapur_id == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
                    @endforeach
                </select>
                @error('dapur_id')
                    <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="nama">Nama CCTV <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <input id="nama" type="text" name="nama" value="{{ old('nama', $cctv->nama) }}" required 
                           class="input input-bordered" placeholder="Contoh: Kamera Depan Gudang">
                </div>
                @error('nama')
                    <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-1.5">
                    <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="section">Area / Section</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <input id="section" type="text" name="section" value="{{ old('section', $cctv->section) }}" 
                               class="input input-bordered" placeholder="Contoh: Gudang Bahan, Area Masak, dll">
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="camera_number">Nomor Kamera</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <input id="camera_number" type="number" name="camera_number" value="{{ old('camera_number', $cctv->camera_number) }}" min="1" 
                               class="input input-bordered" placeholder="Contoh: 1, 2, 3...">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="ip_address">IP Address</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m4 0h2a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-4-4h4m0 0l3-3m-3 3l-3-3"></path></svg>
                    <input id="ip_address" type="text" name="ip_address" value="{{ old('ip_address', $cctv->ip_address) }}" 
                           class="input input-bordered" placeholder="Contoh: 192.168.1.100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-1.5">
                    <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="rtsp_url">RTSP URL</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <input id="rtsp_url" type="text" name="rtsp_url" value="{{ old('rtsp_url', $cctv->rtsp_url) }}" 
                               class="input input-bordered" placeholder="rtsp://user:pass@ip:554/stream">
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="hls_url">HLS URL (streaming web)</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <input id="hls_url" type="text" name="hls_url" value="{{ old('hls_url', $cctv->hls_url) }}" 
                               class="input input-bordered" placeholder="https://domain.com/stream.m3u8">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="status">Status <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <select id="status" name="status" required class="input input-bordered">
                    <option value="online" {{ old('status', $cctv->status) === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ old('status', $cctv->status) === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="maintenance" {{ old('status', $cctv->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>
    </div>
</div>

        <div class="flex flex-wrap gap-3 mt-2 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Perbarui CCTV</span>
            </button>
            <a href="{{ route('admin.cctvs.index') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>

@endsection
