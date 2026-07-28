@extends('layouts.dashboard')
@section('title', 'Input Nilai')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Input Nilai</h1>
<p class="text-on-surface-variant">Pilih kelas dan materi untuk mengelola penilaian.</p>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 max-w-xl">
    <form method="GET" action="{{ route('guru.nilai.manage') }}" class="space-y-4">
        <div>
            <label class="text-sm font-medium">Kelas</label>
            <select name="class_id" onchange="location.href='{{ route('guru.nilai.index') }}?class_id=' + this.value"
                    class="mt-1 w-full rounded-md border-outline-variant">
                <option value="">Pilih kelas...</option>
                @foreach ($myClasses as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        @if ($selectedClassId)
        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
        <div>
            <label class="text-sm font-medium">Materi</label>
            <select name="material_topic_id" required class="mt-1 w-full rounded-md border-outline-variant">
                <option value="">Pilih materi...</option>
                @foreach ($topics as $t)
                    <option value="{{ $t->id }}">{{ $t->title }} ({{ $t->semester }})</option>
                @endforeach
            </select>
            @if ($topics->isEmpty())
                <p class="text-xs text-status-warning mt-1">Belum ada topik untuk kelas ini.</p>
            @endif
        </div>
        <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Kelola Nilai</button>
        @endif
    </form>
</div>
@endsection