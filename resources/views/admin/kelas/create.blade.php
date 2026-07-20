@extends('layouts.dashboard')
@section('title', 'Tambah Kelas')

@section('dashboard-content')
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
        <select name="jenjang" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="X">Kelas X</option>
            <option value="XI">Kelas XI</option>
            <option value="XII">Kelas XII</option>
        </select>
    </div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>
@endsection