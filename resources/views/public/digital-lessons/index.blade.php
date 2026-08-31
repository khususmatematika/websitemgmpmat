@extends('layouts.app')
@section('title', 'Pembelajaran Digital')

@push('styles')
<style>
    .lesson-card {
        box-shadow: 0px 4px 12px rgba(27, 54, 93, 0.05);
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .lesson-card:hover { transform: translateY(-2px); }
    .lesson-card:active { transform: scale(0.97); }
</style>
@endpush

@section('content')
<main class="px-4 md:px-margin-desktop pt-8 pb-20 max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <div class="mb-6">
        <h1 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep mb-2">Pembelajaran Digital</h1>
        <p class="text-on-surface-variant opacity-80 leading-relaxed max-w-2xl">
            Video, simulasi, dan media interaktif untuk memperdalam pemahaman konsep matematika.
        </p>
    </div>

    <div class="flex flex-wrap gap-2 mb-8">
        @foreach (\App\Support\MathTopics::JENJANG as $key => $label)
        <a href="{{ route('digital-lessons.public', ['jenjang' => $key]) }}"
           class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $jenjang == $key ? 'bg-primary text-white shadow-md' : 'bg-white border border-outline-variant text-on-surface-variant hover:border-primary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($lessons as $l)
        <div class="lesson-card bg-white rounded-xl p-4 flex flex-col items-center text-center border border-outline-variant/30 group">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-4 text-primary group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined !text-4xl">devices</span>
            </div>
            <h3 class="font-headline text-sm font-bold text-navy-deep mb-4 leading-tight line-clamp-2">{{ $l->title }}</h3>
            <div class="mt-auto w-full flex flex-col gap-2">
                <button onclick="document.getElementById('lesson-modal-{{ $l->id }}').classList.remove('hidden')"
                        class="text-primary font-label text-xs flex items-center justify-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-sm">visibility</span>Pratinjau
                </button>
                <button onclick="document.getElementById('lesson-modal-{{ $l->id }}').classList.remove('hidden')"
                        class="w-full py-2 bg-navy-deep text-white font-bold rounded-md font-label text-xs active:scale-95 transition-transform hover:bg-primary">
                    Buka
                </button>
            </div>
        </div>

        <div id="lesson-modal-{{ $l->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div id="lesson-card-{{ $l->id }}" class="bg-white rounded-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-[18px]">devices</span>
                        </span>
                        <h4 class="font-bold text-navy-deep truncate">{{ $l->title }}</h4>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="toggleLessonFullscreen('lesson-modal-{{ $l->id }}')" title="Layar Penuh"
                                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">fullscreen</span>
                        </button>
                        <button onclick="document.getElementById('lesson-modal-{{ $l->id }}').classList.add('hidden')"
                                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                            <span class="material-symbols-outlined text-on-surface-variant">close</span>
                        </button>
                    </div>
                </div>
                <iframe src="{{ $l->embed_url }}" class="flex-1 w-full" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed border-outline-variant">
            <span class="material-symbols-outlined text-outline-variant text-6xl mb-3">devices_off</span>
            <p class="text-on-surface-variant font-medium">Belum ada media pembelajaran digital untuk jenjang ini.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-10 p-6 rounded-2xl bg-primary-container text-white overflow-hidden relative shadow-lg">
        <div class="relative z-10">
            <h4 class="font-headline text-white text-lg font-bold mb-2">Ingin Berlatih Soal?</h4>
            <p class="text-sm opacity-80 mb-4 max-w-md">Coba fitur Latihan Soal AI untuk mengukur pemahamanmu setelah menonton materi ini.</p>
            <a href="{{ route('latihan.create') }}" class="inline-block bg-math-teal text-white px-6 py-2 rounded-full font-bold text-sm hover:brightness-110 transition-all active:scale-95">
                Mulai Latihan
            </a>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-math-teal/20 rounded-full blur-2xl"></div>
    </div>
</main>

<script>
function toggleLessonFullscreen(modalId) {
    const cardId = modalId.replace('lesson-modal-', 'lesson-card-');
    const card = document.getElementById(cardId);

    if (!document.fullscreenElement) {
        card.requestFullscreen?.() || card.webkitRequestFullscreen?.();
        card.classList.remove('rounded-2xl', 'max-w-3xl', 'h-[85vh]');
        card.classList.add('w-screen', 'h-screen');
    } else {
        document.exitFullscreen?.() || document.webkitExitFullscreen?.();
        card.classList.add('rounded-2xl', 'max-w-3xl', 'h-[85vh]');
        card.classList.remove('w-screen', 'h-screen');
    }
}

document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        document.querySelectorAll('[id^="lesson-card-"]').forEach(card => {
            card.classList.add('rounded-2xl', 'max-w-3xl', 'h-[85vh]');
            card.classList.remove('w-screen', 'h-screen');
        });
    }
});
</script>
@endsection