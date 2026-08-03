@extends('layouts.app')
@section('title', 'Pembelajaran Digital')

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
                    <span class="material-symbols-outlined text-math-teal text-2xl">devices</span>
                </div>
                <span class="text-math-teal font-label text-xs uppercase tracking-widest">Media Interaktif</span>
            </div>
            <h1 class="font-headline text-3xl md:text-4xl font-bold text-white mb-2">Pembelajaran Digital</h1>
            <p class="text-white/70 max-w-2xl">Video, simulasi, dan media interaktif untuk memperdalam pemahaman konsep matematika.</p>
        </div>
    </div>
</section>

<section class="py-12 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto -mt-8 relative z-10">
    <div class="bg-white rounded-xl shadow-md border border-outline-variant/30 p-4 mb-8 flex flex-wrap gap-2">
        @foreach (\App\Support\MathTopics::JENJANG as $key => $label)
        <a href="{{ route('digital-lessons.public', ['jenjang' => $key]) }}"
           class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $jenjang == $key ? 'bg-primary text-white shadow-md' : 'bg-surface-container text-on-surface-variant hover:bg-primary/10 hover:text-primary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($lessons as $l)
        <div class="group bg-white rounded-2xl shadow-sm border border-outline-variant/30 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="aspect-video bg-surface-container relative overflow-hidden">
                <iframe src="{{ $l->embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
                <div class="absolute top-3 right-3 bg-navy-deep/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">play_circle</span>
                    Interaktif
                </div>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-navy-deep group-hover:text-primary transition-colors line-clamp-2">{{ $l->title }}</h3>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-2xl border border-dashed border-outline-variant">
            <span class="material-symbols-outlined text-outline-variant text-6xl mb-3">devices_off</span>
            <p class="text-on-surface-variant font-medium">Belum ada media pembelajaran digital untuk jenjang ini.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection