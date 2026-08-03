@extends('layouts.app')
@section('title', 'Toolkit Matematika')

@section('content')
<section class="relative overflow-hidden">
    <div class="hero-gradient py-16 px-margin-mobile md:px-margin-desktop relative">
        <div class="absolute inset-0 math-pattern opacity-10"></div>
        <div class="relative z-10 max-w-container-max mx-auto">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-white/70 hover:text-white mb-6">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Beranda
            </a>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-math-teal text-2xl">calculate</span>
                </div>
                <span class="text-math-teal font-label text-xs uppercase tracking-widest">Alat Bantu</span>
            </div>
            <h1 class="font-headline text-3xl md:text-4xl font-bold text-white mb-2">Toolkit Matematika</h1>
            <p class="text-white/70 max-w-2xl">Kalkulator, grafik fungsi, dan alat bantu interaktif untuk membantu proses belajarmu.</p>
        </div>
    </div>
</section>

<section class="py-12 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto -mt-8 relative z-10">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
        @forelse ($toolkits as $t)
        <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.remove('hidden')"
                class="group relative bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center overflow-hidden">
            <div class="absolute -top-6 -right-6 w-20 h-20 bg-math-teal/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-math-teal/10 flex items-center justify-center mx-auto mb-3 group-hover:bg-math-teal transition-all duration-300">
                    <span class="material-symbols-outlined text-math-teal text-2xl group-hover:text-white transition-colors">{{ $t->icon }}</span>
                </div>
                <h3 class="font-label text-navy-deep text-sm font-bold">{{ $t->title }}</h3>
                <p class="text-[10px] text-on-surface-variant mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Klik untuk buka</p>
            </div>
        </button>

        <div id="toolkit-modal-{{ $t->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-math-teal/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-math-teal text-[18px]">{{ $t->icon }}</span>
                        </span>
                        <h4 class="font-bold text-navy-deep">{{ $t->title }}</h4>
                    </div>
                   <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
    <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-lg bg-math-teal/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-math-teal text-[18px]">{{ $t->icon }}</span>
        </span>
        <h4 class="font-bold text-navy-deep">{{ $t->title }}</h4>
    </div>
    <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.add('hidden')"
            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
        <span class="material-symbols-outlined text-on-surface-variant">close</span>
    </button>
</div>
                </div>
                <div class="flex-1 w-full overflow-auto">
    @if ($t->input_type === 'code')
        {!! $t->embed_code !!}
    @else
        <iframe src="{{ $t->embed_url }}" class="w-full h-full" frameborder="0"></iframe>
    @endif
</div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-2xl border border-dashed border-outline-variant">
            <span class="material-symbols-outlined text-outline-variant text-6xl mb-3">calculate</span>
            <p class="text-on-surface-variant font-medium">Belum ada toolkit tersedia.</p>
        </div>
        @endforelse
    </div>
</section>

@endsection
