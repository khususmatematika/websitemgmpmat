@extends('layouts.dashboard')
@section('title', 'Pembelajaran Digital Saya')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Pembelajaran Digital Saya</h1>
    <a href="{{ route('guru.pembelajaran-digital.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">
        + Tambah Media
    </a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse ($lessons as $l)
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="aspect-video bg-surface-container">
            <iframe src="{{ $l->embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="p-4">
            <h3 class="font-bold text-navy-deep">{{ $l->title }}</h3>
            <p class="text-xs text-on-surface-variant mb-3">{{ $l->jenjang }} &middot; {{ $l->topic }}</p>
            <form action="{{ route('admin.pembelajaran-digital.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">
                @csrf @method('DELETE')
                <button class="text-status-error text-sm font-bold">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada media pembelajaran digital yang kamu tambahkan.</p>
    @endforelse
</div>
@endsection