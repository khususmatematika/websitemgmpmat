@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-margin-mobile">
    <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-outline-variant/30 p-8">
        <a href="{{ route('nilai.show') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Nilai
        </a>

        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-math-teal text-4xl">lock_reset</span>
            <h1 class="font-headline text-2xl font-bold text-navy-deep mt-2">Ganti Password</h1>
            <p class="text-on-surface-variant text-sm mt-1">Pastikan password baru mudah kamu ingat.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('nilai.password.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Password Saat Ini</label>
                <input type="password" name="current_password" required
                       class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
                <p class="text-xs text-on-surface-variant mt-1">Jika belum pernah diganti, password default sama dengan NIS.</p>
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Password Baru</label>
                <input type="password" name="new_password" required minlength="6"
                       class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" required minlength="6"
                       class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
            </div>
            <button type="submit" class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
@endsection