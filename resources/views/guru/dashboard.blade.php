@extends('layouts.dashboard')
@section('title', 'Dashboard Guru')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Selamat datang, {{ auth('guru')->user()->name }}</h1>
<p class="text-on-surface-variant">Ringkasan kelas dan siswa yang kamu ajar.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mt-6">
    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-math-teal/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-math-teal text-2xl">groups</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-navy-deep">{{ number_format($totalStudents) }}</p>
            <p class="text-xs text-on-surface-variant">Siswa yang Diajar</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-navy-deep/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-navy-deep text-2xl">meeting_room</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-navy-deep">{{ number_format($totalClasses) }}</p>
            <p class="text-xs text-on-surface-variant">Kelas yang Diajar</p>
        </div>
    </div>
</div>

@if ($attendanceByClass->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 mt-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-1">Persentase Kehadiran per Kelas</h2>
    <p class="text-xs text-on-surface-variant mb-6">Bulan {{ $currentMonthLabel }}</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @foreach ($attendanceByClass as $att)
        @php
            $total = $att['hadir'] + $att['sakit'] + $att['izin'] + $att['alpa'];
            $pct = $att['persentase'] ?? 0;
            $radius = 45;
            $circumference = 2 * M_PI * $radius;
            $offset = $circumference * (1 - $pct / 100);
        @endphp
        <div class="text-center">
            <div class="relative w-28 h-28 mx-auto">
                <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#e3e2e6" stroke-width="10"></circle>
                    @if ($total > 0)
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none"
                            stroke="{{ $pct >= 90 ? '#2D6A4F' : ($pct >= 75 ? '#FFB703' : '#D00000') }}"
                            stroke-width="10"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round"></circle>
                    @endif
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-navy-deep">
                        {{ $total > 0 ? $pct . '%' : '-' }}
                    </span>
                </div>
            </div>
            <p class="text-sm font-bold text-navy-deep mt-2">{{ $att['name'] }}</p>
            <p class="text-[11px] text-on-surface-variant">{{ $total }} data tercatat</p>
        </div>
        @endforeach
    </div>

    <div class="flex items-center justify-center gap-6 mt-6 pt-4 border-t border-outline-variant text-xs text-on-surface-variant">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-status-success inline-block"></span> ≥90% Baik</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-status-warning inline-block"></span> 75-89% Cukup</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-status-error inline-block"></span> &lt;75% Perlu Perhatian</span>
    </div>
</div>
@endif

@if ($classes->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 mt-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Rincian Kelas</h2>
    <div class="space-y-2">
        @foreach ($classes as $c)
        <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant/50">
            <span class="font-medium text-navy-deep text-sm">{{ $c->name }}</span>
            <span class="text-xs text-on-surface-variant">{{ $c->students_count }} siswa</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection