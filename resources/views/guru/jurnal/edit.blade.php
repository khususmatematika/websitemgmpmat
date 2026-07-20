@extends('layouts.dashboard')
@section('title', 'Edit Jurnal Mengajar')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-1">Edit Jurnal Mengajar</h1>
<p class="text-on-surface-variant mb-6">{{ $journal->schoolClass->name }} &middot; {{ $journal->journal_date->translatedFormat('l, d F Y') }}</p>

@if (session('status'))
<div class="mb-4 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('guru.jurnal.update', $journal) }}" class="space-y-6">
    @csrf @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        <div>
            <label class="text-sm font-medium">Materi yang Diajarkan</label>
            <select name="materi" class="mt-1 w-full rounded-md border-outline-variant">
                <option value="">Pilih materi...</option>
                @foreach ($topics as $t)
                    <option value="{{ $t->title }}" {{ $journal->materi === $t->title ? 'selected' : '' }}>{{ $t->title }} ({{ $t->semester }})</option>
                @endforeach
                @if ($journal->materi && !$topics->pluck('title')->contains($journal->materi))
                    <option value="{{ $journal->materi }}" selected>{{ $journal->materi }} (materi lama/manual)</option>
                @endif
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Detail Kegiatan</label>
            <textarea name="kegiatan" rows="3" class="mt-1 w-full rounded-md border-outline-variant">{{ $journal->kegiatan }}</textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
        <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Kehadiran Siswa</h2>
        <div class="space-y-2">
            @forelse ($journal->attendances as $att)
            <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant/50">
                <span class="text-sm font-medium text-navy-deep">{{ $att->student->name }}</span>
                <select name="attendance[{{ $att->student_id }}]" class="rounded-md border-outline-variant text-sm">
                    @foreach (['Hadir', 'Sakit', 'Izin', 'Alpa'] as $status)
                        <option value="{{ $status }}" {{ $att->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            @empty
            <p class="text-on-surface-variant text-center py-8 text-sm">Belum ada data kehadiran.</p>
            @endforelse
        </div>
    </div>

    <button type="submit" class="bg-math-teal text-white px-8 py-3 rounded-md font-bold">Simpan Perubahan</button>
</form>
@endsection