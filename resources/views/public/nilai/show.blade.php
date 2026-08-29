@extends('layouts.app')
@section('title', 'Nilai dan Kehadiran Saya')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-3xl mx-auto">

    <div class="hero-gradient rounded-2xl p-6 md:p-7 mb-6 relative overflow-hidden">
        <div class="absolute inset-0 math-pattern opacity-10"></div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-math-teal/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm shrink-0">
                    <span class="material-symbols-outlined text-math-teal text-2xl">school</span>
                </div>
                <div>
                    <h1 class="font-headline text-lg md:text-xl font-bold text-white">{{ $student->name }}</h1>
                    <p class="text-white/60 text-xs md:text-sm mt-0.5">NIS: {{ $student->nis }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('nilai.password.edit') }}"
                class="flex items-center gap-1.5 bg-white/10 border border-white/20 text-white px-4 py-2 rounded-full text-xs md:text-sm font-bold hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-[16px] md:text-[18px]">lock</span>
                    <span class="hidden sm:inline">Ganti Password</span>
                </a>
                <form method="POST" action="{{ route('nilai.logout') }}">
                    @csrf
                    <button class="flex items-center gap-1.5 bg-status-error/90 text-white px-4 py-2 rounded-full text-xs md:text-sm font-bold hover:brightness-110 transition-colors">
                        <span class="material-symbols-outlined text-[16px] md:text-[18px]">logout</span>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if (session('status'))
    <div class="mb-6 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif

    @if ($overallByClass->count() > 0)
    <div class="grid grid-cols-1 {{ $overallByClass->count() > 1 ? 'sm:grid-cols-2' : '' }} gap-3 mb-8">
        @foreach ($overallByClass as $item)
        @php
            $avg = $item['average'];
            $badgeColor = $avg >= 85 ? 'text-status-success' : ($avg >= 70 ? 'text-status-warning' : 'text-status-error');
            $bgColor = $avg >= 85 ? 'bg-status-success/10' : ($avg >= 70 ? 'bg-status-warning/10' : 'bg-error-container');
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant/30 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl {{ $bgColor }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined {{ $badgeColor }} text-xl">emoji_events</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide">Nilai Akhir &middot; {{ $item['class'] }}</p>
                    <p class="text-[11px] text-on-surface-variant mt-0.5">Rata-rata dari {{ $item['topic_count'] }} materi</p>
                </div>
            </div>
            <div class="text-2xl md:text-3xl font-bold {{ $badgeColor }} shrink-0 ml-3">{{ $avg }}</div>
        </div>
        @endforeach
    </div>
    @endif

    @if (count($attendanceSummary) > 0)
        <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Rekap Kehadiran</h2>
        @if (count($attendanceSummary) > 1)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        @else
        <div class="grid grid-cols-1 gap-4 mb-8">
        @endif
         @foreach ($attendanceSummary as $att)
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="font-bold text-navy-deep text-sm">{{ $att['class'] }}</p>
                @if ($att['percentage'] !== null)
                <span class="text-lg font-bold {{ $att['percentage'] >= 90 ? 'text-status-success' : ($att['percentage'] >= 75 ? 'text-status-warning' : 'text-status-error') }}">
                    {{ $att['percentage'] }}%
                </span>
                @endif
            </div>
            <div class="grid grid-cols-4 gap-2 text-center mb-3">
                <div>
                    <p class="text-sm font-bold text-status-success">{{ $att['hadir'] }}</p>
                    <p class="text-[10px] text-on-surface-variant">Hadir</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-status-warning">{{ $att['sakit'] }}</p>
                    <p class="text-[10px] text-on-surface-variant">Sakit</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-blue-600">{{ $att['izin'] }}</p>
                    <p class="text-[10px] text-on-surface-variant">Izin</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-status-error">{{ $att['alpa'] }}</p>
                    <p class="text-[10px] text-on-surface-variant">Alpa</p>
                </div>
            </div>

            @if (count($att['meetings']) > 0)
            <button type="button" onclick="document.getElementById('meetings-{{ $att['class_id'] }}').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                    class="w-full flex items-center justify-center gap-1 text-xs font-bold text-math-teal border-t border-outline-variant pt-3">
                Lihat Detail per Pertemuan
                <span class="material-symbols-outlined text-[16px] chevron transition-transform">expand_more</span>
            </button>
            @endif
        </div>

        @if (count($att['meetings']) > 0)
        <div id="meetings-{{ $att['class_id'] }}" class="hidden border-t border-outline-variant max-h-64 overflow-y-auto">
            @foreach ($att['meetings'] as $m)
            @php
                $statusColor = match($m['status']) {
                    'Hadir' => 'text-status-success bg-status-success/10',
                    'Sakit' => 'text-status-warning bg-status-warning/10',
                    'Izin' => 'text-blue-600 bg-blue-50',
                    'Alpa' => 'text-status-error bg-error-container',
                    default => 'text-on-surface-variant bg-surface-container',
                };
            @endphp
            <div class="flex items-center justify-between px-5 py-2.5 border-b border-outline-variant last:border-b-0 text-xs">
                <div class="min-w-0">
                    <p class="font-medium text-navy-deep">{{ $m['date'] }}</p>
                    @if ($m['materi'])
                    <p class="text-on-surface-variant truncate">{{ $m['materi'] }}</p>
                    @endif
                </div>
                <span class="shrink-0 px-2 py-1 rounded-full font-bold {{ $statusColor }}">{{ $m['status'] }}</span>
            </div>
            @endforeach
        </div>
        @endif
        </div>
        @endforeach
        </div>
    @endif

    @if (session('status'))
    <div class="mb-6 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-6">
        @forelse ($results as $r)
        @php
            
                $final = $r['final'];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="text-xs font-bold text-math-teal uppercase tracking-wide">{{ $r['class'] }}</span>
                            <h2 class="font-headline text-lg font-bold text-navy-deep">{{ $r['topic'] }}</h2>
                        </div>
                        <div class="text-center px-4 py-2 rounded-lg bg-blue-50 shrink-0">
                            <p class="text-[10px] font-bold uppercase text-blue-600">Nilai Akhir</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $final ?? '-' }}</p>
                        </div>
                    </div>

                {{-- Rincian komponen --}}
                <div class="space-y-1 mb-5">
                    @foreach ($r['components'] as $c)
                    <div class="flex items-center justify-between text-sm py-2 border-t border-outline-variant first:border-t-0">
                        <span class="text-on-surface-variant">{{ $c['name'] }} <span class="text-xs">({{ $c['weight'] }}%)</span></span>
                        <span class="font-bold text-navy-deep">{{ $c['score'] ?? '-' }}</span>
                    </div>
                    @endforeach

                    
                </div>

                {{-- Statistik Kelas --}}
                @if ($r['statistics']['count'] > 0)
                <div class="bg-surface-container-low rounded-lg p-4">
                    <p class="text-xs font-bold text-navy-deep uppercase tracking-wide mb-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                        Statistik Kelas ({{ $r['statistics']['count'] }} siswa)
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <p class="text-lg font-bold text-navy-deep">{{ $r['statistics']['average'] }}</p>
                            <p class="text-[10px] text-on-surface-variant uppercase">Rata-rata</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-status-success">{{ $r['statistics']['highest'] }}</p>
                            <p class="text-[10px] text-on-surface-variant uppercase">Tertinggi</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-status-error">{{ $r['statistics']['lowest'] }}</p>
                            <p class="text-[10px] text-on-surface-variant uppercase">Terendah</p>
                        </div>
                    </div>

                    @if ($final !== null && $r['statistics']['average'] !== null)
                    <div class="mt-3 pt-3 border-t border-outline-variant/50">
                        @php $diff = $final - $r['statistics']['average']; @endphp
                        <p class="text-xs {{ $diff >= 0 ? 'text-status-success' : 'text-status-error' }} font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">{{ $diff >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            Nilaimu {{ $diff >= 0 ? number_format($diff, 2) . ' poin di atas' : number_format(abs($diff), 2) . ' poin di bawah' }} rata-rata kelas
                        </p>
                    </div>
                    @endif

                    @if ($r['is_class_table_published'])
                    <a href="{{ route('nilai.class-table', [$r['class_id'], $r['topic_id']]) }}"
                    class="mt-3 flex items-center justify-center gap-2 bg-math-teal/10 text-math-teal py-2 rounded-md text-sm font-bold hover:bg-math-teal/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">groups</span>
                        Lihat Nilai Seluruh Kelas
                    </a>
                    @endif

                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-outline-variant/30 p-12 text-center">
            <span class="material-symbols-outlined text-outline-variant text-5xl mb-3">grade</span>
            <p class="text-on-surface-variant">Belum ada nilai yang diinput untuk kamu.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection