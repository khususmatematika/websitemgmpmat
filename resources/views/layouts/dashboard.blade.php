@extends('layouts.app')

@section('content')
@php
    $guard = $guard ?? 'guru';
    $panelTitle = $panelTitle ?? 'Panel';
    $dashUser = auth()->guard($guard)->user();
    $dashPeriod = \App\Models\AcademicYear::active();
    $initial = mb_strtoupper(mb_substr($dashUser->name ?? '?', 0, 1));
@endphp

<style>
    #dash-aside { width: 256px; transition: width .2s ease; }
    #dash-aside.is-collapsed { width: 76px; }

    #dash-aside.is-collapsed .nav-label,
    #dash-aside.is-collapsed .brand-text,
    #dash-aside.is-collapsed .user-text { display: none; }

    #dash-aside.is-collapsed .dash-top { justify-content: center; }
    #dash-aside.is-collapsed .nav-link,
    #dash-aside.is-collapsed .logout-btn {
        justify-content: center; padding: 0; width: 48px; height: 48px; margin-left: auto; margin-right: auto;
    }
    #dash-aside.is-collapsed .active-bar { left: -14px; }
    #dash-aside.is-collapsed .user-card { justify-content: center; padding-left: 0; padding-right: 0; }

    .nav-scroll { overflow-y: auto; overflow-x: hidden; scrollbar-width: thin; scrollbar-color: #c4c6cf transparent; }
    #dash-aside.is-collapsed .nav-scroll { scrollbar-width: none; }
    #dash-aside.is-collapsed .nav-scroll::-webkit-scrollbar { display: none; }
    .nav-scroll::-webkit-scrollbar { width: 4px; }
    .nav-scroll::-webkit-scrollbar-thumb { background: #c4c6cf; border-radius: 4px; }

    #dash-drawer { transform: translateX(-100%); transition: transform .25s ease; }
    #dash-drawer.open { transform: translateX(0); }
</style>

<div class="flex min-h-[calc(100vh-62px)]">

    {{-- SIDEBAR DESKTOP --}}
    <aside id="dash-aside"
           class="hidden md:flex flex-col shrink-0 sticky top-[62px] h-[calc(100vh-62px)] bg-white border-r border-outline-variant/50 z-30">
        <div class="dash-top h-14 flex items-center justify-between px-3 border-b border-outline-variant/40 shrink-0">
            <span class="brand-text font-headline font-bold text-navy-deep text-sm truncate pl-1">{{ $panelTitle }}</span>
            <button type="button" id="dash-toggle" title="Ciutkan/lebarkan menu"
                    class="w-10 h-10 rounded-xl hover:bg-surface-container flex items-center justify-center text-on-surface-variant shrink-0">
                <span class="material-symbols-outlined" id="dash-toggle-icon">menu_open</span>
            </button>
        </div>

        <nav class="nav-scroll flex-1 py-3 px-3 space-y-1">
            @include('partials.dash-nav', ['navItems' => $navItems])
        </nav>

        <div class="shrink-0 border-t border-outline-variant/40 p-3 space-y-2">
            <div class="user-card flex items-center gap-3 px-2 py-1.5">
                <div class="w-9 h-9 rounded-full bg-math-teal/10 text-math-teal font-bold flex items-center justify-center shrink-0" data-tip="{{ $dashUser->name }}">{{ $initial }}</div>
                <div class="user-text min-w-0">
                    <p class="text-sm font-bold text-navy-deep truncate">{{ $dashUser->name }}</p>
                    @if ($dashPeriod)
                        <p class="text-[10px] text-math-teal font-bold truncate">{{ $dashPeriod->label }}</p>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route($guard . '.logout') }}">
                @csrf
                <button data-tip="Keluar"
                        class="logout-btn w-full h-11 px-3 rounded-xl flex items-center gap-3 text-sm font-medium text-status-error hover:bg-error-container/40 transition-colors">
                    <span class="material-symbols-outlined text-[22px] shrink-0">logout</span>
                    <span class="nav-label">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- KONTEN --}}
    <div class="flex-1 min-w-0">
        {{-- Bar atas khusus HP --}}
        <div class="md:hidden sticky top-[62px] z-30 bg-white/90 backdrop-blur border-b border-outline-variant/50 flex items-center gap-2 px-3 py-2">
            <button type="button" onclick="openDash()" class="w-10 h-10 rounded-xl hover:bg-surface-container flex items-center justify-center text-navy-deep">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="font-headline font-bold text-navy-deep text-sm truncate">{{ $panelTitle }}</span>
            @if ($dashPeriod)
                <span class="ml-auto text-[10px] text-math-teal font-bold truncate max-w-[40%]">{{ $dashPeriod->label }}</span>
            @endif
        </div>

        <main class="p-4 md:p-8 pb-28 md:pb-10 space-y-6">
            @yield('dashboard-content')
        </main>
    </div>
