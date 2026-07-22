@extends('layouts.dashboard')
@section('title', 'Generator Modul Ajar AI')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <div>
        <h1 class="font-headline text-2xl font-bold text-navy-deep">Generator Modul Ajar AI</h1>
        <p class="text-xs text-on-surface-variant mt-1">Sisa kuota generate AI hari ini: <strong>{{ $remaining }} dari 5</strong> (gabungan seluruh fitur AI guru)</p>
    </div>
    <a href="{{ route('guru.modul-ajar.create') }}" @if($remaining <= 0) class="bg-surface-container text-on-surface-variant px-4 py-2 rounded-md font-bold text-sm cursor-not-allowed pointer-events-none" @else class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm hover:brightness-110" @endif>
        + Buat Modul Ajar
    </a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse ($modules as $m)
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden hover:shadow-md transition-shadow">
        <div class="h-1.5 {{ $m->status === 'completed' ? 'bg-status-success' : ($m->status === 'failed' ? 'bg-status-error' : 'bg-status-warning') }}"></div>
        <div class="p-5">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-bold text-navy-deep pr-2">{{ $m->materi }}</h3>
                @if ($m->status === 'completed')
                    <span class="text-xs font-bold text-status-success bg-status-success/10 px-2 py-1 rounded-full whitespace-nowrap">Selesai</span>
                @elseif ($m->status === 'failed')
                    <span class="text-xs font-bold text-status-error bg-error-container px-2 py-1 rounded-full whitespace-nowrap">Gagal</span>
                @else
                    <span class="text-xs font-bold text-status-warning bg-status-warning/10 px-2 py-1 rounded-full whitespace-nowrap">Diproses</span>
                @endif
            </div>
            <p class="text-xs text-on-surface-variant mb-4">{{ $m->kelas }} &middot; {{ $m->meetings_count }} Pertemuan &middot; {{ $m->created_at->format('d M Y') }}</p>
            <div class="flex gap-4 pt-3 border-t border-outline-variant">
                <a href="{{ route('guru.modul-ajar.show', $m) }}" class="flex items-center gap-1 text-math-teal text-sm font-bold">
                    <span class="material-symbols-outlined text-[16px]">visibility</span> Lihat Detail
                </a>
                <form action="{{ route('guru.modul-ajar.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus modul ajar ini?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-1 text-status-error text-sm font-bold">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada Modul Ajar yang dibuat.</p>
    @endforelse
</div>
@endsection