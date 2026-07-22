@extends('layouts.app')
@section('title', 'Beranda - SMAN 1 Turen Math Portal')

@section('content')
<section class="hero-gradient relative flex flex-col items-center px-4 md:px-margin-desktop pt-10 pb-8 md:pt-16 md:pb-10 overflow-hidden">
    <div class="absolute inset-0 math-pattern opacity-10"></div>

    <div class="relative z-10 max-w-4xl w-full text-center space-y-4 md:space-y-5">
        <h1 class="font-headline text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight">
            Portal Matematika <br><span class="text-math-teal">SMAN 1 Turen</span>
        </h1>
        <p class="text-white/80 text-sm md:text-base max-w-2xl mx-auto">
            Pusat digitalisasi pembelajaran, galeri karya siswa, dan sumber daya akademik terintegrasi.
        </p>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 pt-2">
            <a href="{{ route('materials.public') }}"
               class="flex items-center justify-center gap-2 bg-math-teal text-white px-6 py-3 rounded-md font-bold text-sm hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-math-teal/20">
                <span class="material-symbols-outlined text-[20px]">school</span>
                Jelajahi Pembelajaran
            </a>

            <a href="{{ route('bank-soal.public') }}"
   class="flex items-center justify-center gap-2 border-2 border-white/30 text-white px-6 py-3 rounded-md font-bold text-sm hover:bg-white/10 active:scale-95 transition-all">
    <span class="material-symbols-outlined text-[20px]">quiz</span>
    Bank Soal
</a>
        </div>
    </div>

    {{-- Stats Ribbon — normal flow, TIDAK absolute, jadi tidak akan overlap --}}
    <div class="relative z-10 w-full mt-8 md:mt-10 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl py-4 px-4">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 text-white">

            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-math-teal text-xl">calendar_today</span>
                <div class="flex flex-col leading-tight text-center sm:text-left">
                    <span class="font-label text-white text-xs" id="real-time-date">--</span>
                    <span class="font-label text-math-teal text-sm" id="real-time-clock">--:--:--</span>
                </div>
            </div>

            <div class="h-8 w-px bg-white/20 hidden sm:block"></div>

            <div class="flex items-center gap-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-math-teal text-xl">visibility</span>
                    <div class="flex flex-col leading-tight">
                        <span class="font-label text-[10px] text-white/60">HARI INI</span>
                        <span class="font-label text-white text-sm font-bold">{{ number_format($visitorToday) }}</span>
                    </div>
                </div>
                <div class="h-6 w-px bg-white/20"></div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-math-teal text-xl">group</span>
                    <div class="flex flex-col leading-tight">
                        <span class="font-label text-[10px] text-white/60">TOTAL</span>
                        <span class="font-label text-white text-sm font-bold">{{ number_format($visitorTotal) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="py-24 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <h2 class="font-headline text-2xl font-bold text-navy-deep mb-8">Akses Navigasi Utama</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-6">

        <a href="{{ route('materials.public') }}" class="md:col-span-2 md:row-span-2 group bg-white rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-xl transition-all p-8">
            <span class="material-symbols-outlined text-math-teal text-4xl mb-4">book</span>
            <h3 class="font-headline text-xl text-navy-deep group-hover:text-math-teal">Materi</h3>
            <p class="text-on-surface-variant mt-2">Modul pembelajaran matematika kelas X, XI, XII Kurikulum Merdeka.</p>
        </a>

        <a href="{{ route('digital-lessons.public') }}" class="md:col-span-2 bg-white p-8 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">devices</span>
            <h3 class="font-headline text-navy-deep">Pembelajaran Digital</h3>
        </a>

        <a href="{{ route('student-works.public') }}" class="md:col-span-2 group bg-white p-8 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
            <span class="material-symbols-outlined text-status-warning text-3xl mb-2">auto_awesome_motion</span>
            <h3 class="font-headline text-navy-deep group-hover:text-status-warning transition-colors">Karya Siswa</h3>
            <p class="text-on-surface-variant text-xs mt-2">Lihat & unggah karya kreatif matematika siswa.</p>
        </a>

        <a href="{{ route('forum.public') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all text-center">
            <span class="material-symbols-outlined text-secondary text-4xl mb-3">forum</span>
            <h3 class="font-label text-navy-deep">Forum</h3>
        </a>

        <a href="{{ route('toolkits.public') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all text-center">
            <span class="material-symbols-outlined text-math-teal text-4xl mb-3">calculate</span>
            <h3 class="font-label text-navy-deep">Toolkit</h3>
        </a>

        <a href="{{ route('bank-soal.public') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all text-center">
    <span class="material-symbols-outlined text-status-error text-4xl mb-3">quiz</span>
    <h3 class="font-label text-navy-deep">Bank Soal</h3>
</a>

        <a href="{{ route('latihan.create') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all text-center">
            <span class="material-symbols-outlined text-status-success text-4xl mb-3">history_edu</span>
            <h3 class="font-label text-navy-deep">Latihan</h3>
        </a>

    </div>

    <div class="absolute inset-0 math-pattern opacity-10"></div>

    {{-- Simbol matematika mengambang --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 8%; top: 15%; font-size: 3rem; animation-delay: 0s;">∑</span>
        <span class="floating-symbol absolute text-math-teal/20 font-bold" style="left: 85%; top: 20%; font-size: 2.5rem; animation-delay: 1.5s;">π</span>
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 15%; top: 65%; font-size: 2rem; animation-delay: 3s;">√</span>
        <span class="floating-symbol absolute text-math-teal/15 font-bold" style="left: 75%; top: 60%; font-size: 3.5rem; animation-delay: 0.8s;">∞</span>
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 45%; top: 10%; font-size: 2rem; animation-delay: 2.2s;">∫</span>
        <span class="floating-symbol absolute text-math-teal/10 font-bold hidden md:inline" style="left: 92%; top: 45%; font-size: 2rem; animation-delay: 1s;">Δ</span>
        <span class="floating-symbol absolute text-white/10 font-bold hidden md:inline" style="left: 3%; top: 40%; font-size: 2.2rem; animation-delay: 2.8s;">θ</span>
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
</script>
@endpush