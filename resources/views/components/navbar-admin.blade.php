<header class="topbar">
    <div class="brand">
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
        <div class="brandmark">
            <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_desktop') ?? 'GENTA Pangan Banten' }}">
        </div>
        <div>{{ strtoupper(settings('app_name_desktop') ?? 'GENTA') }} <small>ADMIN PANEL</small></div>
    </div>
    <div class="top-actions">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2" aria-expanded="open" aria-label="Profile menu">
                <span class="demo-pill hidden sm:inline-block">{{ auth()->user()->name }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-75" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
                <a href="{{ route('admin.settings.profile') }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>