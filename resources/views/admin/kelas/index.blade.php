@extends('layouts.dashboard')
@section('title', 'Data Kelas')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Data Kelas</h1>
    <a href="{{ route('admin.kelas.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Tambah Kelas</a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                <th class="p-4 text-left">Nama Kelas</th>
                <th class="p-4 text-left">Jenjang</th>
                <th class="p-4 text-left">Fase</th>
                <th class="p-4 text-left">Jumlah Siswa</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($classes as $c)
            <tr class="border-t border-outline-variant">
                <td class="p-4 font-medium text-navy-deep">{{ $c->name }}</td>
                <td class="p-4">{{ $c->jenjang }}</td>
                <td class="p-4"><span class="text-xs px-2 py-1 rounded-full bg-math-teal/10 text-math-teal font-bold">{{ $c->fase ?? '-' }}</span></td>
                <td class="p-4">{{ $c->students_count }}</td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.kelas.edit', $c) }}" class="text-math-teal font-bold">Edit</a>
                    <form action="{{ route('admin.kelas.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas ini? Data siswa yang terhubung ke kelas ini juga akan lepas.')">
                        @csrf @method('DELETE')
                        <button class="text-status-error font-bold">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="p-8 text-center text-on-surface-variant">Belum ada data kelas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection