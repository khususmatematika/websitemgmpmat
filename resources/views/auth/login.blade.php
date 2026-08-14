@extends('layouts.app')
@section('title', 'Masuk ke Portal')

@push('styles')
<style>
    .login-float { animation: loginFloat 6s ease-in-out infinite; }
    @keyframes loginFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }
    .role-chip { transition: all 0.2s ease; }
    .role-chip:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-72px)] flex">

    {{-- Panel Kiri: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 hero-gradient relative overflow-hidden flex-col items-center justify-center text-center p-16">
        <div class="absolute inset-0 math-pattern opacity-10"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-math-teal/20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary/40 rounded-full blur-[100px]"></div>

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <span class="floating-symbol absolute text-white/10 font-bold" style="left: 10%; top: 20%; font-size: 3rem;">∑</span>
            <span class="floating-symbol absolute text-math-teal/20 font-bold" style="left: 80%; top: 25%; font-size: 2.5rem; animation-delay: 1.5s;">π</span>
            <span class="floating-symbol absolute text-white/10 font-bold" style="left: 20%; top: 70%; font-size: 2rem; animation-delay: 3s;">√</span>
            <span class="floating-symbol absolute text-math-teal/15 font-bold" style="left: 70%; top: 65%; font-size: 3.5rem; animation-delay: 0.8s;">∞</span>
        </div>

        <div class="relative z-10 max-w-md space-y-8">
            <div class="login-float w-24 h-24 rounded-3xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto backdrop-blur-sm shadow-2xl">
                <span class="material-symbols-outlined text-math-teal text-5xl">functions</span>
            </div>
            <div>
                <h1 class="font-headline text-3xl font-bold text-white mb-2">SMAN 1 Turen<br>Math Portal</h1>
                <p class="text-white/70 text-sm leading-relaxed">
                    Satu pintu masuk terpadu untuk Siswa, Guru, dan Admin. Sistem otomatis mengenali peran Anda.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="role-chip bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-math-teal text-xl block mb-1">school</span>
                    <p class="text-white text-xs font-bold">Siswa</p>
                    <p class="text-white/50 text-[10px]">NIS</p>
                </div>
                <div class="role-chip bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-math-teal text-xl block mb-1">badge</span>
                    <p class="text-white text-xs font-bold">Guru</p>
                    <p class="text-white/50 text-[10px]">Email</p>
                </div>
                <div class="role-chip bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-math-teal text-xl block mb-1">admin_panel_settings</span>
                    <p class="text-white text-xs font-bold">Admin</p>
                    <p class="text-white/50 text-[10px]">Email</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Kanan: Form --}}
    <div class="flex-1 flex items-center justify-center p-6 md:p-16 bg-surface">
        <div class="w-full max-w-sm">

            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 rounded-2xl hero-gradient flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-math-teal text-2xl">functions</span>
                </div>
            </div>

            <div class="mb-8">
                <span class="inline-flex items-center gap-2 text-xs font-bold text-math-teal uppercase tracking-widest mb-3">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    Area Terbatas
                </span>
                <h2 class="font-headline text-2xl font-bold text-navy-deep">Selamat Datang Kembali</h2>
                <p class="text-on-surface-variant text-sm mt-1">Sistem otomatis mengenali peran dari data yang Anda masukkan.</p>
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
                    <label class="text-sm font-medium text-navy-deep">Email atau NIS</label>
                    <div class="relative mt-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">person</span>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                               placeholder="nama@sman1turen.sch.id atau 12345"
                               class="w-full pl-10 py-2.5 rounded-lg border-outline-variant focus:ring-2 focus:ring-math-teal focus:border-math-teal text-sm transition-all">
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Guru/Admin: gunakan email. Siswa: gunakan NIS.
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-navy-deep">Kata Sandi</label>
                    <div class="relative mt-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">lock</span>
                        <input type="password" name="password" id="password-field" required placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 rounded-lg border-outline-variant focus:ring-2 focus:ring-math-teal focus:border-math-teal text-sm transition-all">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-navy-deep">
                            <span class="material-symbols-outlined text-[20px]" id="toggle-icon">visibility</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1.5">Siswa: password default sama dengan NIS jika belum pernah diganti.</p>
                </div>

                <label class="flex items-center gap-2 text-sm text-on-surface-variant cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-outline-variant text-math-teal focus:ring-math-teal">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-navy-deep text-white py-3 rounded-lg font-bold hover:bg-math-teal active:scale-[0.98] transition-all shadow-lg shadow-navy-deep/20">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Masuk ke Akun
                </button>
            </form>

            <div class="flex items-center gap-3 my-6">
                <div class="h-px flex-1 bg-outline-variant"></div>
                <span class="text-xs text-on-surface-variant">atau</span>
                <div class="h-px flex-1 bg-outline-variant"></div>
            </div>

            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg border border-outline-variant text-navy-deep text-sm font-bold hover:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('password-field');
    const icon = document.getElementById('toggle-icon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        field.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection