@extends('layouts.dashboard')
@section('title', 'Tahun Ajaran')

@section('dashboard-content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div>
        <h1 class="font-headline text-2xl font-bold text-navy-deep">Tahun Ajaran & Semester</h1>
        <p class="text-on-surface-variant text-sm">Jurnal dan nilai baru otomatis masuk ke periode aktif. Data periode lama tetap tersimpan sebagai arsip.</p>
    </div>
    <a href="{{ route('admin.tahun-ajaran.promote') }}"
       class="flex items-center gap-2 bg-navy-deep text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-math-teal transition-colors whitespace-nowrap">
        <span class="material-symbols-outlined text-[18px]">upgrade</span>
        Naik Kelas Siswa
    </a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if (session('error'))
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="hero-gradient rounded-2xl p-6 relative overflow-hidden">
    <div class="absolute inset-0 math-pattern opacity-10"></div>
    <div class="relative z-10 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-math-teal text-2xl">event_available</span>
        </div>
        <div>
            <p class="text-white/60 text-[10px] uppercase tracking-widest">Periode Aktif</p>
            @if ($active)
                <p class="font-headline text-white text-xl font-bold">{{ $active->label }}</p>
                <p class="text-white/60 text-xs mt-0.5">{{ $active->start_date?->format('d/m/Y') }} - {{ $active->end_date?->format('d/m/Y') }}</p>
            @else
                <p class="font-headline text-white text-xl font-bold">Belum ada periode aktif</p>
            @endif
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Tambah Periode Baru</h2>
    <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
        @csrf
        <div>
            <label class="text-xs font-medium">Tahun Ajaran</label>
            <input name="name" required placeholder="2026/2027" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-outline-variant text-sm">
        </div>
        <div>
            <label class="text-xs font-medium">Semester</label>
            <select name="semester" class="mt-1 w-full rounded-md border-outline-variant text-sm">
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium">Tanggal Mulai</label>
            <input type="date" name="start_date" required value="{{ old('start_date') }}" class="mt-1 w-full rounded-md border-outline-variant text-sm">
        </div>
        <div>
            <label class="text-xs font-medium">Tanggal Selesai</label>
            <input type="date" name="end_date" required value="{{ old('end_date') }}" class="mt-1 w-full rounded-md border-outline-variant text-sm">
        </div>
        <button class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm">+ Tambah</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-4 text-left">Periode</th>
                    <th class="p-4 text-left">Rentang Tanggal</th>
                    <th class="p-4 text-center">Jurnal</th>
                    <th class="p-4 text-center">Komponen Nilai</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periods as $p)
                <tr class="border-t border-outline-variant {{ $p->is_active ? 'bg-math-teal/5' : '' }}">
                    <td class="p-4">
                        <span class="font-bold text-navy-deep">{{ $p->label }}</span>
                        @if ($p->is_active)
                            <span class="ml-2 text-[10px] bg-math-teal text-white px-2 py-0.5 rounded-full">AKTIF</span>
                        @endif
                    </td>
                    <td class="p-4 text-on-surface-variant text-xs">{{ $p->start_date?->format('d/m/Y') }} - {{ $p->end_date?->format('d/m/Y') }}</td>
                    <td class="p-4 text-center">{{ $p->journals_count }}</td>
                    <td class="p-4 text-center">{{ $p->components_count }}</td>
                    <td class="p-4">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="openEditPeriod({{ $p->id }}, '{{ $p->start_date?->format('Y-m-d') }}', '{{ $p->end_date?->format('Y-m-d') }}')"
                                    class="text-navy-deep text-xs font-bold">Atur Tanggal</button>
                            @unless ($p->is_active)
                            <form method="POST" action="{{ route('admin.tahun-ajaran.activate', $p) }}"
                                  onsubmit="return confirm('Jadikan {{ $p->label }} periode aktif? Jurnal dan nilai baru akan masuk ke periode ini.')">
                                @csrf
                                <button class="text-math-teal text-xs font-bold">Aktifkan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $p) }}" onsubmit="return confirm('Hapus periode ini?')">
                                @csrf @method('DELETE')
                                <button class="text-status-error text-xs font-bold">Hapus</button>
                            </form>
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">Belum ada periode.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-status-warning/10 border border-status-warning/30 rounded-xl p-4 text-xs text-on-surface-variant">
    <p class="font-bold text-navy-deep text-sm mb-1">Urutan saat pergantian periode</p>
    <ol class="list-decimal list-inside space-y-0.5">
        <li>Backup database (mysqldump).</li>
        <li>Tambah periode baru, lalu klik <strong>Aktifkan</strong>.</li>
        <li>Pergantian tahun ajaran: jalankan <strong>Naik Kelas Siswa</strong>.</li>
        <li>Guru memperbarui jadwal di Profil Saya dan membuat komponen penilaian baru.</li>
    </ol>
</div>

<div id="edit-period-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-sm p-6">
        <h3 class="font-headline text-lg font-bold text-navy-deep mb-4">Atur Tanggal Periode</h3>
        <form id="edit-period-form" method="POST">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium">Mulai</label>
                    <input type="date" name="start_date" id="edit-start" required class="mt-1 w-full rounded-md border-outline-variant text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Selesai</label>
                    <input type="date" name="end_date" id="edit-end" required class="mt-1 w-full rounded-md border-outline-variant text-sm">
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="document.getElementById('edit-period-modal').classList.add('hidden')"
                        class="flex-1 py-2 border border-outline-variant rounded-md text-sm font-bold text-on-surface-variant">Batal</button>
                <button class="flex-1 py-2 bg-math-teal text-white rounded-md text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPeriod(id, start, end) {
    document.getElementById('edit-period-form').action = `/admin/tahun-ajaran/${id}`;
    document.getElementById('edit-start').value = start;
    document.getElementById('edit-end').value = end;
    document.getElementById('edit-period-modal').classList.remove('hidden');
}
</script>
@endsection