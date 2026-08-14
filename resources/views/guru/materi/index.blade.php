@extends('layouts.dashboard')
@section('title', 'Materi Saya')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Materi Saya</h1>
    <a href="{{ route('guru.materi.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Upload Materi</a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse ($materials as $m)
    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex gap-4">
        <div class="w-16 h-20 shrink-0 rounded-md border border-outline-variant/50 overflow-hidden bg-surface-container flex items-center justify-center" id="cover-preview-{{ $m->id }}">
            @if ($m->cover_path)
                <img src="{{ asset('storage/'.$m->cover_path) }}" class="w-full h-full object-cover">
            @else
                <span class="material-symbols-outlined text-error text-2xl">picture_as_pdf</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-navy-deep truncate">{{ $m->title }}</h3>
            <p class="text-xs text-on-surface-variant mb-3">{{ $m->jenjang }} &middot; Semester {{ $m->semester }} &middot; {{ $m->file_size_human }}</p>
            <div class="flex items-center gap-3">
                @if (!$m->cover_path)
                <button type="button" onclick="generateCover({{ $m->id }}, '{{ route('guru.materi.cover.save', $m) }}', '{{ asset('storage/'.$m->file_path) }}')"
                        class="text-math-teal text-xs font-bold" id="gen-btn-{{ $m->id }}">
                    Buat Sampul
                </button>
                @endif
                <form action="{{ route('guru.materi.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                    @csrf @method('DELETE')
                    <button class="text-status-error text-xs font-bold">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada materi yang kamu unggah.</p>
    @endforelse
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

async function generateCover(id, saveUrl, pdfUrl) {
    const btn = document.getElementById('gen-btn-' + id);
    btn.textContent = 'Membuat...';
    btn.disabled = true;

    try {
        const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
        const page = await pdf.getPage(1);
        const viewport = page.getViewport({ scale: 0.6 });

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        const ctx = canvas.getContext('2d');
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

        const coverData = canvas.toDataURL('image/jpeg', 0.7);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const res = await fetch(saveUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ cover_data: coverData }),
        });

        if (res.ok) {
            const data = await res.json();
            document.getElementById('cover-preview-' + id).innerHTML = `<img src="${data.cover_url}" class="w-full h-full object-cover">`;
            btn.remove();
        } else {
            btn.textContent = 'Gagal, coba lagi';
            btn.disabled = false;
        }
    } catch (err) {
        console.error('Gagal membuat sampul:', err);
        btn.textContent = 'Gagal, coba lagi';
        btn.disabled = false;
    }
}
</script>
@endsection