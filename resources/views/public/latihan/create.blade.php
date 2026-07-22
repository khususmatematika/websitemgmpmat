@extends('layouts.app')
@section('title', 'Latihan Soal AI')

@section('content')
<div class="max-w-xl mx-auto py-16 px-margin-mobile">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <h1 class="font-headline text-2xl font-bold text-navy-deep mb-2">Latihan Soal Adaptif AI</h1>
    <p class="text-on-surface-variant mb-2">10 soal kontekstual akan digenerate khusus untuk kamu.</p>
    <p class="text-xs text-on-surface-variant mb-8">Sisa kuota latihan hari ini: <strong>{{ $remaining }} dari 3</strong></p>

    @if (session('error'))
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('latihan.start') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4" id="latihan-form">
        @csrf

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Nama Kamu</label>
            <input type="text" name="student_name" id="student_name" required value="{{ old('student_name') }}"
                   class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Kelas</label>
            <input type="text" name="class_name" required placeholder="mis. XI IPA 2" value="{{ old('class_name') }}"
                   class="mt-1 w-full rounded-md border-outline-variant focus:ring-math-teal focus:border-math-teal">
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Jenjang / Kelas</label>
            <select name="jenjang" id="jenjang-select" required class="mt-1 w-full rounded-md border-outline-variant">
                <option value="">Pilih kelas terlebih dahulu...</option>
                @foreach ($jenjangList as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-on-surface-variant">Materi</label>
            <select name="topic" id="topic-select" required disabled
                    class="mt-1 w-full rounded-md border-outline-variant disabled:bg-surface-container disabled:cursor-not-allowed">
                <option value="">Pilih kelas dahulu di atas</option>
            </select>
        </div>

        <button type="submit" id="submit-btn" @if($remaining <= 0) disabled @endif
                class="w-full bg-math-teal text-white py-3 rounded-md font-bold hover:brightness-110 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            Mulai Latihan
        </button>
        @if ($remaining <= 0)
            <p class="text-xs text-status-error text-center">Kuota latihan hari ini sudah habis (3x/hari).</p>
        @endif
    </form>
</div>

<script>
const allTopicsLatihan = @json(\App\Models\MaterialTopic::orderBy('semester')->orderBy('order_index')->get(['jenjang', 'title']));

document.getElementById('jenjang-select').addEventListener('change', function () {
    const jenjang = this.value;
    const topicSelect = document.getElementById('topic-select');

    if (!jenjang) {
        topicSelect.innerHTML = '<option value="">Pilih kelas dahulu di atas</option>';
        topicSelect.disabled = true;
        return;
    }

    const filtered = allTopicsLatihan.filter(t => t.jenjang === jenjang);
    if (filtered.length === 0) {
        topicSelect.innerHTML = '<option value="">Belum ada topik untuk kelas ini</option>';
        topicSelect.disabled = true;
        return;
    }

    topicSelect.innerHTML = '<option value="">Pilih materi...</option>' +
        filtered.map(t => `<option value="${t.title}">${t.title}</option>`).join('');
    topicSelect.disabled = false;
});

document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('student_name');
    const saved = localStorage.getItem('student_display_name');
    if (saved && !nameInput.value) nameInput.value = saved;

    document.getElementById('latihan-form').addEventListener('submit', function () {
        localStorage.setItem('student_display_name', nameInput.value);
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px] align-middle">progress_activity</span> Menyiapkan 10 soal...';
    });
});
</script>
@endsection