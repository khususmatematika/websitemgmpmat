@extends('layouts.dashboard')
@section('title', 'Jurnal Mengajar')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Jurnal Mengajar</h1>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-1">Jadwal Hari Ini</h2>
    <p class="text-xs text-on-surface-variant mb-4">{{ \Carbon\Carbon::parse($todayDate)->translatedFormat('l, d F Y') }}</p>

    <div class="space-y-3">
        @forelse ($todaySchedules as $sch)
        @php $isFilled = in_array($sch->class_id, $filledClassIds); @endphp
        <a href="{{ route('guru.jurnal.create', $sch) }}"
           class="flex items-center justify-between p-4 rounded-lg border transition-all
                  {{ $isFilled ? 'border-status-success/40 bg-status-success/5' : 'border-outline-variant hover:border-math-teal' }}">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-navy-deep/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-navy-deep">schedule</span>
                </div>
                <div>
                    <p class="font-bold text-navy-deep text-sm">{{ $sch->schoolClass->name }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $sch->start_time }} - {{ $sch->end_time }}</p>
                </div>
            </div>
            @if ($isFilled)
                <span class="flex items-center gap-1 text-xs font-bold text-status-success bg-status-success/10 px-3 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Sudah diisi
                </span>
            @else
                <span class="text-xs font-bold text-math-teal">Isi Jurnal →</span>
            @endif
        </a>
        @empty
        <p class="text-on-surface-variant text-center py-8 text-sm">Tidak ada jadwal mengajar hari ini.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <div class="flex items-center justify-between mb-4">
    <h2 class="font-headline text-lg font-bold text-navy-deep">Rekap Jurnal</h2>
    <div class="flex gap-2">
        @if ($filterClass)
        <a href="{{ route('guru.jurnal.print', ['class_id' => $filterClass, 'month' => $filterMonth]) }}" target="_blank"
           class="flex items-center gap-2 bg-navy-deep text-white px-4 py-2 rounded-md font-bold text-xs hover:bg-math-teal transition-colors">
            <span class="material-symbols-outlined text-[16px]">print</span>
            Cetak Kelas Ini
        </a>
        @endif
        <a href="{{ route('guru.jurnal.print', ['month' => $filterMonth]) }}" target="_blank"
           class="flex items-center gap-2 bg-math-teal text-white px-4 py-2 rounded-md font-bold text-xs hover:brightness-110 transition-colors">
            <span class="material-symbols-outlined text-[16px]">library_books</span>
            Cetak Semua Kelas
        </a>
    </div>
</div>

    <form method="GET" class="flex flex-col md:flex-row gap-3 mb-4">
        <select name="class_id" onchange="this.form.submit()" class="rounded-md border-outline-variant text-sm flex-1">
            <option value="">Semua Kelas</option>
            @foreach ($myClasses as $c)
                <option value="{{ $c->id }}" {{ $filterClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <input type="month" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()"
               class="rounded-md border-outline-variant text-sm">
    </form>


    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Kelas</th>
                    <th class="p-3 text-left">Materi</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($journals as $j)
                <tr class="border-t border-outline-variant">
                    <td class="p-3">{{ $j->journal_date->format('d M Y') }}</td>
                    <td class="p-3">{{ $j->schoolClass->name }}</td>
                    <td class="p-3">{{ $j->materi ?? '-' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('guru.jurnal.edit', $j) }}" class="text-math-teal font-bold">Lihat/Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-8 text-center text-on-surface-variant">Belum ada jurnal untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection