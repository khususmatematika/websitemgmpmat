@extends('layouts.app')
@section('title', 'Login Guru')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-margin-mobile">
    <div class="w-full max-w-md bg-navy-deep rounded-xl shadow-sm border border-outline-variant/30 p-8">
        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-math-teal text-4xl">school</span>
            <h1 class="font-headline text-2xl font-bold text-navy-deep mt-2">Login Guru</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('guru.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Password</label>
                <input type="password" name="password" required
                    class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
            </div>
            <label class="flex items-center gap-2 text-sm text-on-surface-variant">
                <input type="checkbox" name="remember" class="rounded border-outline-variant">
                Ingat saya
            </label>
            <button type="submit" class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 transition-all">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection