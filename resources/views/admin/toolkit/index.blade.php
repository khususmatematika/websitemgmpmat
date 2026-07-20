@extends('layouts.dashboard')
@section('title', 'Kelola Toolkit')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Kelola Toolkit</h1>
    <a href="{{ route('admin.toolkit.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Tambah Toolkit</a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach ($toolkits as $t)
    <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30">
        <span class="material-symbols-outlined text-math-teal text-3xl mb-2">{{ $t->icon }}</span>
        <h3 class="font-bold text-navy-deep">{{ $t->title }}</h3>
        <div class="flex gap-3 mt-3">
            <a href="{{ route('admin.toolkit.edit', $t) }}" class="text-math-teal text-sm font-bold">Edit</a>
            <form action="{{ route('admin.toolkit.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus toolkit ini?')">
                @csrf @method('DELETE')
                <button class="text-status-error text-sm font-bold">Hapus</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection