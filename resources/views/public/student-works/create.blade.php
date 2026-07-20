@extends('layouts.app')
@section('title', 'Unggah Karya')

@section('content')
<div class="max-w-xl mx-auto py-16 px-margin-mobile">
    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Unggah Karya Kamu</h1>
    <p class="text-on-surface-variant mb-8">Karya akan tayang setelah disetujui Admin. Nama akan diingat otomatis di browser ini untuk unggahan berikutnya.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('student-works.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        @csrf

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Nama Kamu</label>
            <input type="text" name="student_name" id="student_name" required
                   value="{{ old('student_name') }}"
                   class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
        </div>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('student_name');
    const saved = localStorage.getItem('student_display_name');
    if (saved && !nameInput.value) nameInput.value = saved;
    document.querySelector('form').addEventListener('submit', () => {
        localStorage.setItem('student_display_name', nameInput.value);
    });
});
</script>
@endpush
@endsection