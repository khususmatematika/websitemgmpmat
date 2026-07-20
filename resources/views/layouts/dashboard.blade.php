@extends('layouts.app')

@section('content')
<div class="flex min-h-[calc(100vh-64px)] items-start relative">

    {{-- Overlay untuk mobile saat sidebar terbuka --}}
    <div id="dashboard-backdrop" class="hidden fixed inset-0 bg-black/40 z-30 md:hidden" onclick="closeDashboardSidebar()"></div>

    <aside id="sidebar"
           class="fixed md:sticky top-[64px] md:top-[64px] left-0 h-[calc(100vh-64px)] w-72 md:w-64 border-r border-outline-variant bg-surface-container-low p-4 space-y-2 transition-transform md:transition-all duration-300 z-40 flex flex-col
                  -translate-x-full md:translate-x-0">

        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-3 min-w-0 sidebar-full">
                <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary">calculate</span>
                </div>
                <div class="min-w-0 nav-label">
                    <p class="font-headline text-navy-deep text-sm font-bold truncate">{{ $panelTitle ?? 'Panel' }}</p>
                    <p class="text-on-surface-variant text-xs truncate">{{ auth()->guard($guard)->user()->name }}</p>
                </div>
            </div>
            <button id="sidebar-toggle" type="button" class="hidden md:block text-on-surface-variant hover:text-math-teal shrink-0" title="Sembunyikan/tampilkan menu">
                <span class="material-symbols-outlined" id="sidebar-toggle-icon">chevron_left</span>
            </button>
            <button type="button" onclick="closeDashboardSidebar()" class="md:hidden text-on-surface-variant shrink-0">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden">
            @foreach ($navItems as $item)
                <a href="{{ $item['url'] }}" title="{{ $item['label'] }}"
                   class="flex items-center gap-3 p-3 rounded-lg font-medium transition-all relative group
                          {{ request()->routeIs($item['active']) ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                    <span class="material-symbols-outlined shrink-0">{{ $item['icon'] }}</span>
                    <span class="font-label text-xs nav-label whitespace-nowrap">{{ $item['label'] }}</span>
                    <span class="sidebar-tooltip hidden absolute left-full ml-2 whitespace-nowrap bg-navy-deep text-white text-xs px-2 py-1 rounded-md z-50 group-hover:block">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="pt-4 mt-auto border-t border-outline-variant">
            <form method="POST" action="{{ route($guard.'.logout') }}">
                @csrf
                <button title="Keluar" class="w-full flex items-center gap-3 p-3 text-status-error hover:bg-error-container/30 rounded-lg relative group">
                    <span class="material-symbols-outlined shrink-0">logout</span>
                    <span class="font-label text-sm nav-label">Keluar</span>
                    <span class="sidebar-tooltip hidden absolute left-full ml-2 whitespace-nowrap bg-navy-deep text-white text-xs px-2 py-1 rounded-md z-50 group-hover:block">
                        Keluar
                    </span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-margin-desktop bg-surface-bg pb-12 min-w-0">
        {{-- Tombol buka sidebar khusus mobile --}}
        <button type="button" onclick="openDashboardSidebar()"
                class="md:hidden flex items-center gap-2 mb-4 bg-white border border-outline-variant px-3 py-2 rounded-md text-sm font-bold text-navy-deep shadow-sm">
            <span class="material-symbols-outlined text-[20px]">menu</span>
            Menu
        </button>

        <div class="max-w-container-max mx-auto space-y-6 md:space-y-8">
            @yield('dashboard-content')
        </div>
    </main>
</div>

<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');

    // --- Desktop collapse (icon-only) ---
    function applyDesktopState(collapsed) {
        if (window.innerWidth < 768) return;
        if (collapsed) {
            sidebar.classList.remove('md:w-64');
            sidebar.classList.add('md:w-20');
            sidebar.querySelectorAll('.nav-label').forEach(el => el.classList.add('hidden'));
            sidebar.querySelectorAll('.sidebar-tooltip').forEach(el => el.classList.remove('hidden'));
            toggleIcon.textContent = 'chevron_right';
        } else {
            sidebar.classList.remove('md:w-20');
            sidebar.classList.add('md:w-64');
            sidebar.querySelectorAll('.nav-label').forEach(el => el.classList.remove('hidden'));
            sidebar.querySelectorAll('.sidebar-tooltip').forEach(el => el.classList.add('hidden'));
            toggleIcon.textContent = 'chevron_left';
        }
    }

    const savedCollapsed = localStorage.getItem('sidebar_collapsed') === '1';
    applyDesktopState(savedCollapsed);

    toggleBtn.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.contains('md:w-20');
        const next = !isCollapsed;
        applyDesktopState(next);
        localStorage.setItem('sidebar_collapsed', next ? '1' : '0');
    });

    // --- Mobile drawer ---
    window.openDashboardSidebar = function () {
        sidebar.classList.remove('-translate-x-full');
        document.getElementById('dashboard-backdrop').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.closeDashboardSidebar = function () {
        sidebar.classList.add('-translate-x-full');
        document.getElementById('dashboard-backdrop').classList.add('hidden');
        document.body.style.overflow = '';
    };
})();
</script>
@endsection