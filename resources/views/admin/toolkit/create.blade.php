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
        <label class="text-sm font-medium block mb-2">Sumber Toolkit</label>
        <div class="flex gap-3 mb-4">
            <label class="flex-1 flex items-center gap-2 border border-outline-variant rounded-md p-3 cursor-pointer has-[:checked]:border-math-teal has-[:checked]:bg-math-teal/5">
                <input type="radio" name="input_type" value="url" id="type-url" onchange="toggleInputType()" {{ old('input_type', 'url') == 'url' ? 'checked' : '' }} class="text-math-teal">
                <span class="text-sm font-medium">Gunakan URL</span>
            </label>
            <label class="flex-1 flex items-center gap-2 border border-outline-variant rounded-md p-3 cursor-pointer has-[:checked]:border-math-teal has-[:checked]:bg-math-teal/5">
                <input type="radio" name="input_type" value="code" id="type-code" onchange="toggleInputType()" {{ old('input_type') == 'code' ? 'checked' : '' }} class="text-math-teal">
                <span class="text-sm font-medium">Tempel Kode Embed</span>
            </label>
        </div>

        <div id="field-url" class="{{ old('input_type', 'url') == 'code' ? 'hidden' : '' }}">
            <label class="text-sm font-medium">URL</label>
            <input type="url" name="embed_url" placeholder="https://www.desmos.com/calculator" value="{{ old('embed_url') }}" class="mt-1 w-full rounded-md border-outline-variant">
            <p class="text-xs text-on-surface-variant mt-1">Link akan dimasukkan ke dalam iframe otomatis.</p>
        </div>

        <div id="field-code" class="{{ old('input_type') != 'code' ? 'hidden' : '' }}">
            <label class="text-sm font-medium">Kode Embed (HTML)</label>
            <textarea name="embed_code" rows="6" placeholder="&lt;iframe src=&quot;...&quot;&gt;&lt;/iframe&gt; atau &lt;div&gt;...&lt;/div&gt;&lt;script&gt;...&lt;/script&gt;" class="mt-1 w-full rounded-md border-outline-variant font-mono text-xs">{{ old('embed_code') }}</textarea>
            <p class="text-xs text-on-surface-variant mt-1">Tempel kode HTML/iframe/script lengkap dari penyedia widget (mis. GeoGebra, Desmos, Wordwall).</p>
        </div>
    </div>

    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>

<script>
function toggleInputType() {
    const isUrl = document.getElementById('type-url').checked;
    document.getElementById('field-url').classList.toggle('hidden', !isUrl);
    document.getElementById('field-code').classList.toggle('hidden', isUrl);
}
</script>
@endsection