@extends('layouts.app')
@section('title', 'Beranda - SMAN 1 Turen Math Portal')

@push('styles')
<style>
    @keyframes floatSymbol {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.5; }
        50% { transform: translateY(-18px) rotate(6deg); opacity: 0.9; }
    }
    .floating-symbol { animation: floatSymbol 7s ease-in-out infinite; }

    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(32, 178, 170, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(32, 178, 170, 0); }
    }
    .glow-badge { animation: pulseGlow 2.5s ease-in-out infinite; }

    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.7s ease-out, transform 0.7s ease-out;
    }
    .reveal-on-scroll.revealed { opacity: 1; transform: translateY(0); }

    .bento-item { transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .bento-item:hover { transform: translateY(-4px); }

    .step-line {
        background: linear-gradient(90deg, transparent, #20B2AA 20%, #20B2AA 80%, transparent);
        height: 2px;
    }

    .spot-card {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
    }
    .spot-card:hover {
        transform: translateY(-6px) scale(1.015);
        box-shadow: 0 20px 40px -12px rgba(15, 37, 68, 0.35);
    }

    .spot-glow {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle 180px at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.25), transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .spot-card:hover .spot-glow { opacity: 1; }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .icon-float { animation: iconFloat 3s ease-in-out infinite; }

    .spot-card {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
    }
    .spot-card:hover {
        transform: translateY(-6px) scale(1.015);
        box-shadow: 0 20px 40px -12px rgba(15, 37, 68, 0.35);
    }

    .spot-glow {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle 180px at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.25), transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }
    .spot-card:hover .spot-glow { opacity: 1; }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .icon-float { animation: iconFloat 3s ease-in-out infinite; }

    @keyframes decoFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; }
        50% { transform: translateY(-10px) rotate(8deg); opacity: 1; }
    }
    .deco-float { animation: decoFloat 4s ease-in-out infinite; }

    .spot-card:hover .material-symbols-outlined.absolute {
        transform: scale(1.08) rotate(3deg);
        transition: transform 0.6s ease;
    }
</style>

@endpush

@section('content')

{{-- ============ HERO ============ --}}
<section class="hero-gradient relative overflow-hidden">
    <div class="absolute inset-0 math-pattern opacity-10"></div>
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 6%; top: 18%; font-size: 2.8rem;">∑</span>
        <span class="floating-symbol absolute text-math-teal/20 font-bold" style="left: 88%; top: 15%; font-size: 2.2rem; animation-delay: 1.5s;">π</span>
        <span class="floating-symbol absolute text-white/10 font-bold hidden md:inline" style="left: 12%; top: 72%; font-size: 2rem; animation-delay: 3s;">√</span>
        <span class="floating-symbol absolute text-math-teal/15 font-bold" style="left: 90%; top: 68%; font-size: 3rem; animation-delay: 0.8s;">∞</span>
    </div>

    <div class="relative z-10 max-w-container-max mx-auto px-4 md:px-margin-desktop pt-12 pb-16 md:pt-20 md:pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            {{-- Left: text --}}
            <div class="lg:col-span-7 text-center lg:text-left">
                <div class="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 backdrop-blur-sm rounded-full px-3 py-1 mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-math-teal glow-badge"></span>
                    <span class="text-white/80 text-[10px] md:text-xs font-medium">SMA NEGERI 1 TUREN</span>
                </div>

                <h1 class="font-headline text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-[1.1] mb-4">
                    Belajar Matematika<br>
                    <span class="text-math-teal">Lebih Cerdas</span> &amp; Terarah
                </h1>
                <p class="text-white/70 text-sm md:text-base max-w-lg mx-auto lg:mx-0 mb-7">
                    Selamat datang di Portal Matematika SMA Negeri 1 Turen.
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center lg:justify-start gap-3">
                    <a href="{{ route('materials.public') }}"
                       class="flex items-center justify-center gap-2 bg-math-teal text-white px-6 py-3 rounded-full font-bold text-sm hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-math-teal/25">
                        <span class="material-symbols-outlined text-[20px]">school</span>
                        Mulai Belajar
                    </a>
                    <a href="{{ route('login') }}"
                       class="flex items-center justify-center gap-2 bg-white/10 border border-white/20 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/20 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">grade</span>
                        Cek Nilai Saya
                    </a>
                </div>
            </div>

            {{-- Right: live clock + stats card --}}
            <div class="lg:col-span-5">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-3xl p-6 shadow-2xl">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-white/50 text-[10px] uppercase tracking-widest font-label">Waktu Sekarang</p>
                            <p class="text-white text-sm font-medium mt-0.5" id="real-time-date">--</p>
                            <p class="text-math-teal text-2xl font-bold font-label mt-1" id="real-time-clock">--:--:--</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-math-teal/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-math-teal text-2xl">calendar_month</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/5 rounded-2xl p-4 text-center">
                            <span class="material-symbols-outlined text-math-teal text-xl">visibility</span>
                            <p class="text-white text-xl font-bold mt-1">{{ number_format($visitorToday) }}</p>
                            <p class="text-white/50 text-[10px] uppercase tracking-wide">Hari Ini</p>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 text-center">
                            <span class="material-symbols-outlined text-math-teal text-xl">group</span>
                            <p class="text-white text-xl font-bold mt-1">{{ number_format($visitorTotal) }}</p>
                            <p class="text-white/50 text-[10px] uppercase tracking-wide">Total</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- wave divider --}}
    <div class="absolute bottom-0 left-0 w-full leading-[0]">
        <svg viewBox="0 0 1440 60" class="w-full h-10 md:h-14" preserveAspectRatio="none">
            <path fill="#F8FAFC" d="M0,32L80,28C160,24,320,16,480,18.7C640,21,800,35,960,37.3C1120,40,1280,32,1360,28L1440,24L1440,60L0,60Z"></path>
        </svg>
    </div>
