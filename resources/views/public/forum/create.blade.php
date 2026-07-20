@extends('layouts.app')
@section('title', 'Buat Postingan Forum')

@section('content')
<div class="max-w-xl mx-auto py-16 px-margin-mobile">
    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Buat Postingan Baru</h1>
    <p class="text-on-surface-variant mb-8">Postingan langsung tayang ke publik. Gunakan bahasa yang sopan.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('forum.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        @csrf

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Nama Kamu</label>
            <input type="text" name="author_name" id="author_name" required
                   value="{{ old('author_name') }}"
                   class="mt-1 w-full rounded-md border-outline-variant focus:ring-secondary focus:border-secondary">
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Isi Postingan</label>
            <textarea name="content" rows="5" required
                      class="mt-1 w-full rounded-md border-outline-variant focus:ring-secondary focus:border-secondary">{{ old('content') }}</textarea>
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm">
            <p class="text-xs text-on-surface-variant mt-1">Format JPG/PNG, maks 5MB.</p>
        </div>

        <button class="w-full bg-secondary text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
            Posting
        </button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('author_name');
    const saved = localStorage.getItem('student_display_name');
    if (saved && !nameInput.value) nameInput.value = saved;
    document.querySelector('form').addEventListener('submit', () => {
        localStorage.setItem('student_display_name', nameInput.value);
    });
});
</script>
@endpush
@endsection