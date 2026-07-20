@extends('layouts.dashboard')
@section('title', 'Persentase Kehadiran Siswa')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Persentase Kehadiran Siswa</h1>
<p class="text-on-surface-variant">Dihitung dari jurnal yang sudah kamu isi pada bulan yang dipilih.</p>

<form method="GET" class="flex flex-col md:flex-row gap-3">
    <select name="class_id" onchange="this.form.submit()" class="rounded-md border-outline-variant text-sm flex-1">
        @forelse ($myClasses as $c)
            <option value="{{ $c->id }}" {{ $filterClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @empty
            <option value="">Belum ada kelas</option>
        @endforelse
    </select>
    <input type="month" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()"
           class="rounded-md border-outline-variant text-sm">
</form>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-4">
    <p class="text-xs text-on-surface-variant">Jumlah pertemuan tercatat bulan ini: <strong>{{ $totalPertemuan }}</strong></p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                <th class="p-3 text-left">Nama Siswa</th>
                <th class="p-3 text-center">Hadir</th>
                <th class="p-3 text-center">Sakit</th>
                <th class="p-3 text-center">Izin</th>
                <th class="p-3 text-center">Alpa</th>
                <th class="p-3 text-right">Persentase Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summary as $row)
            <tr class="border-t border-outline-variant">
                <td class="p-3 font-medium text-navy-deep">{{ $row['student']->name }}</td>
                <td class="p-3 text-center">{{ $row['hadir'] }}</td>
                <td class="p-3 text-center">{{ $row['sakit'] }}</td>
                <td class="p-3 text-center">{{ $row['izin'] }}</td>
                <td class="p-3 text-center">{{ $row['alpa'] }}</td>
                <td class="p-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <div class="w-24 h-2 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-full {{ $row['persentase'] >= 90 ? 'bg-status-success' : ($row['persentase'] >= 75 ? 'bg-status-warning' : 'bg-status-error') }}"
                                 style="width: {{ $row['persentase'] }}%"></div>
                        </div>
                        <span class="font-bold {{ $row['persentase'] >= 90 ? 'text-status-success' : ($row['persentase'] >= 75 ? 'text-status-warning' : 'text-status-error') }}">
                            {{ $row['persentase'] }}%
                        </span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-8 text-center text-on-surface-variant">Belum ada data kehadiran untuk kelas/bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection