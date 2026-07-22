@extends('layouts.dashboard')
@section('title', 'Dashboard Guru')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

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
        @foreach ($attendanceByClass as $i => $att)
        <div class="text-center">
            <div class="relative w-28 h-28 mx-auto">
                <canvas id="chart-{{ $i }}"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-navy-deep">
                        {{ $att['persentase'] !== null ? $att['persentase'] . '%' : '-' }}
                    </span>
                </div>
            </div>
            <p class="text-sm font-bold text-navy-deep mt-2">{{ $att['name'] }}</p>
            <p class="text-[11px] text-on-surface-variant">{{ $att['hadir'] + $att['sakit'] + $att['izin'] + $att['alpa'] }} data tercatat</p>
        </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json($attendanceByClass);

    data.forEach((att, i) => {
        const ctx = document.getElementById('chart-' + i);
        if (!ctx) return;

        const total = att.hadir + att.sakit + att.izin + att.alpa;
        const values = total > 0 ? [att.hadir, att.sakit, att.izin, att.alpa] : [1, 0, 0, 0];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
                datasets: [{
                    data: values,
                    backgroundColor: total > 0
                        ? ['#20B2AA', '#FFB703', '#3B82F6', '#D00000']
                        : ['#e3e2e6'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { enabled: total > 0 } },
            }
        });
    });
});
</script>
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