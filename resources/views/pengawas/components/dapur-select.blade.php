@props(['dapurs'])

<div class="space-y-3">
    <label for="dapur_id" class="block text-sm font-medium text-[var(--color-secondary)] mb-1.5">Pilih Dapur <span class="text-red-500">*</span></label>
    <select id="dapur_id" name="dapur_id" class="input-field" required>
        <option value="">-- Pilih Dapur --</option>
        @foreach ($dapurs as $dapur)
            <option value="{{ $dapur->id }}" {{ old('dapur_id') == $dapur->id ? 'selected' : '' }}>{{ $dapur->nama }}</option>
        @endforeach
    </select>
    @error('dapur_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>