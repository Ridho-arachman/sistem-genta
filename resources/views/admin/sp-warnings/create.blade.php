@extends('admin.layouts.app', ['pageTitle' => 'Buat Surat Peringatan'])

@section('page_content')
<div class="max-w-2xl mx-auto">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <div class="text-[var(--color-primary)] text-[11px] font-extrabold uppercase tracking-[1.4px]">Genta · Admin Panel</div>
            <h1 class="text-[28px] font-extrabold tracking-[-0.5px] mt-1 mb-1">Buat Surat Peringatan Baru</h1>
            <div class="text-[var(--color-text-muted)] text-sm">SP akan dikirim ke pengawas dan disalin ke manajerial via notifikasi</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.sp-warnings.store') }}" class="space-y-6">
        @csrf

        <div class="card card-bordered">
            <div class="card-body">
                <div class="card-title">
                    <span class="section-icon grid h-6 w-6 place-items-center rounded-lg bg-[var(--color-primary)]/10 text-xs text-[var(--color-primary)]">⚠</span>
                    Data Surat Peringatan
                </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="user_id">Pengawas <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <select id="user_id" name="user_id" required class="input input-bordered">
                    <option value="">Pilih pengawas</option>
                    @foreach ($pengawas as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="level">Level SP <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <select id="level" name="level" required class="input input-bordered">
                    <option value="">Pilih level</option>
                    <option value="1">SP-1 (Peringatan Pertama)</option>
                    <option value="2">SP-2 (Peringatan Kedua)</option>
                    <option value="3">SP-3 (Peringatan Terakhir / PHK)</option>
                </select>
                @error('level')
                    <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" for="reason">Alasan <span class="text-xs text-[var(--color-primary)]">*</span></label>
                <textarea id="reason" name="reason" required rows="4" class="input input-bordered" placeholder="Jelaskan alasan pemberian SP..."></textarea>
                @error('reason')
                    <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <h4 class="font-medium text-yellow-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Informasi Level SP
                </h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• SP-1: Peringatan pertama (edukatif)</li>
                    <li>• SP-2: Peringatan kedua (tegas)</li>
                    <li>• SP-3: Peringatan terakhir, berpotensi PHK</li>
                    <li>• Notifikasi otomatis terkirim ke pengawas & manajerial</li>
                </ul>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-2 border-t border-[var(--color-border)] pt-4">
            <button type="submit" class="btn btn-primary">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Terbitkan SP</span>
            </button>
            <a href="{{ route('admin.sp-warnings.index') }}" class="btn btn-secondary">
                <span>Batal</span>
            </a>
        </div>
    </form>
</div>
@endsection