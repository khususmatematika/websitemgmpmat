@extends('layouts.dashboard')
@section('title', 'Rekap Nilai Kelas')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Rekap Nilai Kelas</h1>
<p class="text-on-surface-variant">Pilih kelas untuk melihat nilai akhir tiap materi, mengatur bobot, dan mengisi ASAS.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($classes as $c)
    <a href="{{ route('guru.rekap.show', $c) }}"
       class="bg-white rounded-xl border border-outline-variant/30 p-5 hover:shadow-md hover:border-math-teal transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-math-teal/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-math-teal">table_chart</span>
        </div>
        <div>
            <p class="font-bold text-navy-deep">{{ $c->name }}</p>
            <p class="text-xs text-on-surface-variant">{{ $c->students_count }} siswa</p>
        </div>
    </a>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada kelas yang Anda ampu.</p>
    @endforelse
</div>
@endsection