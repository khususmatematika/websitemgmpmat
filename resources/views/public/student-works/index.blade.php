@extends('layouts.app')
@section('title', 'Karya Siswa - SMAN 1 Turen Math Portal')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <span class="text-math-teal font-label text-xs uppercase tracking-widest mb-2 block">Galeri Kreativitas</span>
            <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Karya Siswa</h1>
            <p class="text-on-surface-variant max-w-2xl">Eksplorasi proyek kreatif matematika buatan siswa SMAN 1 Turen.</p>
        </div>
        <a href="{{ route('student-works.create') }}"
           class="flex items-center gap-2 bg-math-teal text-white px-6 py-3 rounded-md font-bold hover:brightness-110 transition-all whitespace-nowrap">
            <span class="material-symbols-outlined">add_a_photo</span>
            Unggah Karya
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($works as $work)
        <div class="group bg-white rounded-xl overflow-hidden shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all cursor-pointer"
             onclick="document.getElementById('work-modal-{{ $work->id }}').classList.remove('hidden')">

            <div class="aspect-square bg-surface-container overflow-hidden relative">
                @if ($work->file_type === 'image')
                    <img src="{{ asset('storage/'.$work->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @elseif ($work->file_type === 'video')
                    <video src="{{ asset('storage/'.$work->file_path) }}" class="w-full h-full object-cover"></video>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                        <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-error text-5xl">picture_as_pdf</span>
                    </div>
                @endif
            </div>

            <div class="p-3">
                <p class="font-bold text-navy-deep text-sm truncate flex items-center gap-1">
                    {{ $work->student_name }}
                    @if ($work->actor_type === 'teacher')
                    <span class="bg-navy-deep text-white text-[9px] px-1.5 py-0.5 rounded-full shrink-0">Guru</span>
                    @endif
                </p>
                <div class="flex items-center gap-3 mt-1 text-on-surface-variant text-xs">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">favorite</span>
                        <span class="grid-like-count-{{ $work->id }}">{{ $work->likes_count }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">chat_bubble</span>
                        {{ $work->comments->count() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div id="work-modal-{{ $work->id }}" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center sticky top-0 bg-white z-10">
                    <div>
                        <p class="font-bold text-navy-deep flex items-center gap-1">
                            {{ $work->student_name }}
                            @if ($work->actor_type === 'teacher')
                            <span class="bg-navy-deep text-white text-[9px] px-1.5 py-0.5 rounded-full">Guru</span>
                            @endif
                        </p>
                        <p class="text-xs text-on-surface-variant">{{ $work->created_at->diffForHumans() }}</p>
                    </div>
                    <button onclick="document.getElementById('work-modal-{{ $work->id }}').classList.add('hidden')"
                            class="material-symbols-outlined text-on-surface-variant">close</button>
                </div>

                <div class="bg-surface-container">
                    @if ($work->file_type === 'image')
                        <img src="{{ asset('storage/'.$work->file_path) }}" class="w-full max-h-[400px] object-contain mx-auto">
                    @elseif ($work->file_type === 'video')
                        <video src="{{ asset('storage/'.$work->file_path) }}" controls class="w-full max-h-[400px] mx-auto"></video>
                    @else
                        <embed src="{{ asset('storage/'.$work->file_path) }}" type="application/pdf" class="w-full h-[400px]">
                    @endif
                </div>

                <div class="p-4 space-y-4">
                    @if ($work->description)
                        <p class="text-sm text-on-surface">{{ $work->description }}</p>
                    @endif

                    <div class="flex items-center gap-4 pt-2 border-t border-outline-variant">
                        <button type="button"
                                class="like-btn flex items-center gap-1 text-sm font-bold {{ $work->isLikedBy($identifier) ? 'text-status-error' : 'text-on-surface-variant' }}"
                                data-url="{{ route('student-works.like', $work) }}"
                                id="like-btn-{{ $work->id }}">
                            <span class="material-symbols-outlined like-icon" style="font-variation-settings: 'FILL' {{ $work->isLikedBy($identifier) ? 1 : 0 }};">favorite</span>
                            <span class="like-count">{{ $work->likes_count }}</span> Suka
                        </button>

                        <button onclick="document.getElementById('report-form-{{ $work->id }}').classList.toggle('hidden')"
                                class="flex items-center gap-1 text-sm text-on-surface-variant hover:text-status-error ml-auto">
                            <span class="material-symbols-outlined text-[18px]">flag</span>
                            Laporkan
                        </button>
                    </div>

                    <div id="report-form-{{ $work->id }}" class="hidden bg-error-container/20 rounded-lg p-4">
                        <form action="{{ route('student-works.report', $work) }}" method="POST" class="space-y-3">
                            @csrf
                            <label class="text-xs font-bold text-status-error">Laporkan karya ini</label>
                            <select name="reason" required class="w-full text-sm rounded-md border-outline-variant">
                                <option value="">Pilih alasan...</option>
                                @foreach ($reasons as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="reporter_name" placeholder="Nama Anda (opsional)" class="w-full text-sm rounded-md border-outline-variant">
                            <button class="bg-status-error text-white text-sm font-bold px-4 py-2 rounded-md">Kirim Laporan</button>
                        </form>
                    </div>

                    {{-- Daftar Komentar --}}
                    <div class="space-y-3 pt-2 border-t border-outline-variant">
                        <p class="text-sm font-bold text-navy-deep">Komentar ({{ $work->comments->count() + $work->comments->sum(fn($c) => $c->replies->count()) }})</p>

                        @forelse ($work->comments as $comment)
                        @include('public.student-works.partials.comment', ['comment' => $comment, 'work' => $work, 'reasons' => $reasons, 'depth' => 0, 'isLoggedIn' => $isLoggedIn])
                        @empty
                        <p class="text-xs text-on-surface-variant">Belum ada komentar.</p>
                        @endforelse
                    </div>

                    {{-- Form Tambah Komentar --}}
                    @if ($isLoggedIn)
                    <form action="{{ route('student-works.comment', $work) }}" method="POST" enctype="multipart/form-data" class="space-y-2 pt-2 border-t border-outline-variant">
                        @csrf
                        <textarea name="content" rows="2" placeholder="Tulis komentar..." class="w-full text-sm rounded-md border-outline-variant"></textarea>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-1 text-xs text-on-surface-variant cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">image</span>
                                <span id="main-file-label-{{ $work->id }}">Tambah Foto</span>
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="updateFileLabel(this, 'main-file-label-{{ $work->id }}')">
                            </label>
                            <button class="ml-auto bg-navy-deep text-white text-sm font-bold px-4 py-2 rounded-md">Kirim Komentar</button>
                        </div>
                    </form>
                    @else
                    <div class="pt-2 border-t border-outline-variant text-center py-4">
                        <p class="text-xs text-on-surface-variant mb-2">Masuk untuk memberi suka dan komentar</p>
                        <a href="{{ route('login.select') }}" class="inline-block bg-math-teal text-white text-xs font-bold px-4 py-2 rounded-md">Masuk</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada karya siswa yang disetujui untuk tampil.</p>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
function updateFileLabel(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files.length > 0 && label) {
        label.textContent = input.files[0].name;
    }
}

document.body.addEventListener('click', async (e) => {
    const btn = e.target.closest('.like-btn');
    if (!btn) return;

    const url = btn.dataset.url;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        if (res.status === 401) {
            const data = await res.json();
            if (confirm(data.message + '\n\nMasuk sekarang?')) {
                window.location.href = data.login_url;
            }
            return;
        }

        const data = await res.json();

        const icon = btn.querySelector('.like-icon');
        const countEl = btn.querySelector('.like-count');

        countEl.textContent = data.likes_count;
        icon.style.fontVariationSettings = `'FILL' ${data.liked ? 1 : 0}`;
        btn.classList.toggle('text-status-error', data.liked);
        btn.classList.toggle('text-on-surface-variant', !data.liked);

        const workId = url.match(/karya-siswa\/(\d+)\/like/)?.[1];
        if (workId) {
            const gridCount = document.querySelector('.grid-like-count-' + workId);
            if (gridCount) gridCount.textContent = data.likes_count;
        }
    } catch (err) {
        console.error('Gagal memproses like:', err);
    }
});
</script>
@endpush
@endsection