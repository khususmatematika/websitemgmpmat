@extends('layouts.app')
@section('title', 'Leaderboard Latihan')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-2xl mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Leaderboard Latihan</h1>
    <p class="text-on-surface-variant mb-6">Skor tertinggi per materi.</p>

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($jenjangList as $key => $label)
        <a href="{{ route('leaderboard.public', ['jenjang' => $key, 'topic' => $topic]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium {{ $jenjang == $key ? 'bg-math-teal text-white' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('leaderboard.public', ['jenjang' => $jenjang]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium {{ !$topic ? 'bg-navy-deep text-white' : 'bg-surface-container text-on-surface-variant' }}">
            Semua Topik
        </a>
        @foreach ($topics as $t)
        <a href="{{ route('leaderboard.public', ['jenjang' => $jenjang, 'topic' => $t]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium {{ $topic == $t ? 'bg-navy-deep text-white' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $t }}
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        @forelse ($entries as $i => $entry)
        <div class="flex items-center gap-4 p-4 {{ $i < count($entries) - 1 ? 'border-b border-outline-variant' : '' }}">
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                         {{ $i === 0 ? 'bg-status-warning text-white' : ($i === 1 ? 'bg-outline-variant text-navy-deep' : ($i === 2 ? 'bg-tertiary-container/50 text-navy-deep' : 'bg-surface-container text-on-surface-variant')) }}">
                {{ $i + 1 }}
            </span>
            <div class="flex-1">
                <p class="font-bold text-navy-deep text-sm">{{ $entry->student_name }}</p>
                <p class="text-xs text-on-surface-variant">{{ $entry->topic }}</p>
            </div>
            <span class="font-bold text-math-teal">{{ $entry->score }}</span>
        </div>
        @empty
        <p class="text-on-surface-variant text-center py-12">Belum ada yang mengikuti latihan untuk kombinasi ini.</p>
        @endforelse
    </div>
</section>
@endsection