</div>

{{-- DRAWER HP --}}
<div id="dash-backdrop" class="hidden fixed inset-0 bg-black/40 z-[60] md:hidden" onclick="closeDash()"></div>
<div id="dash-drawer" class="fixed top-0 left-0 h-full w-72 bg-white z-[70] md:hidden shadow-2xl flex flex-col">
    <div class="h-14 flex items-center justify-between px-4 border-b border-outline-variant/40 shrink-0">
        <span class="font-headline font-bold text-navy-deep">{{ $panelTitle }}</span>
        <button type="button" onclick="closeDash()" class="w-10 h-10 rounded-xl hover:bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <nav class="nav-scroll flex-1 py-3 px-3 space-y-1">
        @include('partials.dash-nav', ['navItems' => $navItems])
    </nav>
    <div class="shrink-0 border-t border-outline-variant/40 p-3">
        <div class="flex items-center gap-3 px-2 py-1.5 mb-2">
            <div class="w-9 h-9 rounded-full bg-math-teal/10 text-math-teal font-bold flex items-center justify-center">{{ $initial }}</div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-navy-deep truncate">{{ $dashUser->name }}</p>
                @if ($dashPeriod)<p class="text-[10px] text-math-teal font-bold truncate">{{ $dashPeriod->label }}</p>@endif
            </div>
        </div>
        <form method="POST" action="{{ route($guard . '.logout') }}">
            @csrf
            <button class="w-full h-11 px-3 rounded-xl flex items-center gap-3 text-sm font-medium text-status-error hover:bg-error-container/40">
                <span class="material-symbols-outlined text-[22px]">logout</span> Keluar
            </button>
        </form>
    </div>
</div>

{{-- Tooltip tunggal (tidak terpotong oleh area scroll) --}}
<div id="dash-tip" class="hidden fixed -translate-y-1/2 z-[80] bg-navy-deep text-white text-xs px-3 py-1.5 rounded-lg shadow-lg pointer-events-none whitespace-nowrap"></div>

<script>
(function () {
    const aside = document.getElementById('dash-aside');
    const icon = document.getElementById('dash-toggle-icon');
    const tip = document.getElementById('dash-tip');

    function apply(collapsed) {
        aside.classList.toggle('is-collapsed', collapsed);
        icon.textContent = collapsed ? 'menu' : 'menu_open';
    }

    let saved = false;
    try { saved = localStorage.getItem('dash_collapsed') === '1'; } catch (e) {}
    apply(saved);

    document.getElementById('dash-toggle').addEventListener('click', () => {
        const next = !aside.classList.contains('is-collapsed');
        apply(next);
        tip.classList.add('hidden');
        try { localStorage.setItem('dash_collapsed', next ? '1' : '0'); } catch (e) {}
    });

    aside.querySelectorAll('[data-tip]').forEach(el => {
        el.addEventListener('mouseenter', () => {
            if (!aside.classList.contains('is-collapsed')) return;
            const r = el.getBoundingClientRect();
            tip.textContent = el.dataset.tip;
            tip.style.top = (r.top + r.height / 2) + 'px';
            tip.style.left = (r.right + 10) + 'px';
            tip.classList.remove('hidden');
        });
        el.addEventListener('mouseleave', () => tip.classList.add('hidden'));
    });
    aside.querySelector('.nav-scroll').addEventListener('scroll', () => tip.classList.add('hidden'));
})();

function openDash() {
    document.getElementById('dash-drawer').classList.add('open');
    document.getElementById('dash-backdrop').classList.remove('hidden');
}
function closeDash() {
    document.getElementById('dash-drawer').classList.remove('open');
    document.getElementById('dash-backdrop').classList.add('hidden');
}
</script>
@endsection