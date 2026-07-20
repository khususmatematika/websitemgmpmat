@extends('layouts.dashboard')
@section('title', 'Upload Materi')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Upload Materi Baru</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('guru.materi.store') }}" enctype="multipart/form-data"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="text-sm font-medium">Judul Materi</label><input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div>
        <label class="text-sm font-medium">Jenjang</label>
        <select name="jenjang" class="mt-1 w-full rounded-md border-outline-variant">
            @foreach ($jenjangList as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
        </select>
    </div>
    <div>
    <label class="text-sm font-medium">Semester</label>
    <select name="semester" class="mt-1 w-full rounded-md border-outline-variant">
        <option value="Ganjil">Semester Ganjil</option>
        <option value="Genap">Semester Genap</option>
    </select>
</div>
    
    <div><label class="text-sm font-medium">File PDF (maks 50MB)</label><input type="file" name="file" accept="application/pdf" class="mt-1 w-full"></div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Upload</button>
</form>
@endsection