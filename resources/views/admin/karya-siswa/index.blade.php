@extends('layouts.dashboard')
@section('title', 'Moderasi Karya Siswa')

@section('dashboard-content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Moderasi Karya Siswa</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.karya-siswa.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-md text-sm font-bold {{ $status == 'pending' ? 'bg-status-warning text-navy-deep' : 'bg-surface-container text-on-surface-variant' }}">
            Menunggu
        </a>
        <a href="{{ route('admin.karya-siswa.index', ['status' => 'approved']) }}"
           class="px-4 py-2 rounded-md text-sm font-bold {{ $status == 'approved' ? 'bg-status-success text-white' : 'bg-surface-container text-on-surface-variant' }}">
            Disetujui
        </a>
        <a href="{{ route('admin.karya-siswa.index', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-md text-sm font-bold {{ $status == 'rejected' ? 'bg-status-error text-white' : 'bg-surface-container text-on-surface-variant' }}">
            Ditolak
        </a>
    </div>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($works as $work)
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="aspect-video bg-surface-container">
            @if ($work->file_type === 'image')
                <img src="{{ asset('storage/'.$work->file_path) }}" class="w-full h-full object-cover">
            @elseif ($work->file_type === 'video')
                <video src="{{ asset('storage/'.$work->file_path) }}" controls class="w-full h-full object-cover"></video>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-error text-5xl">picture_as_pdf</span>
                </div>
            @endif
        </div>
        <div class="p-4">
            <p class="font-bold text-navy-deep">{{ $work->student_name }}</p>
            @if ($work->description)
                <p class="text-sm text-on-surface-variant mt-1">{{ Str::limit($work->description, 100) }}</p>
            @endif
            <p class="text-xs text-on-surface-variant mt-2">{{ $work->created_at->format('d M Y H:i') }} &middot; {{ $work->likes_count }} suka</p>

            @if ($status === 'pending')
            <div class="flex gap-2 mt-4">
                <form action="{{ route('admin.karya-siswa.approve', $work) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="w-full bg-status-success text-white text-sm font-bold py-2 rounded-md">Setujui</button>
                </form>
                <form action="{{ route('admin.karya-siswa.reject', $work) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('Tolak karya ini?')">
                    @csrf
                    <button class="w-full bg-status-error text-white text-sm font-bold py-2 rounded-md">Tolak</button>
                </form>
            </div>
            @else
            <form action="{{ route('admin.karya-siswa.destroy', $work) }}" method="POST" class="mt-4"
                  onsubmit="return confirm('Hapus permanen karya ini?')">
                @csrf @method('DELETE')
                <button class="w-full bg-surface-container text-status-error text-sm font-bold py-2 rounded-md">Hapus Permanen</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <p class="text-on-surface-variant col-span-full text-center py-12">Tidak ada karya dengan status ini.</p>
    @endforelse
</div>
@endsection