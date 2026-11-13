@php
    $profileRoute = $profileRoute ?? 'admin.settings.profile';
    $showHamburger = $showHamburger ?? true;
    $showModeBadge = $showModeBadge ?? false;
    $topbarClass = $topbarClass ?? '';
    $brandLogo = $brandLogo ?? 'logo.png';
    $brandAlt = $brandAlt ?? 'GENTA Pangan Banten';
    $brandText = $brandText ?? 'GENTA';
    $brandSubText = $brandSubText ?? 'ADMIN PANEL';
@endphp

<header class="bg-[var(--color-primary)] text-white h-[72px] fixed top-0 left-0 right-0 z-1000 flex items-center justify-between px-4 sm:px-6 gap-4 shadow-[0_2px_8px_rgb(0_0_0_/_0.08)] {{ $topbarClass }}">
    <div class="flex items-center gap-3 px-2">
        @if($showHamburger)
        <button 
            class="w-10 h-10 rounded-[10px] bg-white/15 text-white border-none cursor-pointer transition-colors hover:bg-white/20 flex items-center justify-center lg:hidden" 
            @click.stop="$store.ui.toggle()" 
            :aria-expanded="$store.ui.sidebarOpen"
            aria-controls="sidebar"
            aria-label="Toggle menu">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    :d="$store.ui.sidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
            </svg>
        </button>
        @endif
        <div class="w-10 h-10 rounded-[13px] bg-white p-2 overflow-hidden">
            <img src="{{ asset($brandLogo) }}" alt="{{ $brandAlt }}" class="h-full w-full object-contain">
        </div>
        <div class="font-extrabold tracking-tight tracking-[-0.3px]">{{ $brandText }} <small class="text-[11px] font-semibold opacity-75 tracking-wide block mt-1">{{ $brandSubText }}</small></div>
    </div>
    <div class="flex items-center gap-3">
        @if($showModeBadge)
        <span class="hidden sm:inline-block text-xs font-extrabold tracking-wide border border-white/30 rounded-full px-3 py-1">MODE PRODUKSI</span>
        @endif
        <div id="profileMenu" class="relative" x-data="{ open: false }">
            <button id="profileBtn" class="flex items-center gap-2 rounded-[10px] px-1.5 py-1 transition-colors hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50" @click="open = !open" :aria-expanded="open" aria-label="Profile menu">
                <span class="hidden max-w-[160px] truncate sm:inline-block">{{ auth()->user()->name }}</span>
                @php
                    $_userName = auth()->user()->name ?? 'Admin';
                    $_nameParts = array_filter(explode(' ', trim($_userName)));
                    $_initials = strtoupper($_nameParts[0][0] ?? 'A');
                    if (count($_nameParts) > 1) {
                        $_initials .= strtoupper($_nameParts[1][0] ?? '');
                    }
                @endphp
                <div class="w-9 h-9 rounded-full bg-red-100 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm">{{ $_initials }}</div>
            </button>
            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1" x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-y-95" x-transition:enter-end="transform opacity-100 scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-y-100" x-transition:leave-end="transform opacity-0 scale-y-95" @click.outside="open = false">
                <a href="{{ route($profileRoute) }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>