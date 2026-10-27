@extends('admin.layouts.app', ['pageTitle' => 'Tambah CCTV'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="heading mb-8">
        <div>
            <div class="eyebrow">Genta · Admin Panel</div>
            <h1>Tambah CCTV Baru</h1>
            <div class="muted">Daftarkan kamera CCTV baru ke dapur</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cctvs.store') }}" class="space-y-6">
        @csrf

        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon">▣</span>
                Data Kamera
            </div>

            <div class="form-field">
                <label class="form-label" for="dapur_id">Dapur <span class="required">*</span></label>
                <select id="dapur_id" name="dapur_id" required class="input-field select-enhanced">
                    <option value="">Pilih dapur</option>
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}">{{ $dapur->nama }}</option>
                    @endforeach
                </select>
                @error('dapur_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="nama">Nama CCTV <span class="required">*</span></label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required 
                           class="input-field select-enhanced" placeholder="Contoh: Kamera Depan Gudang">
                </div>
                @error('nama')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-field">
                    <label class="form-label" for="section">Area / Section</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <input id="section" type="text" name="section" value="{{ old('section') }}" 
                               class="input-field select-enhanced" placeholder="Contoh: Gudang Bahan, Area Masak, dll">
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label" for="camera_number">Nomor Kamera</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <input id="camera_number" type="number" name="camera_number" value="{{ old('camera_number') }}" min="1" 
                               class="input-field select-enhanced" placeholder="Contoh: 1, 2, 3...">
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="ip_address">IP Address</label>
                <div class="input-with-icon">
                    <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m4 0h2a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-4-4h4m0 0l3-3m-3 3l-3-3"></path></svg>
                    <input id="ip_address" type="text" name="ip_address" value="{{ old('ip_address') }}" 
                           class="input-field select-enhanced" placeholder="Contoh: 192.168.1.100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-field">
                    <label class="form-label" for="rtsp_url">RTSP URL</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <input id="rtsp_url" type="text" name="rtsp_url" value="{{ old('rtsp_url') }}" 
                               class="input-field select-enhanced" placeholder="rtsp://user:pass@ip:554/stream">
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label" for="hls_url">HLS URL (streaming web)</label>
                    <div class="input-with-icon">
                        <svg class="input-icon-inner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <input id="hls_url" type="text" name="hls_url" value="{{ old('hls_url') }}" 
                               class="input-field select-enhanced" placeholder="https://domain.com/stream.m3u8">
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label class="form-label" for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" required class="input-field select-enhanced">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-action btn-action-primary">
                <svg class="btn-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan CCTV</span>
            </button>
            <a href="{{ route('admin.cctvs.index') }}" class="btn-action btn-action-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>
@endsection