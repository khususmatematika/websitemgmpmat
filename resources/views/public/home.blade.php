@extends('layouts.app')
@section('title', 'Beranda - SMAN 1 Turen Math Portal')

@section('content')
<section class="hero-gradient relative flex flex-col items-center px-4 md:px-margin-desktop pt-10 pb-8 md:pt-16 md:pb-10 overflow-hidden">
    <div class="absolute inset-0 math-pattern opacity-10"></div>

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 8%; top: 15%; font-size: 3rem; animation-delay: 0s;">∑</span>
        <span class="floating-symbol absolute text-math-teal/20 font-bold" style="left: 85%; top: 20%; font-size: 2.5rem; animation-delay: 1.5s;">π</span>
        <span class="floating-symbol absolute text-white/10 font-bold" style="left: 15%; top: 65%; font-size: 2rem; animation-delay: 3s;">√</span>
        <span class="floating-symbol absolute text-math-teal/15 font-bold" style="left: 75%; top: 60%; font-size: 3.5rem; animation-delay: 0.8s;">∞</span>
        <span class="floating-symbol absolute text-white/10 font-bold hidden md:inline" style="left: 45%; top: 10%; font-size: 2rem; animation-delay: 2.2s;">∫</span>
    </div>

    <div class="relative z-10 max-w-4xl w-full text-center space-y-4 md:space-y-5">
        <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto backdrop-blur-sm mb-2">
            <span class="material-symbols-outlined text-math-teal text-3xl md:text-4xl">functions</span>
        </div>
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

{{-- Fitur Utama: showcase card, swipeable di mobile, grid di desktop --}}
<section class="py-12 md:py-16 px-4 md:px-margin-desktop max-w-container-max mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="text-math-teal font-label text-xs uppercase tracking-widest">Jelajahi</span>
            <h2 class="font-headline text-xl md:text-2xl font-bold text-navy-deep">Fitur Utama</h2>
        </div>
        <span class="text-xs text-on-surface-variant hidden md:inline">Geser untuk lihat semua &rarr;</span>
    </div>

    @php
        $features = [
            ['url' => route('materials.public'), 'icon' => 'book', 'label' => 'Materi', 'desc' => 'Modul pembelajaran lengkap X-XII', 'color' => 'math-teal'],
            ['url' => route('digital-lessons.public'), 'icon' => 'devices', 'label' => 'Pembelajaran Digital', 'desc' => 'Video & simulasi interaktif', 'color' => 'primary'],
            ['url' => route('student-works.public'), 'icon' => 'auto_awesome_motion', 'label' => 'Karya Siswa', 'desc' => 'Galeri kreativitas siswa', 'color' => 'status-warning'],
            ['url' => route('forum.public'), 'icon' => 'forum', 'label' => 'Forum', 'desc' => 'Diskusi & tanya jawab', 'color' => 'secondary'],
            ['url' => route('toolkits.public'), 'icon' => 'calculate', 'label' => 'Toolkit', 'desc' => 'Kalkulator & alat bantu', 'color' => 'math-teal'],
            ['url' => route('bank-soal.public'), 'icon' => 'quiz', 'label' => 'Bank Soal', 'desc' => 'Soal latihan siap pakai', 'color' => 'status-error'],
            ['url' => route('latihan.create'), 'icon' => 'history_edu', 'label' => 'Latihan AI', 'desc' => '10 soal adaptif per sesi', 'color' => 'status-success'],
        ];
    @endphp

    <div class="flex md:grid md:grid-cols-4 gap-4 overflow-x-auto md:overflow-visible pb-4 md:pb-0 snap-x snap-mandatory -mx-4 px-4 md:mx-0 md:px-0">
        @foreach ($features as $f)
        <a href="{{ $f['url'] }}"
           class="group shrink-0 w-[68%] xs:w-60 md:w-auto snap-start bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col">
            <div class="w-14 h-14 rounded-2xl bg-{{ $f['color'] }}/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-{{ $f['color'] }} text-2xl">{{ $f['icon'] }}</span>
            </div>
            <h3 class="font-headline text-navy-deep font-bold mb-1">{{ $f['label'] }}</h3>
            <p class="text-on-surface-variant text-xs mb-4 flex-1">{{ $f['desc'] }}</p>
            <span class="flex items-center gap-1 text-{{ $f['color'] }} text-xs font-bold group-hover:gap-2 transition-all">
                Buka <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </span>
        </a>
        @endforeach
    </div>
</section>

{{-- Section highlight kedua: Nilai & Kehadiran, ajakan login siswa --}}
<section class="py-12 px-4 md:px-margin-desktop max-w-container-max mx-auto">
    <div class="bg-navy-deep rounded-2xl p-8 md:p-10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-math-teal/20 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <span class="text-math-teal font-label text-xs uppercase tracking-widest">Untuk Siswa</span>
                <h3 class="font-headline text-xl md:text-2xl font-bold text-white mt-1 mb-2">Cek Nilai dan Kehadiranmu</h3>
                <p class="text-white/70 text-sm max-w-md">Masuk dengan NIS dan password untuk melihat rekap nilai serta kehadiran di setiap kelas.</p>
            </div>
            <a href="{{ route('nilai.login') }}"
               class="flex items-center gap-2 bg-math-teal text-white px-6 py-3 rounded-md font-bold text-sm hover:brightness-110 active:scale-95 transition-all whitespace-nowrap">
                <span class="material-symbols-outlined text-[20px]">grade</span>
                Cek Sekarang
            </a>
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
</script>
@endpush