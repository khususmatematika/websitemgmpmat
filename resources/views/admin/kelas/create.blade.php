@extends('layouts.dashboard')
@section('title', 'Tambah Kelas')

@section('dashboard-content')
<a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-4">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali ke Data Kelas
</a>

<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Tambah Kelas</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.kelas.store') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-xl">
    @csrf
    <div>
        <label class="text-sm font-medium">Nama Kelas</label>
        <input name="name" required placeholder="mis. XI-A atau XI Mat Lanjut 3" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Jenjang</label>
        <select name="jenjang" id="jenjang-select" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="X">Kelas X</option>
            <option value="XI">Kelas XI</option>
            <option value="XII">Kelas XII</option>
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Fase</label>
        <select name="fase" id="fase-select" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="E">Fase E</option>
            <option value="F">Fase F</option>
            <option value="F+">Fase F+</option>
        </select>
        <p class="text-xs text-on-surface-variant mt-1">
            Fase menentukan materi/topik kurikulum yang otomatis muncul saat guru mengisi Jurnal Mengajar untuk kelas ini.
        </p>
    </div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>

<script>
// Bantu admin: auto-suggest Fase berdasarkan Jenjang yang dipilih (tetap bisa diubah manual)
document.getElementById('jenjang-select').addEventListener('change', function () {
    const faseSelect = document.getElementById('fase-select');
    if (this.value === 'X') faseSelect.value = 'E';
    else faseSelect.value = 'F';
});
</script>
@endsection