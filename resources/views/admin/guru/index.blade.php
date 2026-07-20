@extends('layouts.dashboard')
@section('title', 'Data Guru')

@section('dashboard-content')
<div class="flex items-center justify-between">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Data Guru</h1>
    <a href="{{ route('admin.guru.create') }}" class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm hover:brightness-110">
        + Tambah Guru
    </a>
</div>

@if (session('status'))
    <div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                <th class="p-4 text-left">Nama</th>
                <th class="p-4 text-left">Email</th>
                <th class="p-4 text-left">NIP</th>
                <th class="p-4 text-left">Jabatan</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $t)
            <tr class="border-t border-outline-variant">
                <td class="p-4 font-medium text-navy-deep">{{ $t->name }}</td>
                <td class="p-4">{{ $t->email }}</td>
                <td class="p-4">{{ $t->nip ?? '-' }}</td>
                <td class="p-4">{{ $t->title ?? '-' }}</td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.guru.edit', $t) }}" class="text-math-teal font-bold">Edit</a>
                    <form action="{{ route('admin.guru.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus guru ini?')">
                        @csrf @method('DELETE')
                        <button class="text-status-error font-bold">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $teachers->links() }}
@endsection