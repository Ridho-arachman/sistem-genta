@extends('admin.layouts.app', ['pageTitle' => 'Cetak Laporan SP'])

@section('page_content')
<div class="max-w-4xl mx-auto p-6 print:p-0 print:text-[11px]">
    {{-- Print Header --}}
    <div class="text-center mb-8 pb-6 border-b-2 border-[var(--color-border)]">
        <div class="flex items-center justify-center gap-3 mb-4">
            <div class="w-16 h-16 rounded-full bg-[var(--color-primary)] flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-[var(--color-secondary)]">{{ config('app.name') }}</h1>
                <p class="text-[var(--color-text-muted)]">Sistem Manajemen MBG GENTA</p>
            </div>
        </div>
        <h2 class="text-xl font-semibold text-[var(--color-secondary)] mt-4">Laporan Surat Peringatan (SP)</h2>
        <div class="flex justify-center gap-8 mt-2 text-sm text-[var(--color-text-muted)]">
            <span>Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 text-center print:border print:border-[var(--color-border)] print:shadow-none print:break-inside-avoid">
            <p class="text-3xl font-bold text-[var(--color-primary)]">{{ $warnings->count() }}</p>
            <p class="text-sm text-[var(--color-text-muted)]">Total SP</p>
        </div>
        <div class="card p-4 text-center print:border print:border-[var(--color-border)] print:shadow-none print:break-inside-avoid">
            <p class="text-3xl font-bold text-yellow-600">{{ $warnings->where('level', 1)->count() }}</p>
            <p class="text-sm text-[var(--color-text-muted)]">SP-1</p>
        </div>
        <div class="card p-4 text-center print:border print:border-[var(--color-border)] print:shadow-none print:break-inside-avoid">
            <p class="text-3xl font-bold text-orange-600">{{ $warnings->where('level', 2)->count() }}</p>
            <p class="text-sm text-[var(--color-text-muted)]">SP-2</p>
        </div>
        <div class="card p-4 text-center print:border print:border-[var(--color-border)] print:shadow-none print:break-inside-avoid">
            <p class="text-3xl font-bold text-red-600">{{ $warnings->where('level', 3)->count() }}</p>
            <p class="text-sm text-[var(--color-text-muted)]">SP-3</p>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card overflow-hidden print:border print:border-[var(--color-border)] print:shadow-none">
        @if ($warnings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[var(--color-surface)] border-b-2 border-[var(--color-border)]">
                        <tr>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">No</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Tanggal</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Pengawas</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Level</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Alasan</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Status</th>
                            <th class="p-3 text-left font-semibold text-[var(--color-secondary)]">Diterbitkan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($warnings as $index => $warning)
                        <tr class="hover:bg-[var(--color-surface)]">
                            <td class="p-3 text-[var(--color-text-muted)]">{{ $index + 1 }}</td>
                            <td class="p-3 text-[var(--color-secondary)]">{{ $warning->issued_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-medium text-[var(--color-secondary)]">{{ $warning->user->name }}</td>
                            <td class="p-3">
                                <span class="inline-block px-2 py-1 text-xs font-bold rounded-full
                                    @if($warning->level == 1) bg-yellow-100 text-yellow-800
                                    @elseif($warning->level == 2) bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800 @endif">
                                    SP-{{ $warning->level }}
                                </span>
                            </td>
                            <td class="p-3 text-[var(--color-secondary)] max-w-xs">{{ $warning->reason }}</td>
                            <td class="p-3">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                    @if($warning->status === 'aktif') bg-red-100 text-red-800
                                    @elseif($warning->status === 'dicabut') bg-gray-100 text-gray-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($warning->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-[var(--color-secondary)]">{{ $warning->admin->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="mt-4 text-lg font-medium text-[var(--color-secondary)]">Tidak Ada Data SP</h3>
                <p class="text-[var(--color-text-muted)] mt-1">Tidak ada surat peringatan pada periode yang dipilih</p>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="mt-8 pt-6 border-t border-[var(--color-border)] flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-[var(--color-text-muted)]">
        <p>Dicetak secara otomatis oleh sistem GENTA MBG</p>
        <p>Halaman {{ $page ?? 1 }}</p>
    </div>
</div>
@endsection