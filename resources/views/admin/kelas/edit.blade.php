@extends('layouts.dashboard')
@section('title', 'Edit Kelas')

@section('dashboard-content')
<a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-4">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali ke Data Kelas
</a>

<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Edit Kelas</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.kelas.update', $kelas) }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-xl">
    @csrf
    @method('PUT')

    <div>
        <label class="text-sm font-medium">Nama Kelas</label>
        <input name="name" required value="{{ old('name', $kelas->name) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">Jenjang</label>
        <select name="jenjang" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="X" {{ old('jenjang', $kelas->jenjang) == 'X' ? 'selected' : '' }}>Kelas X</option>
            <option value="XI" {{ old('jenjang', $kelas->jenjang) == 'XI' ? 'selected' : '' }}>Kelas XI</option>
            <option value="XII" {{ old('jenjang', $kelas->jenjang) == 'XII' ? 'selected' : '' }}>Kelas XII</option>
        </select>
    </div>

    <div>
        <label class="text-sm font-medium">Fase</label>
        <select name="fase" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="E" {{ old('fase', $kelas->fase) == 'E' ? 'selected' : '' }}>Fase E</option>
            <option value="F" {{ old('fase', $kelas->fase) == 'F' ? 'selected' : '' }}>Fase F</option>
            <option value="F+" {{ old('fase', $kelas->fase) == 'F+' ? 'selected' : '' }}>Fase F+</option>
        </select>
        <p class="text-xs text-on-surface-variant mt-1">
            Fase menentukan materi/topik kurikulum yang otomatis muncul saat guru mengisi Jurnal Mengajar untuk kelas ini.
        </p>
    </div>

    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan Perubahan</button>
</form>
@endsection