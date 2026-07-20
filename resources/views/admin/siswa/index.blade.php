@extends('layouts.dashboard')
@section('title', 'Data Siswa')

@section('dashboard-content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Data Siswa</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.siswa.import.template') }}" class="flex items-center gap-2 px-4 py-2 border border-navy-deep text-navy-deep rounded-md font-bold text-sm">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Template Excel
        </a>
        <a href="{{ route('admin.siswa.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Tambah Siswa</a>
    </div>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

@if (session('import_errors') && count(session('import_errors')) > 0)
<div class="p-4 bg-error-container/20 rounded-md text-sm">
    <p class="font-bold text-status-error mb-2">Baris bermasalah saat import:</p>
    <ul class="list-disc list-inside text-status-error space-y-1">
        @foreach (session('import_errors') as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-1">Import Data Siswa (Excel)</h2>
    <p class="text-xs text-on-surface-variant mb-4">Download template di atas, isi kolom <code>nis</code>, <code>nama</code>, <code>kelas_reguler</code>, <code>kelas_pilihan</code> (opsional), lalu upload di sini.</p>
    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3">
        @csrf
        <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="flex-1 text-sm">
        <button class="bg-navy-deep text-white px-6 py-2 rounded-md font-bold text-sm whitespace-nowrap">Upload & Import</button>
    </form>
</div>

<form method="GET" class="flex gap-2">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau NIS..."
           class="flex-1 rounded-md border-outline-variant text-sm">
    <button class="bg-surface-container text-on-surface-variant px-4 py-2 rounded-md text-sm font-bold">Cari</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                <th class="p-4 text-left">NIS</th>
                <th class="p-4 text-left">Nama</th>
                <th class="p-4 text-left">Kelas</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $s)
            <tr class="border-t border-outline-variant">
                <td class="p-4">{{ $s->nis ?? '-' }}</td>
                <td class="p-4 font-medium text-navy-deep">{{ $s->name }}</td>
                <td class="p-4">
                    @forelse ($s->classes as $c)
                        <span class="text-xs bg-surface-container px-2 py-0.5 rounded-full">{{ $c->name }}</span>
                    @empty
                        <span class="text-xs text-on-surface-variant">Belum ada kelas</span>
                    @endforelse
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.siswa.edit', $s) }}" class="text-math-teal font-bold">Edit</a>
                    <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus siswa ini?')">
                        @csrf @method('DELETE')
                        <button class="text-status-error font-bold">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="p-8 text-center text-on-surface-variant">Belum ada data siswa.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $students->links() }}
@endsection