@extends('admin.layouts.app', ['pageTitle' => 'Buat Surat Peringatan'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-secondary)]">Buat Surat Peringatan Baru</h2>
            <p class="text-[var(--color-text-muted)] mt-1">SP akan dikirim ke pengawas dan disalin ke manajerial via notifikasi</p>
        </div>

        <form method="POST" action="{{ route('admin.sp-warnings.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Pengawas <span class="text-red-500">*</span></label>
                <select id="user_id" name="user_id" required class="input-field">
                    <option value="">Pilih pengawas</option>
                    @foreach ($pengawas as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="level" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Level SP <span class="text-red-500">*</span></label>
                <select id="level" name="level" required class="input-field">
                    <option value="">Pilih level</option>
                    <option value="1">SP-1 (Peringatan Pertama)</option>
                    <option value="2">SP-2 (Peringatan Kedua)</option>
                    <option value="3">SP-3 (Peringatan Terakhir / PHK)</option>
                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-[var(--color-secondary)] mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea id="reason" name="reason" required rows="4" class="input-field" placeholder="Jelaskan alasan pemberian SP..."></textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-medium text-yellow-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Informasi
                </h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• SP-1: Peringatan pertama (edukatif)</li>
                    <li>• SP-2: Peringatan kedua (tegas)</li>
                    <li>• SP-3: Peringatan terakhir, berpotensi PHK</li>
                    <li>• Notifikasi otomatis terkirim ke pengawas & manajerial</li>
                </ul>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="btn-primary flex-1">Terbitkan SP</button>
                <a href="{{ route('admin.sp-warnings.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection