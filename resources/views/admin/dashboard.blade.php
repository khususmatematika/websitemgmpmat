@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Selamat datang, {{ auth('admin')->user()->name }}</h1>
<p class="text-on-surface-variant">Modul Materi, Moderasi Karya Siswa, dan Jurnal akan tersedia di modul berikutnya.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <a href="{{ route('admin.guru.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/30 hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-math-teal text-3xl mb-2">badge</span>
        <h3 class="font-headline text-navy-deep">Data Guru</h3>
        <p class="text-on-surface-variant text-sm mt-1">Kelola akun & profil guru.</p>
    </a>
</div>
@endsection