</section>

{{-- ============ FITUR UTAMA — INTERACTIVE BENTO GRID ============ --}}
<section class="px-4 md:px-margin-desktop max-w-container-max mx-auto -mt-2 pb-4 reveal-on-scroll">
    <div class="text-center mb-8 md:mb-10">
        <span class="text-math-teal font-label text-xs uppercase tracking-widest">Semua yang Kamu Butuhkan</span>
        <h2 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep mt-1">Fitur Utama Portal</h2>
        <div class="w-14 h-1 bg-math-teal rounded-full mx-auto mt-3"></div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 auto-rows-[130px] md:auto-rows-[150px]">

        {{-- MATERI — kartu besar --}}
        <a href="{{ route('materials.public') }}" data-spot
           class="spot-card bento-item col-span-2 row-span-2 rounded-3xl p-5 md:p-6 flex flex-col justify-between relative overflow-hidden group text-white"
           style="background: linear-gradient(135deg, #0F2544 0%, #1b365d 55%, #20B2AA 140%);">
            <div class="spot-glow"></div>
            <div class="absolute inset-0 math-pattern opacity-[0.06]"></div>
            <span class="material-symbols-outlined absolute -right-6 -bottom-10 text-white/[0.07] pointer-events-none select-none" style="font-size: 220px;">auto_stories</span>
            <span class="deco-float absolute text-math-teal/25 font-headline font-bold" style="right: 18%; top: 22%; font-size: 2.2rem;">∫</span>
            <span class="deco-float absolute text-white/15 font-headline font-bold" style="right: 32%; top: 55%; font-size: 1.6rem; animation-delay: 1.2s;">π</span>
            <span class="deco-float absolute text-math-teal/20 font-headline font-bold" style="right: 10%; top: 45%; font-size: 1.4rem; animation-delay: 2.1s;">√x</span>

            <div class="relative z-10 flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center icon-float">
                    <span class="material-symbols-outlined text-math-teal text-2xl">book</span>
                </div>
                <span class="text-[10px] font-bold bg-white/15 backdrop-blur-sm px-2 py-1 rounded-full">X - XII</span>
            </div>
            <div class="relative z-10">
                <h3 class="font-headline text-lg md:text-xl font-bold mb-1">Materi Pembelajaran</h3>
                <p class="text-white/70 text-xs md:text-sm mb-3">Modul lengkap sesuai Kurikulum Merdeka</p>
                <span class="inline-flex items-center gap-1 text-math-teal text-xs font-bold opacity-0 group-hover:opacity-100 translate-x-[-8px] group-hover:translate-x-0 transition-all duration-300">
                    Jelajahi <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        {{-- LATIHAN AI --}}
        <a href="{{ route('latihan.create') }}" data-spot
           class="spot-card bento-item rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group text-white"
           style="background: linear-gradient(135deg, #2D6A4F 0%, #52B788 100%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute -right-5 -bottom-6 text-white/10 pointer-events-none select-none" style="font-size: 110px;">quiz</span>
            <span class="deco-float absolute text-white/20 font-headline font-bold" style="right: 15%; top: 18%; font-size: 1.3rem;">✓</span>
            <div class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center relative z-10 icon-float">
                <span class="material-symbols-outlined text-xl">history_edu</span>
            </div>
            <div class="relative z-10">
                <h3 class="font-headline text-sm md:text-base font-bold">Latihan AI</h3>
                <p class="text-white/70 text-[10px] mt-0.5 hidden md:block">10 soal adaptif</p>
            </div>
        </a>

        {{-- BANK SOAL --}}
        <a href="{{ route('bank-soal.public') }}" data-spot
           class="spot-card bento-item rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group text-white"
           style="background: linear-gradient(135deg, #D00000 0%, #E85D5D 100%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute -right-5 -bottom-6 text-white/10 pointer-events-none select-none" style="font-size: 110px;">help</span>
            <span class="deco-float absolute text-white/20 font-headline font-bold" style="right: 18%; top: 20%; font-size: 1.4rem; animation-delay: 0.6s;">?</span>
            <div class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center relative z-10 icon-float">
                <span class="material-symbols-outlined text-xl">quiz</span>
            </div>
            <div class="relative z-10">
                <h3 class="font-headline text-sm md:text-base font-bold">Bank Soal</h3>
                <p class="text-white/70 text-[10px] mt-0.5 hidden md:block">Soal siap pakai</p>
            </div>
        </a>

        {{-- PEMBELAJARAN DIGITAL --}}
        <a href="{{ route('digital-lessons.public') }}" data-spot
           class="spot-card bento-item rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group text-white"
           style="background: linear-gradient(135deg, #002046 0%, #2E476F 100%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute -right-5 -bottom-6 text-white/[0.07] pointer-events-none select-none" style="font-size: 110px;">smart_display</span>
            <span class="deco-float absolute text-math-teal/25" style="right: 20%; top: 22%; font-size: 1.2rem; animation-delay: 1.5s;">
                <span class="material-symbols-outlined text-[20px]">play_circle</span>
            </span>
            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center relative z-10 icon-float">
                <span class="material-symbols-outlined text-math-teal text-xl">devices</span>
            </div>
            <div class="relative z-10">
                <h3 class="font-headline text-sm md:text-base font-bold">Pembelajaran Digital</h3>
                <p class="text-white/60 text-[10px] mt-0.5 hidden md:block">Video & simulasi</p>
            </div>
        </a>

        {{-- TOOLKIT --}}
        <a href="{{ route('toolkits.public') }}" data-spot
           class="spot-card bento-item rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group text-white"
           style="background: linear-gradient(135deg, #0F8B8D 0%, #20B2AA 60%, #59DAD1 100%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute -right-5 -bottom-6 text-white/10 pointer-events-none select-none" style="font-size: 110px;">calculate</span>
            <span class="deco-float absolute text-white/20 font-headline font-bold" style="right: 20%; top: 20%; font-size: 1.3rem; animation-delay: 2s;">%</span>
            <div class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center relative z-10 icon-float">
                <span class="material-symbols-outlined text-xl">calculate</span>
            </div>
            <div class="relative z-10">
                <h3 class="font-headline text-sm md:text-base font-bold">Toolkit</h3>
                <p class="text-white/80 text-[10px] mt-0.5 hidden md:block">Kalkulator & grafik</p>
            </div>
        </a>

        {{-- FORUM --}}
        <a href="{{ route('forum.public') }}" data-spot
           class="spot-card bento-item col-span-2 rounded-3xl p-4 md:p-5 flex items-center justify-between group relative overflow-hidden text-white"
           style="background: linear-gradient(120deg, #006A65 0%, #59DAD1 130%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute right-8 -top-6 text-white/10 pointer-events-none select-none" style="font-size: 130px;">forum</span>
            <span class="deco-float absolute text-white/15" style="right: 30%; bottom: 15%; animation-delay: 1s;">
                <span class="material-symbols-outlined text-[26px]">chat_bubble</span>
            </span>
            <div class="relative z-10 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center shrink-0 icon-float">
                    <span class="material-symbols-outlined text-xl">forum</span>
                </div>
                <div>
                    <h3 class="font-headline text-sm md:text-base font-bold">Forum Diskusi</h3>
                    <p class="text-white/70 text-[11px] md:text-xs">Tanya jawab seputar matematika</p>
                </div>
            </div>
            <span class="material-symbols-outlined relative z-10 group-hover:translate-x-1.5 transition-transform">arrow_forward</span>
        </a>

        {{-- KARYA SISWA --}}
        <a href="{{ route('student-works.public') }}" data-spot
           class="spot-card bento-item col-span-2 rounded-3xl p-4 md:p-5 flex items-center justify-between group relative overflow-hidden text-white"
           style="background: linear-gradient(120deg, #B36A00 0%, #FFB703 130%);">
            <div class="spot-glow"></div>
            <span class="material-symbols-outlined absolute right-8 -top-6 text-white/10 pointer-events-none select-none" style="font-size: 130px;">palette</span>
            <span class="deco-float absolute text-white/20" style="right: 32%; bottom: 18%; animation-delay: 1.8s;">
                <span class="material-symbols-outlined text-[24px]">star</span>
            </span>
            <div class="relative z-10 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 icon-float">
                    <span class="material-symbols-outlined text-xl">auto_awesome_motion</span>
                </div>
                <div>
                    <h3 class="font-headline text-sm md:text-base font-bold">Karya Siswa</h3>
                    <p class="text-white/80 text-[11px] md:text-xs">Galeri kreativitas matematika</p>
                </div>
            </div>
            <span class="material-symbols-outlined relative z-10 group-hover:translate-x-1.5 transition-transform">arrow_forward</span>
        </a>

    </div>
