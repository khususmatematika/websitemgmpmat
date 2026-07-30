@extends('layouts.app')
@section('title', 'Toolkit Matematika')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <span class="text-math-teal font-label text-xs uppercase tracking-widest mb-2 block">Alat Bantu</span>
    <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Toolkit Matematika</h1>
    <p class="text-on-surface-variant mb-8 max-w-2xl">Kalkulator, grafik fungsi, dan alat bantu interaktif lainnya untuk membantu proses belajarmu.</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
        @forelse ($toolkits as $t)
        <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.remove('hidden')"
                class="group bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg hover:border-math-teal transition-all text-center">
            <div class="w-14 h-14 rounded-xl bg-math-teal/10 flex items-center justify-center mx-auto mb-3 group-hover:bg-math-teal group-hover:scale-110 transition-all">
                <span class="material-symbols-outlined text-math-teal text-2xl group-hover:text-white transition-colors">{{ $t->icon }}</span>
            </div>
            <h3 class="font-label text-navy-deep text-sm font-bold">{{ $t->title }}</h3>
        </button>

        <div id="toolkit-modal-{{ $t->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-math-teal">{{ $t->icon }}</span>
                        <h4 class="font-bold text-navy-deep">{{ $t->title }}</h4>
                    </div>
                    <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.add('hidden')"
                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container">
                        <span class="material-symbols-outlined text-on-surface-variant">close</span>
                    </button>
                </div>
                <iframe src="{{ $t->embed_url }}" class="flex-1 w-full" frameborder="0"></iframe>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-xl border border-outline-variant/30">
            <span class="material-symbols-outlined text-outline-variant text-5xl mb-3">calculate</span>
            <p class="text-on-surface-variant">Belum ada toolkit tersedia.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection