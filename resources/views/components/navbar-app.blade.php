@props([
    'profileRoute' => 'pengawas.settings',
    'topbarClass' => 'h-18 bg-[var(--color-primary)] text-white flex items-center justify-between px-4 sm:px-6 lg:px-8 gap-4',
    'brandLogo' => 'icons/icon-512.png',
    'brandAlt' => 'Genta Logo',
    'brandText' => 'GENTA',
    'brandSubText' => 'PENGAWASAN DAPUR MBG',
])

<header class="navbar {{ $topbarClass }}">
    <div class="navbar-start gap-3 font-extrabold tracking-[-0.3px]">
        <img src="{{ asset($brandLogo) }}" alt="{{ $brandAlt }}" class="w-10 h-10 rounded-xl object-contain">
        <div>{{ $brandText }} <small class="block text-[11px] font-medium tracking-wide opacity-75 mt-0.5">{{ $brandSubText }}</small></div>
    </div>
    <div class="navbar-end gap-3">
        <div id="profileMenu" class="dropdown dropdown-end" :class="{ 'dropdown-open': open }" x-data="{ open: false }">
            <button id="profileBtn" class="btn btn-ghost gap-2 px-1.5 py-1 text-current hover:bg-transparent hover:border-transparent" @click="open = !open" :aria-expanded="open" aria-label="Profile menu">
                @php
                    $_userName = auth()->user()->name ?? 'Pengawas';
                    $_nameParts = array_filter(explode(' ', trim($_userName)));
                    $_initials = strtoupper($_nameParts[0][0] ?? 'P');
                    if (count($_nameParts) > 1) {
                        $_initials .= strtoupper($_nameParts[1][0] ?? '');
                    }
                @endphp
                <span class="badge badge-outline hidden text-[11px] font-extrabold tracking-[0.6px] sm:inline-flex">{{ auth()->user()->name }}</span>
                <div class="w-9 h-9 rounded-full bg-red-100 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm">{{ $_initials }}</div>
            </button>
            <div id="profileDropdown" class="dropdown-content z-[1002] mt-2 w-48 rounded-box border border-[var(--color-border)] bg-base-100 py-1 shadow-lg" x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-y-95" x-transition:enter-end="transform opacity-100 scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-y-100" x-transition:leave-end="transform opacity-0 scale-y-95" @click.outside="open = false">
                <a href="{{ route($profileRoute) }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>