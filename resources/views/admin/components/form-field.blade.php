@props([
    'label',
    'name' => null,
    'required' => false,
    'hint' => null,
])

{{--
    Satu baris form: label (+ tanda wajib), kontrol, hint, dan pesan error @error otomatis.

    <x-admin-form-field label="Nama Dapur" name="nama" required hint="Contoh: Dapur Pusat">
        <input id="nama" name="nama" class="input input-bordered w-full" value="{{ old('nama') }}" required>
    </x-admin-form-field>
--}}
<div class="flex flex-col gap-1.5">
    <label class="flex items-center gap-1.5 text-[13px] font-semibold text-[var(--color-secondary)]" @if ($name) for="{{ $name }}" @endif>
        {{ $label }}@if ($required) <span class="text-xs text-[var(--color-primary)]">*</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="text-[11px] text-[var(--color-text-muted)] mt-1">{{ $hint }}</p>
    @endif
    @if ($name)
        @error($name)
            <p class="flex items-center gap-1 text-xs text-[#EF4444] mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>