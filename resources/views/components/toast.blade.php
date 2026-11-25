@php
    $type = session('toast_type', (session('success') || session('status')) ? 'success' : (session('error') || $errors->any() ? 'error' : 'info'));
    $message = session('status', session('success', session('error', $errors->first() ?? '')));
    $icons = [
        'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        'error' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    ];
    $alertClasses = [
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning',
        'error' => 'alert-error',
    ];
    $alertClass = $alertClasses[$type] ?? $alertClasses['info'];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

@if ($message)
<div id="toast" role="status" aria-live="polite" aria-atomic="true" x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="toast toast-end z-[9999]">
    <div class="alert {{ $alertClass }}">
        <span class="flex-shrink-0">{!! $icon !!}</span>
        <span class="text-sm font-medium">{{ $message }}</span>
        <button type="button"
                class="btn btn-ghost btn-xs ml-auto"
                @click="show = false"
                aria-label="Close toast">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>
@endif
