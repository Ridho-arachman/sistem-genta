@extends('admin.layouts.app', ['pageTitle' => 'Tambah CCTV'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Tambah CCTV Baru</h2>
        </div>

        <form method="POST" action="{{ route('admin.cctvs.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="dapur_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Dapur <span class="text-red-500">*</span></label>
                <select id="dapur_id" name="dapur_id" required class="input-field">
                    <option value="">Pilih dapur</option>
                    @foreach ($dapurs as $dapur)
                        <option value="{{ $dapur->id }}">{{ $dapur->nama }}</option>
                    @endforeach
                </select>
                @error('dapur_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nama CCTV <span class="text-red-500">*</span></label>
                <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required class="input-field" placeholder="Contoh: Kamera Depan Gudang">
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="section" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Area / Section</label>
                <input id="section" type="text" name="section" value="{{ old('section') }}" class="input-field" placeholder="Contoh: Gudang Bahan, Area Masak, dll">
            </div>

            <div>
                <label for="camera_number" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Nomor Kamera</label>
                <input id="camera_number" type="number" name="camera_number" value="{{ old('camera_number') }}" min="1" class="input-field" placeholder="Contoh: 1, 2, 3...">
            </div>

            <div>
                <label for="ip_address" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">IP Address</label>
                <input id="ip_address" type="text" name="ip_address" value="{{ old('ip_address') }}" class="input-field" placeholder="Contoh: 192.168.1.100">
            </div>

            <div>
                <label for="rtsp_url" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">RTSP URL</label>
                <input id="rtsp_url" type="text" name="rtsp_url" value="{{ old('rtsp_url') }}" class="input-field" placeholder="rtsp://user:pass@ip:554/stream">
            </div>

            <div>
                <label for="hls_url" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">HLS URL (untuk streaming web)</label>
                <input id="hls_url" type="text" name="hls_url" value="{{ old('hls_url') }}" class="input-field" placeholder="https://domain.com/stream.m3u8">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required class="input-field">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1">Simpan CCTV</button>
                <a href="{{ route('admin.cctvs.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection