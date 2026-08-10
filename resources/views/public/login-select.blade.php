@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-margin-mobile py-16">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 text-xs font-bold text-math-teal uppercase tracking-widest mb-3">
                <span class="material-symbols-outlined text-[16px]">lock</span>
                Pilih Peran
            </span>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-navy-deep">Masuk ke Portal</h1>
            <p class="text-on-surface-variant text-sm mt-1">Pilih sesuai peranmu untuk melanjutkan.</p>
        </div>

        @if (session('error'))
            <div class="mb-6 p-3 bg-error-container text-status-error rounded-md text-sm text-center max-w-md mx-auto">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('nilai.login') }}"
               class="group bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-math-teal/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-math-teal text-3xl">school</span>
                </div>
                <h3 class="font-headline text-navy-deep font-bold mb-1">Siswa</h3>
                <p class="text-on-surface-variant text-xs">Masuk dengan NIS & password untuk cek nilai, kehadiran, Forum, dan Karya Siswa.</p>
            </a>

            <a href="{{ route('login') }}"
               class="group bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-navy-deep/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-navy-deep text-3xl">badge</span>
                </div>
                <h3 class="font-headline text-navy-deep font-bold mb-1">Guru / Admin</h3>
                <p class="text-on-surface-variant text-xs">Masuk dengan email & password untuk kelola konten dan dashboard.</p>
            </a>
        </div>
    </div>
</div>
@endsection