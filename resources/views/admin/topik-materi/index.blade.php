@extends('layouts.dashboard')
@section('title', 'Topik Kurikulum')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Topik Kurikulum per Kelas & Semester</h1>
<p class="text-on-surface-variant">Daftar ini akan tampil di halaman publik Materi sebagai outline kurikulum, di bawah kelas yang dipilih.</p>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

{{-- Pemilih Kelas & Semester --}}
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-4 flex flex-col md:flex-row gap-3">
    <form method="GET" action="{{ route('admin.topik-materi.index') }}" class="flex flex-col md:flex-row gap-3 flex-1" id="filter-form">
        <select name="jenjang" onchange="document.getElementById('filter-form').submit()" class="rounded-md border-outline-variant text-sm flex-1">
            @foreach ($jenjangList as $key => $label)
                <option value="{{ $key }}" {{ $jenjang == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="semester" onchange="document.getElementById('filter-form').submit()" class="rounded-md border-outline-variant text-sm flex-1">
            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
        </select>
    </form>
</div>

{{-- Form Tambah Topik --}}
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Tambah Topik Baru</h2>
    <form method="POST" action="{{ route('admin.topik-materi.store') }}" class="flex flex-col md:flex-row gap-3">
        @csrf
        <input type="hidden" name="jenjang" value="{{ $jenjang }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="text" name="title" required placeholder="mis. Bilangan Berpangkat"
               class="flex-1 rounded-md border-outline-variant text-sm">
        <button class="bg-math-teal text-white px-6 py-2 rounded-md font-bold text-sm whitespace-nowrap">+ Tambah</button>
    </form>
</div>

{{-- Daftar Topik untuk Kelas & Semester ini --}}
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <div class="p-4 bg-surface-container-low border-b border-outline-variant">
        <p class="font-bold text-navy-deep text-sm">{{ $jenjangList[$jenjang] }} &middot; Semester {{ $semester }}</p>
    </div>
    @forelse ($topics as $t)
    <div class="flex items-center justify-between p-4 {{ !$loop->last ? 'border-b border-outline-variant' : '' }}">
        <span class="text-sm text-on-surface">{{ $t->title }}</span>
        <form action="{{ route('admin.topik-materi.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus topik ini?')">
            @csrf @method('DELETE')
            <button class="text-status-error text-xs font-bold">Hapus</button>
        </form>
    </div>
    @empty
    <p class="text-on-surface-variant text-center py-8 text-sm">Belum ada topik untuk kelas & semester ini.</p>
    @endforelse
</div>
@endsection