<div {{ $attributes->merge(['class' => 'toast fixed bottom-6 right-6 bg-[var(--color-secondary)] text-white px-5 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300']) }}
     x-show="toast.show"
     x-transition:enter="opacity-0 transform translate-y-2"
     x-transition:leave="opacity-0 transform translate-y-2"
     x-cloak>
    <span x-text="toast.message"></span>
</div>
