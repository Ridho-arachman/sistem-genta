@extends('admin.layouts.app', ['pageTitle' => 'Buat Laporan Anomali CCTV'])

@section('page_content')

<x-admin::page-heading title="Laporan Anomali CCTV" subtitle="Buat laporan anomali baru" />

<x-admin::panel>

        <form method="POST" action="{{ route('admin.cctv-anomalies.store') }}" class="space-y-6">
            @csrf

            <x-admin::form-field label="CCTV" name="cctv_id" required>
                <select id="cctv_id" name="cctv_id" required class="input input-bordered">
                    <option value="">Pilih CCTV</option>
                    @foreach ($cctvs as $cctv)
                        <option value="{{ $cctv->id }}">{{ $cctv->display_name }} ({{ $cctv->dapur->nama }})</option>
                    @endforeach
                </select>
            </x-admin::form-field>

            <x-admin::form-field label="Deskripsi Anomali" name="description" required>
                <textarea id="description" name="description" required rows="4" class="input input-bordered" placeholder="Jelaskan anomali yang terdeteksi..."></textarea>
            </x-admin::form-field>

            <x-admin::form-field label="Tingkat Keparahan" name="severity" required>
                <select id="severity" name="severity" required class="input input-bordered">
                    <option value="">Pilih tingkat</option>
                    <option value="rendah">Rendah - Gangguan minor</option>
                    <option value="sedang" selected>Sedang - Perlu perhatian</option>
                    <option value="tinggi">Tinggi - Kritis, butuh tindakan cepat</option>
                </select>
            </x-admin::form-field>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn btn-primary flex-1">Kirim Laporan</button>
                <a href="{{ route('admin.cctv-anomalies.index') }}" class="btn btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </x-admin::panel>
@endsection