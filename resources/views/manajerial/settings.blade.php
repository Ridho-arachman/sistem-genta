@extends('admin.layouts.app', ['pageTitle' => 'Pengaturan Manajerial'])

@section('page_content')
<x-admin::page-heading title="Pengaturan" subtitle="Akun manajerial bersifat read-only" />

<div class="max-w-xl">
    <x-admin::panel title="Akun Anda">
        <dl class="text-sm space-y-3">
            <div class="flex justify-between gap-4 border-b border-[var(--color-border)] pb-2">
                <dt class="text-[var(--color-text-muted)]">Nama</dt>
                <dd class="font-semibold">{{ auth()->user()->name }}</dd>
            </div>
            <div class="flex justify-between gap-4 border-b border-[var(--color-border)] pb-2">
                <dt class="text-[var(--color-text-muted)]">Email</dt>
                <dd class="font-semibold">{{ auth()->user()->email }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-[var(--color-text-muted)]">Peran</dt>
                <dd class="font-semibold">Manajerial (pimpinan, read-only)</dd>
            </div>
        </dl>
        <p class="text-xs text-[var(--color-text-muted)] mt-4">Perubahan nama/email/password hanya bisa dilakukan oleh admin. Hubungi admin jika perlu.</p>
    </x-admin::panel>
</div>
@endsection
