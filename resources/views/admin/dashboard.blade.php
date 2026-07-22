@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Selamat datang, {{ auth('admin')->user()->name }}</h1>
<p class="text-on-surface-variant">Ringkasan data sistem Portal Matematika SMAN 1 Turen.</p>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mt-6">
    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-math-teal/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-math-teal text-2xl">groups</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-navy-deep">{{ number_format($stats['total_students']) }}</p>
            <p class="text-xs text-on-surface-variant">Total Siswa</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-navy-deep/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-navy-deep text-2xl">meeting_room</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-navy-deep">{{ number_format($stats['total_classes']) }}</p>
            <p class="text-xs text-on-surface-variant">Total Kelas</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-status-warning/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-status-warning text-2xl">badge</span>
        </div>
        <div>
            <p class="text-2xl font-bold text-navy-deep">{{ number_format($stats['total_teachers']) }}</p>
            <p class="text-xs text-on-surface-variant">Total Guru</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <a href="{{ route('admin.guru.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-math-teal text-3xl mb-2">badge</span>
        <h3 class="font-headline text-navy-deep">Data Guru</h3>
        <p class="text-on-surface-variant text-sm mt-1">Kelola akun & profil guru.</p>
    </a>
    <a href="{{ route('admin.siswa.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-navy-deep text-3xl mb-2">groups</span>
        <h3 class="font-headline text-navy-deep">Data Siswa</h3>
        <p class="text-on-surface-variant text-sm mt-1">Kelola data & kelas siswa.</p>
    </a>
    <a href="{{ route('admin.kelas.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-status-warning text-3xl mb-2">meeting_room</span>
        <h3 class="font-headline text-navy-deep">Data Kelas</h3>
        <p class="text-on-surface-variant text-sm mt-1">Kelola daftar kelas sekolah.</p>
    </a>
</div>
@endsection