@extends('layouts.app')
@section('title', 'Nilai Saya')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-3xl mx-auto">

    <div class="bg-gradient-to-r hero-gradient rounded-xl p-6 mb-8 relative overflow-hidden">
        <div class="absolute inset-0 math-pattern opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/10 border border-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-math-teal text-2xl">school</span>
                </div>
                <div>
                    <h1 class="font-headline text-xl font-bold text-black">{{ $student->name }}</h1>
                    <p class="text-black/70 text-sm">NIS: {{ $student->nis }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('nilai.password.edit') }}"
                   class="flex items-center gap-1 bg-white/10 border border-white/20 text-black px-4 py-2 rounded-md text-sm font-bold hover:bg-white/20">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    Ganti Password
                </a>
                <form method="POST" action="{{ route('nilai.logout') }}">
                    @csrf
                    <button class="flex items-center gap-1 bg-status-error/90 text-white px-4 py-2 rounded-md text-sm font-bold hover:brightness-110">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if (session('status'))
    <div class="mb-6 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-6">
        @forelse ($results as $r)
        @php
            $final = $r['final'];
            $badgeColor = $final === null ? 'bg-surface-container text-on-surface-variant'
                : ($final >= 85 ? 'bg-status-success/10 text-status-success'
                : ($final >= 70 ? 'bg-status-warning/10 text-status-warning'
                : 'bg-error-container text-status-error'));
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
            <div class="p-6 pb-4">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <span class="text-xs font-bold text-math-teal uppercase tracking-wide">{{ $r['class'] }}</span>
                        <h2 class="font-headline text-lg font-bold text-navy-deep">{{ $r['topic'] }}</h2>
                    </div>
                    <div class="text-center px-4 py-2 rounded-lg {{ $badgeColor }} shrink-0">
                        <p class="text-[10px] font-bold uppercase">Nilai Akhir</p>
                        <p class="text-2xl font-bold">{{ $final ?? '-' }}</p>
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