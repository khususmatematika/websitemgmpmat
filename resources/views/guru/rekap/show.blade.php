@extends('layouts.dashboard')
@section('title', 'Rekap Nilai ' . $kelas->name)

@section('dashboard-content')
<style>
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinner[type=number] { -moz-appearance: textfield; }
</style>

<a href="{{ route('guru.rekap.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali Pilih Kelas
</a>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div>
        <h1 class="font-headline text-2xl font-bold text-navy-deep">Rekap Nilai {{ $kelas->name }}</h1>
        <p class="text-on-surface-variant text-sm">Nilai Akhir = (Σ nilai materi x bobot + ASAS x bobot ASAS) / total bobot.</p>
    </div>
    <a href="{{ route('guru.rekap.export', $kelas) }}"
       class="flex items-center gap-2 bg-navy-deep text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-math-teal transition-colors whitespace-nowrap">
        <span class="material-symbols-outlined text-[18px]">download</span>
        Download Excel
    </a>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
@endif

@if ($recap['topics']->isEmpty())
<p class="text-on-surface-variant text-center py-12">Belum ada materi yang memiliki komponen penilaian di kelas ini. Buat dulu di menu Input Nilai.</p>
@else
<form method="POST" action="{{ route('guru.rekap.save', $kelas) }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    @csrf
    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="min-width: {{ 420 + $recap['topics']->count() * 130 }}px">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-3 text-left">Nama Siswa</th>
                    @foreach ($recap['topics'] as $t)
                    <th class="p-3 text-center align-top">
                        <span class="block text-xs font-bold leading-tight mb-1">{{ $t->title }}</span>
                        <span class="text-[10px] font-normal">Bobot</span>
                        <input type="number" min="0" step="1" name="topic_weights[{{ $t->id }}]"
                               value="{{ rtrim(rtrim(number_format($recap['topicWeights'][$t->id], 2, '.', ''), '0'), '.') }}"
                               data-topic="{{ $t->id }}"
                               class="topic-weight no-spinner w-14 text-center rounded-md border-outline-variant text-xs">
                    </th>
                    @endforeach
                    <th class="p-3 text-center">Rata-rata<br>Materi</th>
                    <th class="p-3 text-center bg-status-warning/10 align-top">
                        <span class="block text-xs font-bold mb-1">ASAS</span>
                        <span class="text-[10px] font-normal">Bobot</span>
                        <input type="number" min="0" step="1" name="asas_weight" id="asas-weight"
                               value="{{ rtrim(rtrim(number_format($recap['asasWeight'], 2, '.', ''), '0'), '.') ?: 0 }}"
                               class="no-spinner w-14 text-center rounded-md border-outline-variant text-xs">
                    </th>
                    <th class="p-3 text-center">Nilai<br>Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recap['rows'] as $i => $row)
                <tr class="border-t border-outline-variant" data-row="{{ $i }}">
                    <td class="p-3 font-medium text-navy-deep whitespace-nowrap">{{ $row['name'] }}</td>
                    @foreach ($recap['topics'] as $t)
                    <td class="p-2 text-center">
                        <span class="topic-final font-bold text-navy-deep" data-topic="{{ $t->id }}"
                              data-value="{{ $row['finals'][$t->id] ?? 0 }}">{{ $row['finals'][$t->id] ?? '-' }}</span>
                    </td>
                    @endforeach
                    <td class="p-2 text-center font-bold materi-avg">{{ $row['materi_avg'] ?? '-' }}</td>
                    <td class="p-2 text-center bg-status-warning/5">
                        <input type="number" min="0" max="100" step="1" name="asas[{{ $row['student_id'] }}]"
                               value="{{ $row['asas'] }}"
                               class="asas-input no-spinner w-16 text-center rounded-md border-outline-variant text-sm">
                    </td>
                    <td class="p-2 text-center font-bold text-blue-600 final-cell">{{ $row['final'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-on-surface-variant mt-3">Nilai materi dihitung otomatis dari menu Input Nilai. Yang bisa diubah di sini hanya bobot dan nilai ASAS.</p>
    <button type="submit" class="mt-4 bg-math-teal text-white px-8 py-3 rounded-md font-bold">Simpan Bobot dan ASAS</button>
</form>

<script>
function recalcRecap() {
    const tw = {};
    document.querySelectorAll('.topic-weight').forEach(i => tw[i.dataset.topic] = parseFloat(i.value || 0));
    const aw = parseFloat(document.getElementById('asas-weight').value || 0);
    const topicWeightSum = Object.values(tw).reduce((a, b) => a + b, 0);

    document.querySelectorAll('tbody tr[data-row]').forEach(tr => {
        let sum = 0;
        tr.querySelectorAll('.topic-final').forEach(s => {
            sum += parseFloat(s.dataset.value || 0) * (tw[s.dataset.topic] || 0);
        });
        const asas = parseFloat(tr.querySelector('.asas-input').value || 0);
        tr.querySelector('.materi-avg').textContent = topicWeightSum > 0 ? Math.round(sum / topicWeightSum) : '-';
        const total = topicWeightSum + aw;
        tr.querySelector('.final-cell').textContent = total > 0 ? Math.round((sum + asas * aw) / total) : '-';
    });
}
document.querySelectorAll('.topic-weight, #asas-weight, .asas-input').forEach(i => i.addEventListener('input', recalcRecap));
</script>
@endif
@endsection