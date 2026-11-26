@extends('layouts.dashboard')
@section('title', 'Nilai Semester Lalu ' . $kelas->name)

@section('dashboard-content')
<style>
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinner[type=number] { -moz-appearance: textfield; }
</style>

<a href="{{ route('guru.nilai-lalu.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali Pilih Kelas
</a>

<div>
    <h1 class="font-headline text-2xl font-bold text-navy-deep">
        Nilai Semester Lalu: {{ $kelas->name }}
        @if ($isAdvanced)
            <span class="ml-1 align-middle text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full">Kelas Lanjut</span>
        @endif
    </h1>
    <p class="text-on-surface-variant text-sm">
        {{ $period?->label }}
        @if ($current) &middot; kelas ini berada di semester {{ $current }} @endif
        @if ($isAdvanced) &middot; pembelajaran Mat Lanjut dimulai dari semester {{ $firstSemester }} @endif
    </p>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
@endif

@if (!$current)
<div class="p-4 bg-status-warning/10 border border-status-warning/30 rounded-lg text-sm">
    Semester kelas ini belum bisa dihitung. Pastikan jenjang kelas (X, XI, XII) sudah diisi di Data Kelas dan ada periode aktif.
</div>
@elseif (empty($semesters))
<p class="text-on-surface-variant text-center py-12">
    @if ($isAdvanced)
        Kelas Mat Lanjut ini berada di semester {{ $current }}, awal pembelajaran Mat Lanjut, jadi belum ada nilai semester sebelumnya.
    @else
        Kelas ini berada di semester 1, jadi belum ada nilai semester sebelumnya.
    @endif
</p>
@else
<form method="POST" action="{{ route('guru.nilai-lalu.save', $kelas) }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    @csrf
    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="min-width: {{ 260 + count($semesters) * 120 }}px">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-3 text-left">Nama Siswa</th>
                    @foreach ($semesters as $sem)
                    <th class="p-3 text-center">
                        Semester {{ $sem }}
                        <span class="block text-[10px] font-normal">Kelas {{ ['X', 'XI', 'XII'][intdiv($sem - 1, 2)] }} {{ $sem % 2 === 1 ? 'Ganjil' : 'Genap' }}</span>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $i => $student)
                <tr class="border-t border-outline-variant">
                    <td class="p-3 font-medium text-navy-deep whitespace-nowrap">{{ $student->name }}</td>
                    @foreach ($semesters as $sem)
                    <td class="p-2 text-center">
                        <input type="number" min="0" max="100" step="1"
                               name="scores[{{ $student->id }}][{{ $sem }}]"
                               value="{{ $existing[$student->id][$sem] ?? '' }}"
                               data-col="{{ $sem }}" data-row="{{ $i }}"
                               class="prev-input no-spinner w-20 text-center rounded-md border-outline-variant text-sm">
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-on-surface-variant mt-3">
        Tip: salin satu kolom nilai dari Excel, lalu tempel di baris pertama kolom semester yang sesuai. Kosongkan kotak untuk menghapus nilai.
        @if ($isAdvanced) Nilai di halaman ini khusus jalur Mat Lanjut, terpisah dari nilai kelas reguler siswa. @endif
    </p>
    <button type="submit" class="mt-4 bg-math-teal text-white px-8 py-3 rounded-md font-bold">Simpan Nilai Semester Lalu</button>
</form>

<script>
document.querySelectorAll('.prev-input').forEach(input => {
    input.addEventListener('paste', function (e) {
        const text = (e.clipboardData || window.clipboardData).getData('text');
        if (!text.includes('\n') && !text.includes('\t')) return;

        e.preventDefault();
        const rows = text.split(/\r?\n/).map(r => r.trim()).filter(r => r.length > 0);
        const col = this.dataset.col;
        const startRow = parseInt(this.dataset.row);

        rows.forEach((val, i) => {
            const target = document.querySelector(`.prev-input[data-col="${col}"][data-row="${startRow + i}"]`);
            if (target) target.value = val.split('\t')[0];
        });
    });
});
</script>
@endif
@endsection