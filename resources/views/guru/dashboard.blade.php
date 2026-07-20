@extends('layouts.dashboard')
@section('title', 'Dashboard Guru')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Selamat datang, {{ auth('guru')->user()->name }}</h1>
<p class="text-on-surface-variant">Modul Materi, Jurnal Mengajar, dan Generator Modul Ajar akan tersedia di modul pengembangan berikutnya.</p>
@endsection