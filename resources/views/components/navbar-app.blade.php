@php
    $profileRoute = $profileRoute ?? 'pengawas.settings';
    $topbarClass = $topbarClass ?? 'h-18 bg-[var(--color-primary)] text-white flex items-center justify-between px-4 sm:px-6 lg:px-8 gap-4';
    $brandLogo = $brandLogo ?? 'icons/icon-512.png';
    $brandAlt = $brandAlt ?? 'Genta Logo';
    $brandText = $brandText ?? 'GENTA';
    $brandSubText = $brandSubText ?? 'PENGAWASAN DAPUR MBG';
@endphp

<header class="{{ $topbarClass }}">
    <div class="brand flex items-center gap-3 font-extrabold tracking-tight">
        <img src="{{ asset($brandLogo) }}" alt="{{ $brandAlt }}" class="w-10 h-10 rounded-xl object-contain">
        <div>{{ $brandText }} <small class="block font-medium text-xs opacity-75 tracking-wide mt-0.5">{{ $brandSubText }}</small></div>
    </div>
    <div class="top-actions flex items-center gap-3">
        <div id="profileMenu" class="relative">
            <button id="profileBtn" class="flex items-center gap-2" aria-expanded="false" aria-label="Profile menu">
                @php
                    $_userName = auth()->user()->name ?? 'Pengawas';
                    $_nameParts = array_filter(explode(' ', trim($_userName)));
                    $_initials = strtoupper($_nameParts[0][0] ?? 'P');
                    if (count($_nameParts) > 1) {
                        $_initials .= strtoupper($_nameParts[1][0] ?? '');
                    }
                @endphp
                <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-white/30 rounded-full px-3 py-1">{{ auth()->user()->name }}</span>
                <div class="avatar w-9 h-9 rounded-full bg-red-100 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm">{{ $_initials }}</div>
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