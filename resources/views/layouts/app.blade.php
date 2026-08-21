<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
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

   <header class="sticky top-0 w-full z-50 bg-white/80 backdrop-blur-xl border-b border-outline-variant/50 shadow-sm">
        <div class="max-w-container-max mx-auto flex justify-between items-center px-4 md:px-margin-desktop py-3">
            <div class="flex items-center gap-2">
                @guest('guru')
                    @guest('admin')
                    <button id="drawer-toggle" type="button" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg text-navy-deep hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[24px]">menu</span>
                    </button>
                    @endguest
                @endguest
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="w-9 h-9 rounded-xl hero-gradient flex items-center justify-center shadow-md shadow-navy-deep/20 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-white text-[20px]">functions</span>
                    </div>
                    <div class="hidden xs:flex flex-col leading-none">
                        <span class="font-headline text-sm md:text-base font-bold text-navy-deep">SMAN 1 Turen</span>
                        <span class="font-label text-[9px] text-math-teal tracking-wider uppercase hidden md:block">Math Portal</span>
                    </div>
                </a>
            </div>

            @guest('guru')
                @guest('admin')
                <nav class="hidden md:flex items-center gap-0.5 bg-surface-container-low rounded-full p-1">
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
                    <a href="{{ $item['url'] }}"
                    class="nav-icon-btn relative flex items-center justify-center w-9 h-9 rounded-full transition-all
                            {{ request()->url() === $item['url'] ? 'bg-navy-deep text-white shadow-md' : 'text-on-surface-variant hover:bg-white hover:text-math-teal hover:shadow-sm' }}">
                        <span class="material-symbols-outlined text-[19px]">{{ $item['icon'] }}</span>
                        <span class="nav-tooltip absolute top-full mt-2 whitespace-nowrap bg-navy-deep text-white text-xs px-2.5 py-1.5 rounded-lg z-50 pointer-events-none shadow-lg">
                            {{ $item['label'] }}
                        </span>
                    </a>
                    @endforeach
                </nav>
                <a href="{{ route('login') }}" class="flex items-center gap-2 bg-navy-deep text-white px-4 md:px-5 py-2.5 rounded-full font-bold text-sm hover:bg-math-teal hover:shadow-lg hover:shadow-math-teal/30 active:scale-95 transition-all shrink-0">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    <span class="hidden sm:inline">Masuk</span>
                </a>
                @endguest
            @endguest

            @auth('guru')
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-full bg-surface-container-low text-sm font-bold text-navy-deep hover:bg-math-teal/10 hover:text-math-teal transition-colors">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                <div class="hidden md:flex items-center gap-2 pl-3 border-l border-outline-variant">
                    <div class="w-8 h-8 rounded-full bg-math-teal/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-math-teal text-[16px]">person</span>
                    </div>
                    <span class="text-sm text-on-surface-variant font-medium">{{ auth('guru')->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('guru.logout') }}">
                    @csrf
                    <button class="w-9 h-9 flex items-center justify-center rounded-full text-status-error hover:bg-error-container/40 transition-colors" title="Keluar">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
            @endauth

            @auth('admin')
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-full bg-surface-container-low text-sm font-bold text-navy-deep hover:bg-math-teal/10 hover:text-math-teal transition-colors">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                <div class="hidden md:flex items-center gap-2 pl-3 border-l border-outline-variant">
                    <div class="w-8 h-8 rounded-full bg-navy-deep/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-navy-deep text-[16px]">admin_panel_settings</span>
                    </div>
                    <span class="text-sm text-on-surface-variant font-medium">{{ auth('admin')->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="w-9 h-9 flex items-center justify-center rounded-full text-status-error hover:bg-error-container/40 transition-colors" title="Keluar">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
            @endauth
        </div>
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
    <footer class="bg-navy-deep text-white py-6 px-4 md:px-margin-desktop mt-20">
        <div class="max-w-container-max mx-auto text-center text-white/60 text-xs md:text-sm">
            &copy; MGMP Matematika SMA Negeri 1 Turen 2026
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
            <a href="{{ route('nilai.login') }}" class="flex flex-col items-center justify-center gap-0.5 py-1 px-3 rounded-lg {{ request()->routeIs('nilai.*') ? 'text-math-teal' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px]">grade</span>
                <span class="text-[10px] font-medium">Nilai</span>
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

        {{-- Widget Asisten Pemandu --}}
    <div id="assistant-widget" class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-50">
        <button id="assistant-toggle" class="w-14 h-14 rounded-full hero-gradient shadow-xl flex items-center justify-center text-white hover:scale-110 active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-[26px]" id="assistant-icon">smart_toy</span>
        </button>

        <div id="assistant-panel" class="hidden absolute bottom-16 right-0 w-[calc(100vw-2rem)] max-w-sm bg-white rounded-2xl shadow-2xl border border-outline-variant/30 overflow-hidden">
            <div class="hero-gradient p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-[20px]">smart_toy</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Asisten Portal</p>
                    <p class="text-white/70 text-[10px]">Siap membantu 24/7</p>
                </div>
            </div>

            <div id="assistant-messages" class="h-72 overflow-y-auto p-4 space-y-3 bg-surface-bg">
                <div class="bg-white rounded-xl rounded-tl-sm p-3 text-xs text-on-surface max-w-[85%] shadow-sm">
                    Halo! Aku Asisten Portal 🤖 Tanya apa saja seputar Matematika (rumus, konsep, cara kerjakan soal) atau cara pakai website ini ya!
                </div>
            </div>

            <div class="p-3 border-t border-outline-variant bg-white">
                <form id="assistant-form" class="flex gap-2">
                    <input type="text" id="assistant-input" placeholder="Ketik pertanyaan..." autocomplete="off"
                        class="flex-1 text-sm rounded-full border-outline-variant focus:ring-math-teal focus:border-math-teal">
                    <button type="submit" class="w-9 h-9 rounded-full bg-math-teal text-white flex items-center justify-center shrink-0 hover:brightness-110">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const toggle = document.getElementById('assistant-toggle');
        const panel = document.getElementById('assistant-panel');
        const icon = document.getElementById('assistant-icon');
        const form = document.getElementById('assistant-form');
        const input = document.getElementById('assistant-input');
        const messages = document.getElementById('assistant-messages');

        toggle.addEventListener('click', () => {
            const isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            icon.textContent = isHidden ? 'close' : 'smart_toy';
        });

        function addMessage(text, isUser) {
            const bubble = document.createElement('div');
            bubble.className = isUser
                ? 'bg-navy-deep text-white rounded-xl rounded-tr-sm p-3 text-xs max-w-[85%] ml-auto shadow-sm'
                : 'bg-white rounded-xl rounded-tl-sm p-3 text-xs text-on-surface max-w-[85%] shadow-sm';
            bubble.textContent = text;
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const question = input.value.trim();
            if (!question) return;

            addMessage(question, true);
            input.value = '';

            const typingBubble = document.createElement('div');
            typingBubble.className = 'bg-white rounded-xl rounded-tl-sm p-3 text-xs text-on-surface-variant max-w-[85%] shadow-sm';
            typingBubble.id = 'typing-indicator';
            typingBubble.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin align-middle">progress_activity</span> Mengetik...';
            messages.appendChild(typingBubble);
            messages.scrollTop = messages.scrollHeight;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch('{{ route("assistant.ask") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ question }),
                });
                const data = await res.json();
                document.getElementById('typing-indicator')?.remove();
                addMessage(data.answer, false);
            } catch (err) {
                document.getElementById('typing-indicator')?.remove();
                addMessage('Maaf, terjadi kesalahan. Coba lagi.', false);
            }
        });
    })();
    </script>

    {{-- Tombol Back to Top --}}
    <button id="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="hidden fixed bottom-36 md:bottom-6 right-4 md:right-24 z-40 w-11 h-11 rounded-full bg-white border border-outline-variant shadow-lg flex items-center justify-center text-navy-deep hover:bg-navy-deep hover:text-white transition-all">
        <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
    </button>

    <script>
    window.addEventListener('scroll', () => {
        const btn = document.getElementById('back-to-top');
        if (window.scrollY > 400) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    });
    </script>

    @stack('scripts')
</body>
</html>