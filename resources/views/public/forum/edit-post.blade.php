@extends('layouts.app')
@section('title', 'Edit Postingan')

@section('content')
<div class="max-w-xl mx-auto py-16 px-margin-mobile">
    <a href="{{ route('forum.public') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Forum
    </a>

    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Edit Postingan</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('forum.posts.update', $post) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Isi Postingan</label>
            <textarea name="content" rows="5" required
                      class="mt-1 w-full rounded-md border-outline-variant focus:ring-secondary focus:border-secondary">{{ old('content', $post->content) }}</textarea>
        </div>

        @if ($post->image_path)
        <div>
            <label class="text-sm font-medium text-on-surface-variant block mb-2">Gambar Saat Ini</label>
            <img src="{{ asset('storage/'.$post->image_path) }}" class="rounded-lg max-h-48 mb-2">
            <label class="flex items-center gap-2 text-sm text-status-error cursor-pointer">
                <input type="checkbox" name="remove_image" value="1" class="rounded border-outline-variant">
                Hapus gambar ini
            </label>
        </div>
        @endif

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Ganti Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm">
        </div>

        <div class="flex gap-3">
            <button class="flex-1 bg-secondary text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
                Simpan Perubahan
            </button>
            <a href="{{ route('forum.public') }}" class="px-6 py-3 border border-outline-variant rounded-md font-bold text-sm text-on-surface-variant hover:bg-surface-container">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection