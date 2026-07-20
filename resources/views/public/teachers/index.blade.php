@extends('layouts.app')
@section('title', 'Profil Guru - SMAN 1 Turen Math Portal')

@push('styles')
<style>
    .perspective { perspective: 1000px; }
    .preserve-3d { transform-style: preserve-3d; }
    .backface-hidden { backface-visibility: hidden; }
    .rotate-y-180 { transform: rotateY(180deg); }
    .teacher-card:hover .card-inner { transform: rotateY(180deg); }

    /* Kunci perbaikan: atur pointer-events sesuai sisi yang aktif */
    .card-front { pointer-events: auto; }
    .card-back { pointer-events: none; }
    .teacher-card:hover .card-front { pointer-events: none; }
    .teacher-card:hover .card-back { pointer-events: auto; }
</style>
@endpush

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <span class="text-math-teal font-label text-xs uppercase tracking-widest mb-2 block">Our Faculty</span>
    <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Profil Guru Matematika</h1>
    <p class="text-on-surface-variant max-w-2xl mb-10">Tim guru yang membimbing siswa SMAN 1 Turen dalam dunia logika, angka, dan pemecahan masalah.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-gutter">
        @forelse ($teachers as $teacher)
        <a href="{{ $teacher->whatsapp_link ?? '#' }}" target="_blank"
           class="teacher-card perspective h-[420px] w-full cursor-pointer block">
            <div class="card-inner relative w-full h-full transition-transform duration-700 preserve-3d">

                {{-- Front --}}
                <div class="card-front backface-hidden absolute inset-0 bg-white rounded-xl shadow-[0px_4px_12px_rgba(27,54,93,0.05)] overflow-hidden flex flex-col group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $teacher->photo ? asset('storage/'.$teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&size=256&background=1b365d&color=fff' }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-navy-deep/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="bg-white/90 p-3 rounded-full shadow-lg">
                                <span class="material-symbols-outlined text-math-teal">chat</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-stack-md flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-headline text-navy-deep text-xl group-hover:text-math-teal transition-colors">{{ $teacher->name }}</h3>
                            <p class="text-on-surface-variant font-label text-xs flex items-center gap-1.5 mt-1 min-w-0">
                                <span class="material-symbols-outlined text-[16px] shrink-0">mail</span>
                                <span class="truncate" title="{{ $teacher->email }}">{{ $teacher->email }}</span>
                            </p>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-outline-variant">
                            @if ($teacher->title)
                                <span class="px-2 py-1 bg-math-teal/10 text-math-teal rounded font-label text-xs">{{ $teacher->title }}</span>
                            @else
                                <span></span>
                            @endif
                            <span class="material-symbols-outlined text-math-teal">chat</span>
                        </div>
                    </div>
                </div>

                {{-- Back --}}
                <div class="card-back backface-hidden absolute inset-0 bg-navy-deep rounded-xl shadow-xl rotate-y-180 p-stack-lg flex flex-col text-white">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="font-headline text-xl">Jadwal Kelas</h3>
                        <span class="material-symbols-outlined text-math-teal">event_note</span>
                    </div>
                    <div class="space-y-4 flex-1">
                        <div class="space-y-1">
                            <p class="font-label text-xs text-white/60 uppercase">Kelas Diampu</p>
                            @forelse ($teacher->classes as $class)
                                <p class="text-sm">{{ $class->name }}
                                    @if($class->pivot->day)
                                        — {{ $class->pivot->day }} {{ $class->pivot->start_time }}-{{ $class->pivot->end_time }}
                                    @endif
                                </p>
                            @empty
                                <p class="text-sm text-white/60">Belum ada jadwal.</p>
                            @endforelse
                        </div>
                        @if ($teacher->description)
                        <div class="pt-4 mt-auto">
                            <div class="flex items-center gap-3 p-3 bg-white/10 rounded-lg">
                                <span class="material-symbols-outlined text-math-teal">info</span>
                                <p class="text-sm">{{ $teacher->description }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="pt-2 flex items-center gap-2 text-math-teal text-sm font-bold">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                            Klik untuk hubungi via WhatsApp
                        </div>
                    </div>
                </div>

            </div>
        </a>
        @empty
        <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada data guru.</p>
        @endforelse
    </div>
</section>
@endsection