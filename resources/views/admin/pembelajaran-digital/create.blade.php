@extends('layouts.dashboard')
@section('title', 'Tambah Pembelajaran Digital')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Tambah Media Pembelajaran Digital</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.pembelajaran-digital.store') }}"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-2xl">
    @csrf

    <div>
        <label class="text-sm font-medium">Judul Media</label>
        <input name="title" value="{{ old('title') }}" placeholder="mis. Video Trigonometri Interaktif"
               class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">Jenjang</label>
        <select name="jenjang" class="mt-1 w-full rounded-md border-outline-variant">
            @foreach ($jenjangList as $key => $label)
                <option value="{{ $key }}" {{ old('jenjang') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-sm font-medium">Topik</label>
        <select name="topic" class="mt-1 w-full rounded-md border-outline-variant">
            @foreach ($topics as $t)
                <option value="{{ $t }}" {{ old('topic') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-sm font-medium">URL Embed</label>
        <input type="url" name="embed_url" value="{{ old('embed_url') }}"
               placeholder="https://www.youtube.com/embed/xxxx atau link GeoGebra/Wordwall"
               class="mt-1 w-full rounded-md border-outline-variant">
        <p class="text-xs text-on-surface-variant mt-1">
            Untuk YouTube, gunakan link format <span class="font-mono">/embed/</span> (bukan link tonton biasa) agar bisa tampil di iframe.
        </p>
    </div>

    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>
@endsection