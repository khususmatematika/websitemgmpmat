@extends('layouts.app')
@section('title', 'Math Toolkit')

@push('styles')
<style>
    .toolkit-card {
        box-shadow: 0px 4px 12px rgba(27, 54, 93, 0.05);
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .toolkit-card:hover { transform: translateY(-2px); }
    .toolkit-card:active { transform: scale(0.97); }
</style>
@endpush

@section('content')
<main class="px-4 md:px-margin-desktop pt-8 pb-20 max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <div class="mb-8">
        <h1 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep mb-2">Math Toolkit</h1>
        <p class="text-on-surface-variant opacity-80 leading-relaxed max-w-2xl">
            Alat bantu matematika untuk memudahkan eksplorasi konsep &mdash; kalkulator, grafik fungsi, dan lainnya.
        </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($toolkits as $t)
        <div class="toolkit-card bg-white rounded-xl p-4 flex flex-col items-center text-center border border-outline-variant/30 group">
            <div class="w-16 h-16 rounded-full bg-math-teal/10 flex items-center justify-center mb-4 text-math-teal group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined !text-4xl">{{ $t->icon }}</span>
            </div>
            <h3 class="font-headline text-sm font-bold text-navy-deep mb-4 leading-tight line-clamp-2">{{ $t->title }}</h3>
            <div class="mt-auto w-full flex flex-col gap-2">
                <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.remove('hidden')"
                        class="text-math-teal font-label text-xs flex items-center justify-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-sm">visibility</span>Pratinjau
                </button>
                <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.remove('hidden')"
                        class="w-full py-2 bg-navy-deep text-white font-bold rounded-md font-label text-xs active:scale-95 transition-transform hover:bg-math-teal">
                    Buka
                </button>
            </div>
        </div>

        <div id="toolkit-modal-{{ $t->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div id="toolkit-card-{{ $t->id }}" class="bg-white rounded-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-math-teal/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-math-teal text-[18px]">{{ $t->icon }}</span>
                        </span>
                        <h4 class="font-bold text-navy-deep truncate">{{ $t->title }}</h4>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="toggleFullscreen('toolkit-modal-{{ $t->id }}')" title="Layar Penuh"
                                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">fullscreen</span>
                        </button>
                        <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.add('hidden')"
                                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                            <span class="material-symbols-outlined text-on-surface-variant">close</span>
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full overflow-auto" id="toolkit-frame-{{ $t->id }}">
                    @if ($t->input_type === 'code')
                        {!! $t->embed_code !!}
                    @else
                        <iframe src="{{ $t->embed_url }}" class="w-full h-full" frameborder="0"></iframe>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed border-outline-variant">
            <span class="material-symbols-outlined text-outline-variant text-6xl mb-3">calculate</span>
            <p class="text-on-surface-variant font-medium">Belum ada toolkit tersedia.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-10 p-6 rounded-2xl bg-primary-container text-white overflow-hidden relative shadow-lg">
        <div class="relative z-10">
            <h4 class="font-headline text-white text-lg font-bold mb-2">Butuh Bantuan AI?</h4>
            <p class="text-sm opacity-80 mb-4 max-w-md">Gunakan Bank Soal & Latihan AI untuk membuat latihan soal matematika secara instan.</p>
            <a href="{{ route('bank-soal.public') }}" class="inline-block bg-math-teal text-white px-6 py-2 rounded-full font-bold text-sm hover:brightness-110 transition-all active:scale-95">
                Coba Sekarang
            </a>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-math-teal/20 rounded-full blur-2xl"></div>
    </div>
</main>

<script>
function toggleFullscreen(modalId) {
    const cardId = modalId.replace('toolkit-modal-', 'toolkit-card-');
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
        document.querySelectorAll('[id^="toolkit-card-"]').forEach(card => {
            card.classList.add('rounded-2xl', 'max-w-3xl', 'h-[85vh]');
            card.classList.remove('w-screen', 'h-screen');
        });
    }
});
</script>
@endsection