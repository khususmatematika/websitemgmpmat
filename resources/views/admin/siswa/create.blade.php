@extends('layouts.dashboard')
@section('title', 'Tambah Siswa')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Tambah Siswa</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.siswa.store') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-xl">
    @csrf
    <div>
        <label class="text-sm font-medium">NIS (opsional)</label>
        <input name="nis" value="{{ old('nis') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Nama Lengkap</label>
        <input name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Kelas (maks 2 — reguler & pilihan)</label>
        <select name="class_ids[]" multiple size="6" class="mt-1 w-full rounded-md border-outline-variant">
            @foreach ($classes as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <p class="text-xs text-on-surface-variant mt-1">Tahan Ctrl (Windows) untuk pilih lebih dari satu kelas.</p>
    </div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>
@endsection