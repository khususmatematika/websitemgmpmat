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
                    <span class="text-white/80 text-[10px] md:text-xs font-medium">Portal Aktif &middot; Terintegrasi AI</span>
                </div>

                <h1 class="font-headline text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-[1.1] mb-4">
                    Belajar Matematika<br>
                    <span class="text-math-teal">Lebih Cerdas</span> &amp; Terarah
                </h1>
                <p class="text-white/70 text-sm md:text-base max-w-lg mx-auto lg:mx-0 mb-7">
                    Satu portal untuk materi, latihan adaptif berbasis AI, karya kreatif siswa, dan pemantauan nilai &mdash; dirancang khusus untuk SMAN 1 Turen.
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

{{-- ============ FITUR UTAMA — BENTO GRID ============ --}}
<section class="px-4 md:px-margin-desktop max-w-container-max mx-auto -mt-2 pb-4 reveal-on-scroll">
    <div class="text-center mb-8 md:mb-10">
        <span class="text-math-teal font-label text-xs uppercase tracking-widest">Semua yang Kamu Butuhkan</span>
        <h2 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep mt-1">Fitur Utama Portal</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 auto-rows-[130px] md:auto-rows-[150px]">

        <a href="{{ route('materials.public') }}" class="bento-item col-span-2 row-span-2 bg-navy-deep rounded-3xl p-5 md:p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-math-teal/20 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <span class="material-symbols-outlined text-math-teal text-3xl relative z-10">book</span>
            <div class="relative z-10">
                <h3 class="font-headline text-white text-lg md:text-xl font-bold mb-1">Materi Pembelajaran</h3>
                <p class="text-white/60 text-xs md:text-sm">Modul lengkap kelas X-XII sesuai Kurikulum Merdeka</p>
            </div>
        </a>

        <a href="{{ route('latihan.create') }}" class="bento-item bg-status-success rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group">
            <span class="material-symbols-outlined text-white text-2xl">history_edu</span>
            <h3 class="font-headline text-white text-sm md:text-base font-bold">Latihan AI</h3>
        </a>

        <a href="{{ route('bank-soal.public') }}" class="bento-item bg-status-error rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group">
            <span class="material-symbols-outlined text-white text-2xl">quiz</span>
            <h3 class="font-headline text-white text-sm md:text-base font-bold">Bank Soal</h3>
        </a>

        <a href="{{ route('digital-lessons.public') }}" class="bento-item bg-primary rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group">
            <span class="material-symbols-outlined text-math-teal text-2xl">devices</span>
            <h3 class="font-headline text-white text-sm md:text-base font-bold">Pembelajaran Digital</h3>
        </a>

        <a href="{{ route('toolkits.public') }}" class="bento-item bg-math-teal rounded-3xl p-4 md:p-5 flex flex-col justify-between relative overflow-hidden group">
            <span class="material-symbols-outlined text-white text-2xl">calculate</span>
            <h3 class="font-headline text-white text-sm md:text-base font-bold">Toolkit</h3>
        </a>

        <a href="{{ route('forum.public') }}" class="bento-item col-span-2 bg-white border border-outline-variant/30 rounded-3xl p-4 md:p-5 flex items-center justify-between group hover:shadow-lg transition-shadow">
            <div>
                <span class="material-symbols-outlined text-secondary text-2xl mb-1 block">forum</span>
                <h3 class="font-headline text-navy-deep text-sm md:text-base font-bold">Forum Diskusi</h3>
                <p class="text-on-surface-variant text-[11px] md:text-xs">Tanya jawab seputar matematika</p>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>

        <a href="{{ route('student-works.public') }}" class="bento-item col-span-2 bg-white border border-outline-variant/30 rounded-3xl p-4 md:p-5 flex items-center justify-between group hover:shadow-lg transition-shadow">
            <div>
                <span class="material-symbols-outlined text-status-warning text-2xl mb-1 block">auto_awesome_motion</span>
                <h3 class="font-headline text-navy-deep text-sm md:text-base font-bold">Karya Siswa</h3>
                <p class="text-on-surface-variant text-[11px] md:text-xs">Galeri kreativitas matematika</p>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>

    </div>
</section>

{{-- ============ CARA KERJA ============ --}}
<section class="py-14 md:py-20 px-4 md:px-margin-desktop max-w-container-max mx-auto reveal-on-scroll">
    <div class="text-center mb-10 md:mb-14">
        <span class="text-math-teal font-label text-xs uppercase tracking-widest">Mudah Digunakan</span>
        <h2 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep mt-1">Mulai dalam 3 Langkah</h2>
    </div>

    <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-4">
        <div class="hidden md:block absolute top-7 left-[16.5%] right-[16.5%] step-line"></div>

        <div class="relative text-center">
            <div class="w-14 h-14 rounded-2xl bg-navy-deep text-white flex items-center justify-center mx-auto mb-4 font-headline font-bold text-lg shadow-lg shadow-navy-deep/20 relative z-10">1</div>
            <h3 class="font-headline font-bold text-navy-deep mb-1.5">Masuk ke Akun</h3>
            <p class="text-on-surface-variant text-sm max-w-xs mx-auto">Login pakai NIS (siswa) atau email (guru/admin) &mdash; satu pintu untuk semua peran.</p>
        </div>

        <div class="relative text-center">
            <div class="w-14 h-14 rounded-2xl bg-math-teal text-white flex items-center justify-center mx-auto mb-4 font-headline font-bold text-lg shadow-lg shadow-math-teal/20 relative z-10">2</div>
            <h3 class="font-headline font-bold text-navy-deep mb-1.5">Pilih Fitur</h3>
            <p class="text-on-surface-variant text-sm max-w-xs mx-auto">Akses materi, latihan AI, forum, atau unggah karya sesuai kebutuhan belajarmu.</p>
        </div>

        <div class="relative text-center">
            <div class="w-14 h-14 rounded-2xl bg-status-success text-white flex items-center justify-center mx-auto mb-4 font-headline font-bold text-lg shadow-lg shadow-status-success/20 relative z-10">3</div>
            <h3 class="font-headline font-bold text-navy-deep mb-1.5">Pantau Progres</h3>
            <p class="text-on-surface-variant text-sm max-w-xs mx-auto">Cek nilai, kehadiran, dan hasil latihan kapan saja lewat menu Nilai &amp; Kehadiran.</p>
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
</script>
@endpush