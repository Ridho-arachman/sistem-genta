@extends('admin.layouts.app', ['pageTitle' => 'Surat Peringatan (SP)'])

@section('page_content')
<div class="space-y-6">
    {{-- Header --}}
    <x-admin::page-heading eyebrow="" title="Surat Peringatan (SP)" subtitle="Kelola SP 1/2/3 untuk pengawas" title-class="text-2xl font-bold text-[var(--color-secondary)]">
        <x-slot:actions>
            <a href="{{ route('admin.sp-warnings.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-success">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Excel
            </a>
            <a href="{{ route('admin.sp-warnings.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                CSV
            </a>
            <a href="{{ route('admin.sp-warnings.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat SP Baru
            </a>
        </x-slot:actions>
    </x-admin::page-heading>

    {{-- Filters --}}
    <div class="card p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="md:w-64">
                <select name="user_id" class="input input-bordered">
                    <option value="">Semua Pengawas</option>
                    @foreach ($pengawas as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-32">
                <select name="level" class="input input-bordered">
                    <option value="">Semua Level</option>
                    <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>SP-1</option>
                    <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>SP-2</option>
                    <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>SP-3</option>
                </select>
            </div>
            <div class="md:w-32">
                <select name="status" class="input input-bordered">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="dicabut" {{ request('status') === 'dicabut' ? 'selected' : '' }}>Dicabut</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="md:w-48">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="input input-bordered" placeholder="Tanggal Mulai">
            </div>
            <div class="md:w-48">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="input input-bordered" placeholder="Tanggal Akhir">
            </div>
            <button type="submit" class="btn btn-secondary whitespace-nowrap">Filter</button>
            <a href="{{ route('admin.sp-warnings.index') }}" class="btn btn-secondary whitespace-nowrap">Reset</a>
        </form>
    </div>

    {{-- SP Warnings Table --}}
    <x-admin::panel class="overflow-hidden" flush>
        @if ($warnings->count() > 0)
            <div class="overflow-x-auto">
                <x-admin::table>
                    <x-slot:head>
                        <th>Tanggal</th>
                        <th>Pengawas</th>
                        <th>Level</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Diterbitkan Oleh</th>
                        <th>Aksi</th>
                    </x-slot:head>
                        @foreach ($warnings as $warning)
                        <tr>
                            <td>{{ $warning->issued_at->format('d/m/Y H:i') }}</td>
                            <td class="font-medium">{{ $warning->user->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($warning->level == 1) badge-warning
                                    @elseif($warning->level == 2) badge-error
                                    @else bg-red-500/20 text-red-800 font-bold @endif">
                                    SP-{{ $warning->level }}
                                </span>
                            </td>
                            <td class="max-w-xs truncate">{{ $warning->reason }}</td>
                            <td>
                                <span class="badge 
                                    @if($warning->status === 'aktif') badge-error
                                    @elseif($warning->status === 'dicabut') badge-secondary
                                    @else badge-success @endif">
                                    {{ ucfirst($warning->status) }}
                                </span>
                            </td>
                            <td>{{ $warning->admin->name }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.sp-warnings.show', $warning) }}" class="text-[var(--color-primary)] hover:underline text-sm">Detail</a>
                                    @if ($warning->status === 'aktif')
                                    <form action="{{ route('admin.sp-warnings.update', $warning) }}" method="POST" class="inline" x-data="{ confirmStatus: (msg) => window.confirm(msg) }">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" @change="confirmStatus('Yakin ubah status?') && $event.target.form.submit()" class="input input-bordered text-xs py-1">
                                            <option value="aktif" {{ $warning->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="dicabut" {{ $warning->status === 'dicabut' ? 'selected' : '' }}>Cabut</option>
                                            <option value="selesai" {{ $warning->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
            </x-admin::table>
            </div>
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $warnings->links() }}
            </div>
        @else
            <x-admin::empty-state title="Belum Ada Surat Peringatan" message="Belum ada SP yang diterbitkan">
                <x-slot:icon>
                    <svg class="w-16 h-16 mx-auto text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </x-slot:icon>
                <x-slot:action>
                    <a href="{{ route('admin.sp-warnings.create') }}" class="btn btn-primary inline-block">Buat SP Pertama</a>
                </x-slot:action>
            </x-admin::empty-state>
        @endif
    </x-admin::panel>
</div>
@endsection