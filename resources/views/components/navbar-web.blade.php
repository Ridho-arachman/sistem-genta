@php
    $profileRoute = $profileRoute ?? 'admin.settings.profile';
    $showHamburger = $showHamburger ?? true;
    $showModeBadge = $showModeBadge ?? false;
    $topbarClass = $topbarClass ?? '';
    $brandLogo = $brandLogo ?? 'logo.png';
    $brandAlt = $brandAlt ?? 'GENTA Pangan Banten';
    $brandText = $brandText ?? 'GENTA';
    $brandSubText = $brandSubText ?? 'ADMIN PANEL';
    $topbarBg = $topbarBg ?? 'topbar';
@endphp

<header class="{{ $topbarBg }} {{ $topbarClass }}">
    <div class="brand">
        @if($showHamburger)
        <button 
            class="hamburger lg:hidden" 
            @click="sidebarOpen = !sidebarOpen" 
            :aria-expanded="sidebarOpen"
            aria-controls="sidebar"
            aria-label="Toggle menu">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    :d="sidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
            </svg>
        </button>
        @endif
        <div class="brandmark">
            <img src="{{ asset($brandLogo) }}" alt="{{ $brandAlt }}">
        </div>
        <div>{{ $brandText }} <small>{{ $brandSubText }}</small></div>
    </div>
    <div class="top-actions">
        @if($showModeBadge)
        <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-white/30 rounded-full px-3 py-1">MODE PRODUKSI</span>
        @endif
        <div id="profileMenu" class="relative">
            <button id="profileBtn" class="flex items-center gap-2" aria-expanded="false" aria-label="Profile menu">
                <span class="demo-pill hidden sm:inline-block">{{ auth()->user()->name }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </button>
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
                <a href="{{ route($profileRoute) }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('profileBtn');
            const dropdown = document.getElementById('profileDropdown');
            if (btn && dropdown) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', !dropdown.classList.contains('hidden'));
                });
                document.addEventListener('click', function(e) {
                    if (!dropdown.classList.contains('hidden') && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    </script>
</header>