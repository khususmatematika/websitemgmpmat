@extends('layouts.dashboard')
@section('title', 'Tambah Toolkit')

@section('dashboard-content')
<a href="{{ route('admin.toolkit.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-4">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali ke Toolkit
</a>

<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Tambah Toolkit</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.toolkit.store') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-xl">
    @csrf
    <div>
        <label class="text-sm font-medium">Judul Toolkit</label>
        <input name="title" required placeholder="mis. Kalkulator Scientific" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <div>
        <label class="text-sm font-medium">Icon (Material Symbols)</label>
        <input name="icon" required placeholder="mis. calculate, functions, bar_chart" value="{{ old('icon', 'calculate') }}" class="mt-1 w-full rounded-md border-outline-variant">
        <p class="text-xs text-on-surface-variant mt-1">Cari nama icon di <a href="https://fonts.google.com/icons" target="_blank" class="text-math-teal underline">fonts.google.com/icons</a></p>
    </div>
    <div>
        <label class="text-sm font-medium">URL Embed</label>
        <input type="url" name="embed_url" required placeholder="https://www.desmos.com/calculator" value="{{ old('embed_url') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>
@endsection