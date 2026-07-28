@extends('layouts.app')
@section('title', 'Cek Nilai')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-margin-mobile">
    <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-outline-variant/30 p-8">
        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-math-teal text-4xl">grade</span>
            <h1 class="font-headline text-2xl font-bold text-navy-deep mt-2">Cek Nilai Siswa</h1>
            <p class="text-on-surface-variant text-sm mt-1">Masuk menggunakan NIS dan password kamu.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('nilai.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-on-surface-variant">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}" required autofocus
                       class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Password</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
                <p class="text-xs text-on-surface-variant mt-1">Password default sama dengan NIS jika belum pernah diganti.</p>
            </div>
            <button type="submit" class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
                Lihat Nilai
            </button>
        </form>
    </div>
</div>
@endsection