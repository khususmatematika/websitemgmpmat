@extends('layouts.dashboard')
@section('title', 'Konfigurasi Kop Surat')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-1">Konfigurasi Kop Surat</h1>
<p class="text-on-surface-variant mb-6">Digunakan saat mencetak Jurnal Mengajar (dan Modul Ajar nantinya) ke PDF.</p>

@if (session('status'))
<div class="mb-4 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.letterhead.update') }}" enctype="multipart/form-data"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-2xl">
    @csrf @method('PUT')

    <div class="flex items-center gap-4">
        @if ($letterhead->logo_path)
            <img src="{{ asset('storage/'.$letterhead->logo_path) }}" class="w-16 h-16 object-contain border border-outline-variant rounded-md p-1">
        @endif
        <div>
            <label class="text-sm font-medium text-on-surface-variant block mb-1">Logo Sekolah</label>
            <input type="file" name="logo" accept="image/*" class="text-sm">
        </div>
    </div>

    <div>
        <label class="text-sm font-medium">Nama Sekolah</label>
        <input name="school_name" required value="{{ old('school_name', $letterhead->school_name) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">Alamat Sekolah</label>
        <textarea name="address" rows="2" class="mt-1 w-full rounded-md border-outline-variant">{{ old('address', $letterhead->address) }}</textarea>
    </div>

    <div>
        <label class="text-sm font-medium">Nama Kepala Sekolah</label>
        <input name="headmaster_name" value="{{ old('headmaster_name', $letterhead->headmaster_name) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">NIP Kepala Sekolah</label>
        <input name="headmaster_nip" value="{{ old('headmaster_nip', $letterhead->headmaster_nip) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Simpan Konfigurasi</button>
</form>
@endsection