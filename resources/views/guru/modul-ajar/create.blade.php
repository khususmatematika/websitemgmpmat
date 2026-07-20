@extends('layouts.dashboard')
@section('title', 'Buat Modul Ajar Baru')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-1">Buat Modul Ajar Baru</h1>
<p class="text-on-surface-variant mb-6">AI akan generate modul ajar lengkap sesuai Kurikulum Merdeka, dipecah otomatis per beberapa pertemuan agar konsisten.</p>

@if (session('error'))
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('guru.modul-ajar.store') }}" enctype="multipart/form-data" id="module-form"
      class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 max-w-3xl">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium">Nama Sekolah</label>
            <input name="school_name" required value="{{ old('school_name', $letterhead->school_name) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">Tahun Ajaran</label>
            <input name="academic_year" required placeholder="2026/2027" value="{{ old('academic_year') }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">Semester</label>
            <select name="semester" class="mt-1 w-full rounded-md border-outline-variant">
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Fase</label>
            <select name="fase" class="mt-1 w-full rounded-md border-outline-variant">
                @foreach (['A','B','C','D','E','F','F+'] as $f)
                    <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Kelas</label>
            <input name="kelas" required placeholder="XI IPA 2" value="{{ old('kelas') }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">Mata Pelajaran</label>
            <input name="mapel" required value="{{ old('mapel', 'Matematika') }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="text-sm font-medium">Kelas (untuk filter materi)</label>
        <select id="kelas_filter" class="mt-1 w-full rounded-md border-outline-variant">
            <option value="">Pilih kelas...</option>
            @foreach (\App\Support\MathTopics::JENJANG as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Materi</label>
        <select name="materi" id="materi_select" required class="mt-1 w-full rounded-md border-outline-variant" disabled>
            <option value="">Pilih kelas dahulu di atas</option>
        </select>
    </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium">Banyak Pertemuan</label>
            <input type="number" name="meetings_count" id="meetings_count" required min="1" max="20" value="{{ old('meetings_count', 1) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
    <label class="text-sm font-medium">Alokasi Waktu Total</label>
    <div class="mt-1 flex gap-2">
        <input type="text" id="duration_display" readonly value="1 (2x45 menit)"
               class="flex-1 rounded-md border-outline-variant bg-surface-container text-on-surface-variant">
        <input type="number" name="duration_minutes" id="duration_minutes" required min="45" value="{{ old('duration_minutes', 90) }}"
               class="w-24 rounded-md border-outline-variant text-sm" title="Total menit (untuk perhitungan internal)">
    </div>
    <p class="text-xs text-on-surface-variant mt-1">
        Format otomatis: jumlah pertemuan (2x45 menit). Kolom kanan (menit) untuk perhitungan internal, bisa diedit manual jika perlu penyesuaian.
    </p>
</div>

    <div>
        <label class="text-sm font-medium">Model Pembelajaran</label>
        <input name="learning_model" placeholder="mis. Problem Based Learning" value="{{ old('learning_model') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">Integrasi (opsional)</label>
        <input name="integration" placeholder="mis. Literasi, Numerasi, P5" value="{{ old('integration') }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium">Capaian Pembelajaran (CP)</label>
        <textarea name="learning_outcomes" required rows="4" class="mt-1 w-full rounded-md border-outline-variant">{{ old('learning_outcomes') }}</textarea>
    </div>

    <div>
        <label class="text-sm font-medium">File Referensi (opsional)</label>
        <input type="file" name="reference_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1 w-full text-sm">
        <p class="text-xs text-on-surface-variant mt-1">Silabus, contoh modul sebelumnya, atau materi pendukung. Maks 10MB.</p>
    </div>

    <hr class="border-outline-variant">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium">Nama Guru</label>
            <input name="teacher_name" required value="{{ old('teacher_name', $teacher->name) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">NIP Guru</label>
            <input name="teacher_nip" value="{{ old('teacher_nip', $teacher->nip) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">Nama Kepala Sekolah</label>
            <input name="headmaster_name" value="{{ old('headmaster_name', $letterhead->headmaster_name) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">NIP Kepala Sekolah</label>
            <input name="headmaster_nip" value="{{ old('headmaster_nip', $letterhead->headmaster_nip) }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
        <div>
            <label class="text-sm font-medium">Tempat Tanda Tangan</label>
            <input name="signing_place" required value="{{ old('signing_place', 'Turen') }}" class="mt-1 w-full rounded-md border-outline-variant">
        </div>
    </div>

    <button type="submit" id="submit-btn" @if($remaining <= 0) disabled @endif
            class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed">
        Generate Modul Ajar
    </button>
    @if ($remaining <= 0)
        <p class="text-xs text-status-error text-center">Kuota generate AI hari ini sudah habis (5x/hari).</p>
    @endif
</form>

<script>
function recalcDuration() {
    const meetings = parseInt(document.getElementById('meetings_count').value) || 1;
    document.getElementById('duration_minutes').value = meetings * 2 * 45;
    document.getElementById('duration_display').value = `${meetings} (2x45 menit)`;
}
document.getElementById('meetings_count').addEventListener('input', recalcDuration);
document.addEventListener('DOMContentLoaded', recalcDuration);

document.getElementById('module-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px] align-middle">progress_activity</span> Sedang generate, mohon tunggu (bisa 1-2 menit)...';
});
</script>

<script>
const allTopics = @json(\App\Models\MaterialTopic::orderBy('semester')->orderBy('order_index')->get(['jenjang', 'title']));

document.getElementById('kelas_filter').addEventListener('change', function () {
    const jenjang = this.value;
    const materiSelect = document.getElementById('materi_select');

    if (!jenjang) {
        materiSelect.innerHTML = '<option value="">Pilih kelas dahulu di atas</option>';
        materiSelect.disabled = true;
        return;
    }

    const filtered = allTopics.filter(t => t.jenjang === jenjang);
    if (filtered.length === 0) {
        materiSelect.innerHTML = '<option value="">Belum ada topik untuk kelas ini</option>';
        materiSelect.disabled = true;
        return;
    }

    materiSelect.innerHTML = '<option value="">Pilih materi...</option>' +
        filtered.map(t => `<option value="${t.title}">${t.title}</option>`).join('');
    materiSelect.disabled = false;
});
</script>
@endsection