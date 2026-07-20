@extends('layouts.dashboard')
@section('title', 'Isi Jurnal Mengajar')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-1">Isi Jurnal Mengajar</h1>
<p class="text-on-surface-variant mb-6">{{ $teacherClass->schoolClass->name }} &middot; {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('guru.jurnal.store', $teacherClass) }}" class="space-y-6">
    @csrf
    <input type="hidden" name="journal_date" value="{{ $date }}">

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        <div>
            <label class="text-sm font-medium">Materi yang Diajarkan</label>
            <select name="materi" class="mt-1 w-full rounded-md border-outline-variant">
                <option value="">Pilih materi...</option>
                @foreach ($topics as $t)
                    <option value="{{ $t->title }}">{{ $t->title }} ({{ $t->semester }})</option>
                @endforeach
            </select>
            @if ($topics->isEmpty())
                <p class="text-xs text-status-warning mt-1">Belum ada topik kurikulum untuk jenjang kelas ini. Tambahkan lewat menu Admin → Topik Kurikulum.</p>
            @endif
        </div>
        <div>
            <label class="text-sm font-medium">Detail Kegiatan</label>
            <textarea name="kegiatan" rows="3" class="mt-1 w-full rounded-md border-outline-variant"></textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-headline text-lg font-bold text-navy-deep">Kehadiran Siswa</h2>
            <button type="button" onclick="setAllStatus('Hadir')" class="text-xs font-bold text-math-teal">Set Semua Hadir</button>
        </div>

        <div class="space-y-2">
            @forelse ($students as $student)
            <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant/50">
                <span class="text-sm font-medium text-navy-deep">{{ $student->name }}</span>
                <select name="attendance[{{ $student->id }}]" class="attendance-select rounded-md border-outline-variant text-sm">
                    <option value="Hadir" selected>Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>
            @empty
            <p class="text-on-surface-variant text-center py-8 text-sm">Belum ada siswa terdaftar di kelas ini. Tambahkan siswa lewat menu Admin.</p>
            @endforelse
        </div>
    </div>

    <button type="submit" class="bg-math-teal text-white px-8 py-3 rounded-md font-bold">Simpan Jurnal</button>
</form>

<script>
function setAllStatus(status) {
    document.querySelectorAll('.attendance-select').forEach(el => el.value = status);
}
</script>
@endsection