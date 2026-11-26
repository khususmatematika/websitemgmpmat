@extends('layouts.dashboard')
@section('title', 'Nilai Semester Lalu')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Nilai Semester Lalu</h1>
<p class="text-on-surface-variant">Isi nilai akhir semester sebelumnya agar siswa bisa melihat ASAS minimal untuk menyamai nilai tersebut. Kelas pilihan (Mat Lanjut) dimulai dari semester 3.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($classes as $c)
    @php
        $prev = $c->prev_semesters;
        $prevCount = count($prev);
    @endphp
    <a href="{{ route('guru.nilai-lalu.show', $c) }}"
       class="bg-white rounded-xl border border-outline-variant/30 p-5 hover:shadow-md hover:border-math-teal transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-math-teal/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-math-teal">history</span>
        </div>
        <div class="min-w-0">
            <p class="font-bold text-navy-deep">
                {{ $c->name }}
                @if ($c->is_advanced)
                    <span class="ml-1 text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full">Lanjut</span>
                @endif
            </p>
            <p class="text-xs text-on-surface-variant">
                {{ $c->students_count }} siswa &middot;
                @if (!$c->current_semester)
                    semester belum terdeteksi
                @elseif ($prevCount > 0)
                    isi semester {{ $prev[0] }}{{ $prevCount > 1 ? '-' . $prev[$prevCount - 1] : '' }}
                @else
                    semester {{ $c->current_semester }}, belum ada nilai lalu
                @endif
            </p>
        </div>
    </a>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada kelas yang Anda ampu.</p>
    @endforelse
</div>
@endsection