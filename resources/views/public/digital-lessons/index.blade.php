@extends('layouts.app')
@section('title', 'Pembelajaran Digital')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Pembelajaran Digital</h1>
    <p class="text-on-surface-variant mb-8">Media interaktif: video, GeoGebra, Wordwall, dan sejenisnya.</p>

    <div class="flex flex-wrap gap-2 mb-8">
        @foreach (\App\Support\MathTopics::JENJANG as $key => $label)
        <a href="{{ route('digital-lessons.public', ['jenjang' => $key]) }}"
           class="px-4 py-2 rounded-full text-sm font-medium {{ $jenjang == $key ? 'bg-math-teal text-white' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($lessons as $l)
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
            <div class="aspect-video">
                <iframe src="{{ $l->embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="p-4">
                <h3 class="font-bold text-navy-deep">{{ $l->title }}</h3>
                <p class="text-xs text-on-surface-variant">{{ $l->topic }}</p>
            </div>
        </div>
        @empty
        <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada media pembelajaran digital untuk jenjang ini.</p>
        @endforelse
    </div>
</section>
@endsection