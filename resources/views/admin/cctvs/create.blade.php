@extends('admin.layouts.app', ['pageTitle' => 'Tambah CCTV'])

@section('page_content')

    <x-admin::page-heading title="Tambah CCTV Baru" subtitle="Daftarkan kamera CCTV baru ke dapur" />

    <form method="POST" action="{{ route('admin.cctvs.store') }}" class="space-y-6">
        @csrf

        <x-admin::panel title="Data Kamera" icon="camera">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-admin::form-field label="Dapur" name="dapur_id" required>
                    <select id="dapur_id" name="dapur_id" required class="select select-bordered w-full">
                        <option value="">Pilih dapur</option>
                        @foreach ($dapurs as $dapur)
                            <option value="{{ $dapur->id }}" @selected(old('dapur_id') == $dapur->id)>{{ $dapur->nama }}</option>
                        @endforeach
                    </select>
                </x-admin::form-field>

                <x-admin::form-field label="Status" name="status" required>
                    <select id="status" name="status" required class="select select-bordered w-full">
                        <option value="online" @selected(old('status', 'online') === 'online')>Online</option>
                        <option value="offline" @selected(old('status') === 'offline')>Offline</option>
                        <option value="maintenance" @selected(old('status') === 'maintenance')>Maintenance</option>
                    </select>
                </x-admin::form-field>

                <div class="lg:col-span-2">
                    <x-admin::form-field label="Nama CCTV" name="nama" required>
                        <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required
                               class="input input-bordered w-full" placeholder="Contoh: Kamera Depan Gudang">
                    </x-admin::form-field>
                </div>

                <x-admin::form-field label="Area / Section" name="section">
                    <input id="section" type="text" name="section" value="{{ old('section') }}"
                           class="input input-bordered w-full" placeholder="Contoh: Gudang Bahan, Area Masak, dll">
                </x-admin::form-field>

                <x-admin::form-field label="Nomor Kamera" name="camera_number">
                    <input id="camera_number" type="number" name="camera_number" value="{{ old('camera_number') }}" min="1"
                           class="input input-bordered w-full" placeholder="Contoh: 1, 2, 3...">
                </x-admin::form-field>

                <div class="lg:col-span-2">
                    <x-admin::form-field label="RTSP URL" name="rtsp_url">
                        <input id="rtsp_url" type="text" name="rtsp_url" value="{{ old('rtsp_url') }}"
                               class="input input-bordered w-full" placeholder="rtsp://user:pass@ip:554/stream">
                    </x-admin::form-field>
                </div>
            </div>

        </x-admin::panel>

        <div class="flex flex-wrap gap-3 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Simpan CCTV</span>
            </button>
            <a href="{{ route('admin.cctvs.index') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>

@endsection
