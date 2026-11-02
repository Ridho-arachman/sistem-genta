@if (session('status'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm" x-show="true" x-transition>{{ session('status') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm" x-show="true" x-transition>{{ session('error') }}</div>
    </div>
@endif
