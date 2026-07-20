@extends('layouts.dashboard')
@section('title', 'Tambah Guru')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Tambah Guru</h1>

@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.guru.store') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-2xl">
    @csrf
    <div><label class="text-sm font-medium">Nama</label><input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">NIP</label><input name="nip" value="{{ old('nip') }}" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">Password Awal</label><input type="password" name="password" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">Nomor WhatsApp</label><input name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="628xxxxxxxxxx" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">Jabatan</label><input name="title" value="{{ old('title') }}" placeholder="mis. Head of Dept" class="mt-1 w-full rounded-md border-outline-variant"></div>
    <div><label class="text-sm font-medium">Deskripsi</label><textarea name="description" rows="3" class="mt-1 w-full rounded-md border-outline-variant">{{ old('description') }}</textarea></div>
    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan</button>
</form>
@endsection