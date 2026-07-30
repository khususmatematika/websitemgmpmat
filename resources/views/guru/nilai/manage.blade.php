@extends('layouts.dashboard')
@section('title', 'Kelola Nilai')

@section('dashboard-content')
<a href="{{ route('guru.nilai.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali Pilih Kelas/Materi
</a>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div>
        <h1 class="font-headline text-2xl font-bold text-navy-deep">{{ $topic->title }}</h1>
        <p class="text-on-surface-variant text-sm">{{ $class->name }}</p>
    </div>
    <form method="POST" action="{{ route('guru.nilai.publish.toggle') }}">
    @csrf
    <input type="hidden" name="class_id" value="{{ $class->id }}">
    <input type="hidden" name="material_topic_id" value="{{ $topic->id }}">
    <button type="submit"
            class="flex items-center gap-2 px-4 py-2 rounded-md font-bold text-sm whitespace-nowrap
                   {{ $isPublished ? 'bg-status-success text-white' : 'bg-surface-container text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-[18px]">{{ $isPublished ? 'visibility' : 'visibility_off' }}</span>
        {{ $isPublished ? 'Nilai Aktif (Terlihat Siswa)' : 'Nilai Nonaktif' }}
    </button>
</form>
    <a href="{{ route('guru.nilai.export', ['class_id' => $class->id, 'material_topic_id' => $topic->id]) }}"
       class="flex items-center gap-2 bg-navy-deep text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-math-teal transition-colors whitespace-nowrap">
        <span class="material-symbols-outlined text-[18px]">download</span>
        Download Excel
    </a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

{{-- Form Tambah Jenis Penilaian --}}
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-4">Jenis Penilaian & Bobot</h2>

    <form method="POST" action="{{ route('guru.nilai.component.store') }}" class="flex flex-col md:flex-row gap-3 mb-4">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="material_topic_id" value="{{ $topic->id }}">
        <input type="text" name="name" required placeholder="mis. Tugas Harian, UH, UTS" class="rounded-md border-outline-variant text-sm flex-1">
        <input type="number" name="weight" required min="0" max="100" step="0.01" placeholder="Bobot %" class="rounded-md border-outline-variant text-sm w-32">
        <label class="flex items-center gap-2 text-sm whitespace-nowrap">
            <input type="checkbox" name="is_attendance" value="1" class="rounded border-outline-variant">
            Ambil dari Kehadiran
        </label>
        <button class="bg-math-teal text-white px-4 py-2 rounded-md font-bold text-sm whitespace-nowrap">+ Tambah</button>
    </form>

    <div class="flex flex-wrap gap-2">
        @foreach ($components as $c)
        <span class="flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-full text-xs">
            {{ $c->name }} ({{ $c->weight }}%) {{ $c->is_attendance ? '— otomatis' : '' }}
            <form action="{{ route('guru.nilai.component.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus jenis penilaian ini? Nilai yang sudah diinput ikut terhapus.')">
                @csrf @method('DELETE')
                <button class="text-status-error"><span class="material-symbols-outlined text-[14px] align-middle">close</span></button>
            </form>
        </span>
        @endforeach
    </div>
</div>

{{-- Tabel Input Nilai --}}
@if ($components->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-headline text-lg font-bold text-navy-deep">Input Nilai Siswa</h2>
        <p class="text-xs text-on-surface-variant">Tip: bisa copy-paste kolom nilai langsung dari Excel.</p>
    </div>

    <form method="POST" action="{{ route('guru.nilai.save') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="material_topic_id" value="{{ $topic->id }}">

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-surface-container-low text-on-surface-variant">
                    <tr>
                        <th class="p-3 text-left">Nama Siswa</th>
                        @foreach ($components as $c)
                        <th class="p-3 text-center">{{ $c->name }}<br><span class="text-[10px]">({{ $c->weight }}%)</span></th>
                        @endforeach
                        <th class="p-3 text-center">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $rowIndex => $student)
                    <tr class="border-t border-outline-variant">
                        <td class="p-3 font-medium text-navy-deep whitespace-nowrap">{{ $student->name }}</td>
                        @foreach ($components as $c)
                        <td class="p-2 text-center">
                            @if ($c->is_attendance)
                                <span class="inline-block px-2 py-1 bg-math-teal/10 text-math-teal rounded font-bold">
                                    {{ $attendancePercent[$student->id] ?? 0 }}
                                </span>
                            @else
                                @php
                                    $existing = $existingScores[$c->id][$student->id]->score ?? '';
                                @endphp
                                <input type="number" step="0.01" min="0" max="100"
                                       name="scores[{{ $c->id }}][{{ $student->id }}]"
                                       value="{{ $existing }}"
                                       data-col="{{ $c->id }}" data-row="{{ $rowIndex }}"
                                       class="score-input w-20 text-center rounded-md border-outline-variant text-sm">
                            @endif
                        </td>
                        @endforeach
                        <td class="p-2 text-center font-bold text-navy-deep final-grade" data-row="{{ $rowIndex }}">-</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="mt-4 bg-math-teal text-white px-8 py-3 rounded-md font-bold">Simpan Semua Nilai</button>
    </form>
</div>
@else
<p class="text-on-surface-variant text-center py-12">Tambahkan minimal 1 jenis penilaian terlebih dahulu di atas.</p>
@endif

<script>
// Copy-paste Excel: paste beberapa baris ke 1 input akan mengisi ke bawah otomatis
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('paste', function (e) {
        const text = (e.clipboardData || window.clipboardData).getData('text');
        if (!text.includes('\n') && !text.includes('\t')) return; // biarkan paste normal untuk 1 nilai saja

        e.preventDefault();
        const rows = text.split(/\r?\n/).map(r => r.trim()).filter(r => r.length > 0);
        const col = this.dataset.col;
        const startRow = parseInt(this.dataset.row);

        rows.forEach((val, i) => {
            const target = document.querySelector(`.score-input[data-col="${col}"][data-row="${startRow + i}"]`);
            if (target) {
                target.value = val.split('\t')[0]; // ambil kolom pertama kalau ikut ke-paste tab
                target.dispatchEvent(new Event('input'));
            }
        });
    });
});

// Hitung Nilai Akhir live di browser (estimasi, server tetap yang final saat disimpan)
const components = @json($components->map(fn($c) => ['id' => $c->id, 'weight' => $c->weight, 'is_attendance' => $c->is_attendance]));
const attendance = @json($attendancePercent);

function recalcAll() {
    document.querySelectorAll('.final-grade').forEach(el => {
        const row = el.dataset.row;
        let weightedSum = 0, totalWeight = 0;

        components.forEach(c => {
            let val;
            if (c.is_attendance) {
                const studentIdInput = document.querySelectorAll(`tr`)[row]?.querySelector('td:first-child');
                val = null; // dihitung server-side dengan tepat; live calc di sini cukup skip kehadiran untuk estimasi cepat
            } else {
                const input = document.querySelector(`.score-input[data-col="${c.id}"][data-row="${row}"]`);
                val = input && input.value !== '' ? parseFloat(input.value) : null;
            }
            if (val !== null && !isNaN(val)) {
                weightedSum += val * c.weight;
                totalWeight += parseFloat(c.weight);
            }
        });

        el.textContent = totalWeight > 0 ? (weightedSum / totalWeight).toFixed(2) : '-';
    });
}

document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('input', recalcAll);
});
recalcAll();
</script>
@endsection