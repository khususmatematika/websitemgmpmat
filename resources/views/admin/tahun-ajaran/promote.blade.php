@extends('layouts.dashboard')
@section('title', 'Naik Kelas Siswa')

@section('dashboard-content')
<a href="{{ route('admin.tahun-ajaran.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali ke Tahun Ajaran
</a>

<div>
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Naik Kelas Siswa</h1>
    <p class="text-on-surface-variant text-sm">Pilih tujuan tiap kelas asal. Semua siswa di kelas itu dipindahkan sekaligus.</p>
</div>

@if (session('error'))
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
@endif

<div class="bg-status-warning/10 border border-status-warning/30 rounded-xl p-4 text-xs text-on-surface-variant">
    <strong class="text-navy-deep">Penting:</strong> lakukan backup database dulu. Proses ini mengubah keanggotaan kelas dan tidak bisa dibatalkan otomatis.
    Contoh: X-A ke XI-A, XI-A ke XII-A, XII-A ke Lulus. Semua pemindahan dihitung dari kondisi sebelum proses, jadi aman untuk pemindahan berantai.
    Nilai dan kehadiran periode lama tidak ikut berubah.
</div>

<form method="POST" action="{{ route('admin.tahun-ajaran.promote.submit') }}"
      onsubmit="return confirm('Yakin menjalankan pemindahan kelas? Pastikan sudah backup database.')"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    @csrf
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-4 text-left">Kelas Asal</th>
                    <th class="p-4 text-center">Siswa</th>
                    <th class="p-4 text-left">Pindahkan ke</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classes as $c)
                <tr class="border-t border-outline-variant">
                    <td class="p-4 font-bold text-navy-deep">{{ $c->name }}
                        <span class="text-xs font-normal text-on-surface-variant">({{ $c->jenjang }}{{ $c->fase ? ' / Fase '.$c->fase : '' }})</span>
                    </td>
                    <td class="p-4 text-center">{{ $c->students_count }}</td>
                    <td class="p-4">
                        <select name="moves[{{ $c->id }}]" class="rounded-md border-outline-variant text-sm w-full">
                            <option value="keep">- Tidak diubah -</option>
                            <option value="graduate">Lulus / lepas dari kelas</option>
                            @foreach ($classes as $t)
                                @if ($t->id !== $c->id)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-outline-variant">
        <button class="bg-math-teal text-white px-6 py-3 rounded-md font-bold">Jalankan Pemindahan</button>
    </div>
</form>
@endsection