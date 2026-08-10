@extends('layouts.app')
@section('title', 'Unggah Karya')

@section('content')
<div class="max-w-xl mx-auto py-16 px-margin-mobile">
    <a href="{{ route('student-works.public') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Karya Siswa
    </a>

    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Unggah Karya Kamu</h1>
    <p class="text-on-surface-variant mb-8">Karya akan tayang setelah disetujui Admin.</p>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-4 mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-math-teal/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-math-teal">person</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant">Diunggah sebagai</p>
            <p class="font-bold text-navy-deep text-sm">{{ $actor['name'] }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('student-works.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        @csrf

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Deskripsi Singkat (opsional)</label>
            <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-outline-variant">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">File Karya</label>
            <input type="file" name="file" required accept=".jpg,.jpeg,.png,.mp4,.pdf" class="mt-1 w-full text-sm">
            <p class="text-xs text-on-surface-variant mt-1">Format: JPG, PNG, MP4, atau PDF. Maks 20MB.</p>
        </div>

        <button class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
            Unggah Karya
        </button>
    </form>
</div>
@endsection