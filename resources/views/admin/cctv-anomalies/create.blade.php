@extends('admin.layouts.app', ['pageTitle' => 'Buat Laporan Anomali CCTV'])

@section('page_content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
    <div>
        <div class="text-[var(--color-primary)] text-[11px] font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
        <h1 class="text-[28px] font-extrabold tracking-[-0.5px] mt-1 mb-1">Laporan Anomali CCTV</h1>
        <div class="text-[var(--color-text-muted)] text-sm">Buat laporan anomali baru</div>
    </div>
</div>

<div class="card p-8">

        <form method="POST" action="{{ route('admin.cctv-anomalies.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="cctv_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">CCTV <span class="text-red-500">*</span></label>
                <select id="cctv_id" name="cctv_id" required class="input input-bordered">
                    <option value="">Pilih CCTV</option>
                    @foreach ($cctvs as $cctv)
                        <option value="{{ $cctv->id }}">{{ $cctv->display_name }} ({{ $cctv->dapur->nama }})</option>
                    @endforeach
                </select>
                @error('cctv_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Deskripsi Anomali <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" required rows="4" class="input input-bordered" placeholder="Jelaskan anomali yang terdeteksi..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="severity" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Tingkat Keparahan <span class="text-red-500">*</span></label>
                <select id="severity" name="severity" required class="input input-bordered">
                    <option value="">Pilih tingkat</option>
                    <option value="rendah">Rendah - Gangguan minor</option>
                    <option value="sedang" selected>Sedang - Perlu perhatian</option>
                    <option value="tinggi">Tinggi - Kritis, butuh tindakan cepat</option>
                </select>
                @error('severity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn btn-primary flex-1">Kirim Laporan</button>
                <a href="{{ route('admin.cctv-anomalies.index') }}" class="btn btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
@endsection