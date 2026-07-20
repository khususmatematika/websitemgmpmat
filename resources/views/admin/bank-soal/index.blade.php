@extends('layouts.dashboard')
@section('title', 'Bank Soal Saya')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Bank Soal Saya</h1>
    <a href="{{ route('guru.bank-soal.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Upload Soal</a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse ($files as $f)
    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30">
        <h3 class="font-bold text-navy-deep">{{ $f->title }}</h3>
        <p class="text-xs text-on-surface-variant mb-3">{{ $f->jenjang }} &middot; {{ $f->topic }}</p>
        <form action="{{ route('admin.bank-soal.destroy', $f) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
            @csrf @method('DELETE')
            <button class="text-status-error text-sm font-bold">Hapus</button>
        </form>
    </div>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada soal yang kamu unggah.</p>
    @endforelse
</div>
@endsection