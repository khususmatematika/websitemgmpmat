@extends('layouts.app')
@section('title', 'Toolkit Matematika')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Toolkit Matematika</h1>
    <p class="text-on-surface-variant mb-8">Kalkulator, grafik fungsi, dan alat bantu lainnya.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6" x-data="{ open: false, activeUrl: '' }">
        @forelse ($toolkits as $t)
        <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.remove('hidden')"
                class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all text-center">
            <span class="material-symbols-outlined text-math-teal text-4xl mb-3">{{ $t->icon }}</span>
            <h3 class="font-label text-navy-deep text-sm">{{ $t->title }}</h3>
        </button>

        <div id="toolkit-modal-{{ $t->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-3xl h-[80vh] flex flex-col overflow-hidden">
                <div class="p-4 border-b border-outline-variant flex justify-between items-center">
                    <h4 class="font-bold text-navy-deep">{{ $t->title }}</h4>
                    <button onclick="document.getElementById('toolkit-modal-{{ $t->id }}').classList.add('hidden')"
                            class="material-symbols-outlined text-on-surface-variant">close</button>
                </div>
                <iframe src="{{ $t->embed_url }}" class="flex-1 w-full" frameborder="0"></iframe>
            </div>
        </div>
        @empty
        <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada toolkit tersedia.</p>
        @endforelse
    </div>
</section>
@endsection