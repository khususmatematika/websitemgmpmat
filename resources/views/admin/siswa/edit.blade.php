@extends('layouts.dashboard')
@section('title', 'Edit Siswa')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Edit Siswa</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.siswa.update', $student) }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-xl">
    @csrf @method('PUT')
    <div>
        <label class="text-sm font-medium">NIS (opsional)</label>
        <input name="nis" value="{{ old('nis', $student->nis) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Nama Lengkap</label>
        <input name="name" required value="{{ old('name', $student->name) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Kelas (maks 2 — reguler & pilihan)</label>
        @php $currentClassIds = $student->classes->pluck('id')->toArray(); @endphp
        <select name="class_ids[]" multiple size="6" class="mt-1 w-full rounded-md border-outline-variant">
            @foreach ($classes as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, $currentClassIds) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan Perubahan</button>
</form>
@endsection