</section>

{{-- ============ KUTIPAN MOTIVASI ============ --}}
<section class="py-14 md:py-20 px-4 md:px-margin-desktop max-w-container-max mx-auto reveal-on-scroll">
    <div class="relative bg-gradient-to-br from-math-teal/5 via-white to-primary/5 border border-outline-variant/30 rounded-[2rem] p-8 md:p-14 text-center overflow-hidden">
        <div class="absolute top-6 left-6 md:top-8 md:left-10 text-math-teal/15 font-headline text-7xl md:text-9xl leading-none select-none">&ldquo;</div>
        <div class="relative z-10 max-w-2xl mx-auto">
            <p class="font-headline text-lg md:text-2xl font-bold text-navy-deep leading-relaxed mb-5">
                Setiap angka memiliki makna, setiap masalah memiliki solusi, dan setiap pembelajaran membuka jalan menuju masa depan.
            </p>
            <div class="flex items-center justify-center gap-3">
                <div class="w-10 h-10 rounded-full bg-navy-deep flex items-center justify-center">
                    <span class="material-symbols-outlined text-math-teal text-[18px]">functions</span>
                </div>
                <div class="text-left">
                    <p class="font-bold text-navy-deep text-sm">MGMP Matematika</p>
                    <p class="text-on-surface-variant text-xs">SMA Negeri 1 Turen</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ CTA GANDA ============ --}}
