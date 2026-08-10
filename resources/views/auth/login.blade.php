@extends('layouts.app')
@section('title', 'Masuk ke Portal')

@section('content')
<div class="min-h-[calc(100vh-72px)] flex">

    <div class="hidden lg:flex lg:w-1/2 hero-gradient relative overflow-hidden flex-col items-center justify-center text-center p-16">
        <div class="absolute inset-0 math-pattern opacity-10"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-math-teal/20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary/40 rounded-full blur-[100px]"></div>

        <div class="relative z-10 max-w-md space-y-6">
            <div class="w-20 h-20 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto backdrop-blur-sm">
                <span class="material-symbols-outlined text-math-teal text-4xl">functions</span>
            </div>
            <h1 class="font-headline text-3xl font-bold text-white">SMAN 1 Turen<br>Math Portal</h1>
            <p class="text-white/70 text-sm leading-relaxed">
                Satu pintu masuk untuk Siswa, Guru, dan Admin. Sistem otomatis mengenali peran Anda.
            </p>
            <div class="flex items-center justify-center gap-6 pt-4 text-white/50 text-xs">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-math-teal">verified_user</span>
                    Akses Aman
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-math-teal">auto_awesome</span>
                    Terintegrasi AI
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6 md:p-16 bg-surface">
        <div class="w-full max-w-sm">
            <div class="mb-10">
                <span class="inline-flex items-center gap-2 text-xs font-bold text-math-teal uppercase tracking-widest mb-3">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    Area Terbatas
                </span>
                <h2 class="font-headline text-2xl font-bold text-navy-deep">Selamat Datang Kembali</h2>
                <p class="text-on-surface-variant text-sm mt-1">Masuk sebagai Siswa (NIS), Guru, atau Admin (Email).</p>
            </div>

            @if (session('error'))
                <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 p-3 bg-error-container text-status-error rounded-md text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium text-navy-deep">Email (Guru/Admin) atau NIS (Siswa)</label>
                    <div class="relative mt-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">person</span>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                               placeholder="nama@sman1turen.sch.id atau 12345"
                               class="w-full pl-10 rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal text-sm">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-navy-deep">Kata Sandi</label>
                    <div class="relative mt-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">lock</span>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full pl-10 rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal text-sm">
                    </div>
                    <p class="text-xs text-on-surface-variant mt-1">Siswa: password default sama dengan NIS jika belum pernah diganti.</p>
                </div>

                <label class="flex items-center gap-2 text-sm text-on-surface-variant cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-outline-variant text-math-teal focus:ring-math-teal">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-navy-deep text-white py-3 rounded-md font-bold hover:bg-math-teal transition-all shadow-lg shadow-navy-deep/20">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Masuk ke Akun
                </button>
            </form>

            <p class="text-center text-xs text-on-surface-variant mt-8">
                Sistem otomatis mengenali peran Anda sebagai Siswa, Guru, atau Admin.
            </p>
        </div>
    </div>
</div>
@endsection