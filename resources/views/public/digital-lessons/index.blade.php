@extends('layouts.app')
@section('title', 'Pembelajaran Digital')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <span class="text-primary font-label text-xs uppercase tracking-widest mb-2 block">Media Interaktif</span>
    <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Pembelajaran Digital</h1>
    <p class="text-on-surface-variant mb-8 max-w-2xl">Video, simulasi, dan media interaktif untuk memperdalam pemahaman konsep matematika.</p>

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
        <div class="group bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden hover:shadow-lg transition-all">
            <div class="aspect-video bg-surface-container relative overflow-hidden">
                <iframe src="{{ $l->embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-primary text-[18px]">devices</span>
                    </span>
                    <h3 class="font-bold text-navy-deep group-hover:text-primary transition-colors line-clamp-2">{{ $l->title }}</h3>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-xl border border-outline-variant/30">
            <span class="material-symbols-outlined text-outline-variant text-5xl mb-3">devices_off</span>
            <p class="text-on-surface-variant">Belum ada media pembelajaran digital untuk jenjang ini.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection