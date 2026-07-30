@extends('layouts.dashboard')
@section('title', 'Konfigurasi Kop Surat')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-1">Konfigurasi Kop Surat</h1>
<p class="text-on-surface-variant mb-6">Digunakan saat mencetak Jurnal Mengajar dan Modul Ajar ke PDF.</p>

@if (session('status'))
<div class="mb-4 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.letterhead.update') }}" enctype="multipart/form-data"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-6 max-w-3xl">
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

    @php
        $lines = [
            1 => ['label' => 'Baris 1 (mis. Pemerintah Provinsi)', 'placeholder' => 'PEMERINTAH PROVINSI JAWA TIMUR'],
            2 => ['label' => 'Baris 2 (mis. Nama Dinas)', 'placeholder' => 'DINAS PENDIDIKAN'],
            3 => ['label' => 'Baris 3 (Nama Sekolah)', 'placeholder' => 'SMA NEGERI 1 TUREN'],
            4 => ['label' => 'Baris 4 (Alamat)', 'placeholder' => 'Jalan Mayjend Panjaitan 65 Turen, Malang 65175, Telp (0341) 824711'],
            5 => ['label' => 'Baris 5 (Laman & Pos-el)', 'placeholder' => 'Laman: www.smanegeri1turen.sch.id, pos-el: admin@smanegeri1turen.sch.id'],
        ];
    @endphp

    @foreach ($lines as $i => $meta)
    <div class="border border-outline-variant rounded-lg p-4">
        <label class="text-sm font-bold text-navy-deep">{{ $meta['label'] }}</label>
        <div class="grid grid-cols-1 md:grid-cols-[1fr_100px_120px] gap-3 mt-2">
            <input type="text" name="line{{ $i }}_text" value="{{ old('line'.$i.'_text', $letterhead->{'line'.$i.'_text'}) }}"
                   placeholder="{{ $meta['placeholder'] }}" {{ $i <= 3 ? 'required' : '' }}
                   class="rounded-md border-outline-variant text-sm">
            <div>
                <label class="text-xs text-on-surface-variant block">Ukuran (pt)</label>
                <input type="number" name="line{{ $i }}_size" value="{{ old('line'.$i.'_size', $letterhead->{'line'.$i.'_size'}) }}"
                       min="6" max="40" class="rounded-md border-outline-variant text-sm w-full">
            </div>
            <label class="flex items-center gap-2 text-sm mt-5">
                <input type="checkbox" name="line{{ $i }}_bold" value="1" {{ old('line'.$i.'_bold', $letterhead->{'line'.$i.'_bold'}) ? 'checked' : '' }}
                       class="rounded border-outline-variant">
                Tebal (Bold)
            </label>
        </div>
    </div>
    @endforeach

    <hr class="border-outline-variant">

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