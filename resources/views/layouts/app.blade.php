<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMAN 1 Turen Math Portal')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "navy-deep": "#0F2544",
                    "math-teal": "#20B2AA",
                    "primary": "#002046",
                    "primary-container": "#1b365d",
                    "on-primary": "#ffffff",
                    "on-primary-container": "#87a0cd",
                    "secondary": "#006a65",
                    "secondary-container": "#76f3ea",
                    "on-secondary-container": "#006f69",
                    "surface-bg": "#F8FAFC",
                    "surface": "#faf9fd",
                    "surface-container": "#efedf1",
                    "surface-container-low": "#f4f3f7",
                    "surface-container-lowest": "#ffffff",
                    "surface-variant": "#e3e2e6",
                    "outline": "#74777f",
                    "outline-variant": "#c4c6cf",
                    "on-surface": "#1a1b1e",
                    "on-surface-variant": "#44474e",
                    "status-success": "#2D6A4F",
                    "status-warning": "#FFB703",
                    "status-error": "#D00000",
                    "error": "#ba1a1a",
                    "error-container": "#ffdad6",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: {
                    "margin-mobile": "16px", "margin-desktop": "40px",
                    "stack-sm": "8px", "stack-md": "16px", "stack-lg": "32px",
                    "gutter": "24px", "container-max": "1280px",
                },
                fontFamily: {
                    "body": ["Inter"], "headline": ["Hanken Grotesk"], "label": ["JetBrains Mono"],
                },
            }
        }
    }
    </script>

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; }
        .hero-gradient { background: linear-gradient(135deg, #0F2544 0%, #1b365d 100%); }
        .math-pattern { background-image: radial-gradient(circle at 2px 2px, #c4c6cf 1px, transparent 0); background-size: 24px 24px; }

        .nav-tooltip { visibility: hidden; opacity: 0; transition: opacity 0.15s ease, visibility 0.15s ease; }
        .nav-icon-btn:hover .nav-tooltip { visibility: visible; opacity: 1; }

        /* Touch target minimum 44px sesuai standar aksesibilitas mobile */
        @media (max-width: 767px) {
            button, a.btn, input[type="submit"] { min-height: 40px; }
        }

        /* Safe-area untuk perangkat dengan notch/gesture bar */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

        /* Mobile drawer menu */
        #mobile-drawer { transition: transform 0.25s ease; }
        #mobile-drawer.hidden-drawer { transform: translateX(-100%); }
        #drawer-backdrop { transition: opacity 0.25s ease; }
    
    @keyframes floatSymbol {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; }
    50% { transform: translateY(-20px) rotate(8deg); opacity: 1; }
}
.floating-symbol {
    animation: floatSymbol 6s ease-in-out infinite;
}
    </style>
    @stack('styles')
</head>
<body class="bg-surface-bg font-body text-on-surface pb-16 md:pb-0">

    <header class="sticky top-0 w-full z-50 flex justify-between items-center px-4 md:px-margin-desktop py-3 bg-surface/90 backdrop-blur-md shadow-sm border-b border-outline-variant/50">
        <div class="flex items-center gap-2">
    @guest('guru')
        @guest('admin')
        <button id="drawer-toggle" type="button" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg text-navy-deep hover:bg-surface-container">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        @endguest
    @endguest
    <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
        <div class="w-9 h-9 rounded-lg hero-gradient flex items-center justify-center">
            <span class="material-symbols-outlined text-white text-[20px]">functions</span>
        </div>
        <span class="font-headline text-base md:text-lg font-bold text-navy-deep hidden xs:inline">SMAN 1 Turen</span>
    </a>
</div>

        @guest('guru')
            @guest('admin')
            {{-- PENGUNJUNG / SISWA --}}
            <nav class="hidden md:flex items-center gap-1">
                @php
                    $guestNav = [
                        ['url' => route('home'), 'icon' => 'home', 'label' => 'Beranda'],
                        ['url' => route('teachers.public'), 'icon' => 'badge', 'label' => 'Profil Guru'],
                        ['url' => route('materials.public'), 'icon' => 'book', 'label' => 'Materi'],
                        ['url' => route('digital-lessons.public'), 'icon' => 'devices', 'label' => 'Pembelajaran Digital'],
                        ['url' => route('toolkits.public'), 'icon' => 'calculate', 'label' => 'Toolkit'],
                        ['url' => route('student-works.public'), 'icon' => 'auto_awesome_motion', 'label' => 'Karya Siswa'],
                        ['url' => route('forum.public'), 'icon' => 'forum', 'label' => 'Forum'],
                        ['url' => route('bank-soal.public'), 'icon' => 'quiz', 'label' => 'Bank Soal'],
                        ['url' => route('latihan.create'), 'icon' => 'history_edu', 'label' => 'Latihan'],
                    ];
                @endphp
                @foreach ($guestNav as $item)
                <a href="{{ $item['url'] }}" class="nav-icon-btn relative flex items-center justify-center w-10 h-10 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-math-teal transition-colors">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    <span class="nav-tooltip absolute top-full mt-2 whitespace-nowrap bg-navy-deep text-white text-xs px-2 py-1 rounded-md z-50 pointer-events-none">
                        {{ $item['label'] }}
                    </span>
                </a>
                @endforeach
            </nav>
            <a href="{{ route('login') }}" class="flex items-center gap-2 bg-navy-deep text-white px-3 md:px-4 py-2 rounded-md font-bold text-sm hover:bg-math-teal transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                <span class="hidden sm:inline">Masuk</span>
            </a>
            @endguest
        @endguest

        @auth('guru')
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-math-teal">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span class="hidden sm:inline">Dashboard</span>
            </a>
            <span class="text-sm text-on-surface-variant hidden lg:inline">{{ auth('guru')->user()->name }}</span>
            <form method="POST" action="{{ route('guru.logout') }}">
                @csrf
                <button class="flex items-center gap-1 text-status-error text-sm font-bold hover:brightness-110">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
        @endauth

        @auth('admin')
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-math-teal">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span class="hidden sm:inline">Dashboard</span>
            </a>
            <span class="text-sm text-on-surface-variant hidden lg:inline">{{ auth('admin')->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="flex items-center gap-1 text-status-error text-sm font-bold hover:brightness-110">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
        @endauth
    </header>

    {{-- Mobile Drawer Menu (hanya untuk pengunjung) --}}
    @guest('guru')
        @guest('admin')
        <div id="drawer-backdrop" class="hidden fixed inset-0 bg-black/40 z-[60] md:hidden" onclick="closeDrawer()"></div>
        <div id="mobile-drawer" class="hidden-drawer fixed top-0 left-0 h-full w-72 bg-white z-[70] md:hidden shadow-2xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-outline-variant">
                <span class="font-headline font-bold text-navy-deep">Menu</span>
                <button onclick="closeDrawer()" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                @foreach ($guestNav as $item)
                <a href="{{ $item['url'] }}" class="flex items-center gap-3 p-3 rounded-lg text-on-surface-variant hover:bg-surface-container font-medium text-sm">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </nav>
            <div class="p-3 border-t border-outline-variant">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 bg-navy-deep text-white py-3 rounded-md font-bold text-sm">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    Masuk
                </a>
            </div>
        </div>
        @endguest
    @endguest

    <main class="min-w-0">
        @yield('content')
    </main>

    @unless (request()->routeIs('guru.*') || request()->routeIs('admin.*'))
    <footer class="bg-navy-deep text-white pt-12 md:pt-16 pb-8 px-4 md:px-margin-desktop mt-20">
        <div class="max-w-container-max mx-auto text-center text-white/60 text-sm">
            &copy; {{ date('Y') }} SMAN 1 Turen Mathematics Portal. All rights reserved.
        </div>
    </footer>
    @endunless

    {{-- Bottom Navigation Bar (mobile only, pengunjung publik) --}}
    @guest('guru')
        @guest('admin')
        <nav class="fixed bottom-0 left-0 w-full z-40 md:hidden flex justify-around items-center px-2 py-2 pb-safe bg-white border-t border-outline-variant shadow-lg">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg {{ request()->routeIs('home') ? 'text-math-teal' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px]">home</span>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>
            <a href="{{ route('materials.public') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg {{ request()->routeIs('materials.*') ? 'text-math-teal' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px]">book</span>
                <span class="text-[10px] font-medium">Materi</span>
            </a>
            <a href="{{ route('forum.public') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg {{ request()->routeIs('forum.*') ? 'text-math-teal' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px]">forum</span>
                <span class="text-[10px] font-medium">Forum</span>
            </a>
            <a href="{{ route('latihan.create') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg {{ request()->routeIs('latihan.*') ? 'text-math-teal' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px]">history_edu</span>
                <span class="text-[10px] font-medium">Latihan</span>
            </a>
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg text-on-surface-variant">
                <span class="material-symbols-outlined text-[22px]">account_circle</span>
                <span class="text-[10px] font-medium">Masuk</span>
            </a>
        </nav>
        @endguest
    @endguest

    <script>
        function openDrawer() {
            document.getElementById('mobile-drawer').classList.remove('hidden-drawer');
            document.getElementById('drawer-backdrop').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            document.getElementById('mobile-drawer').classList.add('hidden-drawer');
            document.getElementById('drawer-backdrop').classList.add('hidden');
            document.body.style.overflow = '';
        }
        const drawerToggle = document.getElementById('drawer-toggle');
        if (drawerToggle) drawerToggle.addEventListener('click', openDrawer);
    </script>

    @stack('scripts')
</body>
</html>