<section class="py-4 pb-14 md:pb-20 px-4 md:px-margin-desktop max-w-container-max mx-auto reveal-on-scroll">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

        <div class="bg-navy-deep rounded-3xl p-7 md:p-9 relative overflow-hidden">
            <div class="absolute inset-0 math-pattern opacity-5"></div>
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-math-teal/20 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <span class="material-symbols-outlined text-math-teal text-3xl mb-3 block">grade</span>
                <h3 class="font-headline text-white text-lg md:text-xl font-bold mb-2">Cek Nilai &amp; Kehadiran</h3>
                <p class="text-white/60 text-sm mb-5">Pantau perkembangan belajarmu secara real-time, lengkap dengan statistik kelas.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-math-teal text-white px-5 py-2.5 rounded-full font-bold text-sm hover:brightness-110 active:scale-95 transition-all">
                    Cek Sekarang <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="bg-gradient-to-br from-math-teal/10 to-primary/10 border border-math-teal/20 rounded-3xl p-7 md:p-9 relative overflow-hidden">
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-full hero-gradient flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-white text-xl">smart_toy</span>
                </div>
                <h3 class="font-headline text-navy-deep text-lg md:text-xl font-bold mb-2">Tanya Asisten AI</h3>
                <p class="text-on-surface-variant text-sm mb-5">Butuh bantuan cepat soal matematika atau cara pakai portal? Klik ikon robot di pojok kanan bawah, kapan saja.</p>
                <span class="inline-flex items-center gap-2 text-math-teal font-bold text-sm">
                    <span class="material-symbols-outlined text-[18px] animate-bounce">south_east</span> Coba sekarang
                </span>
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    const timeStr = now.toLocaleTimeString('id-ID');
    document.getElementById('real-time-date').innerText = dateStr;
    document.getElementById('real-time-clock').innerText = timeStr;
}
setInterval(updateClock, 1000);
updateClock();

document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
});

document.addEventListener('DOMContentLoaded', () => {
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.dataset.target, 10);
            const duration = 1400;
            const start = performance.now();

            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target);
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target;
            }
            requestAnimationFrame(tick);
            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-spot]').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            card.style.setProperty('--x', x + '%');
            card.style.setProperty('--y', y + '%');
        });
    });
});
</script>

@endpush