@extends('layouts.app')
@section('title', 'Bank Soal')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Beranda
    </a>

    <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Bank Soal</h1>
    <p class="text-on-surface-variant mb-8">Soal dari guru dan hasil generate AI, bisa dipakai ulang untuk belajar.</p>

    @if (session('status'))
        <div class="mb-4 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filter Jenjang --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach ($jenjangList as $key => $label)
        <a href="{{ route('bank-soal.public', ['jenjang' => $key]) }}"
           class="px-4 py-2 rounded-full text-sm font-medium {{ $jenjang == $key ? 'bg-math-teal text-white' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Form Generate AI --}}
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 mb-10">
        <h2 class="font-headline text-lg font-bold text-navy-deep mb-1">Generate Soal Baru dengan AI</h2>
        <p class="text-xs text-on-surface-variant mb-4">Sisa kuota generate hari ini: <strong>{{ $remaining }} dari 3</strong></p>

        <form action="{{ route('bank-soal.generate') }}" method="POST" id="generate-form" class="flex flex-col md:flex-row gap-3">
            @csrf
            <select name="jenjang" id="gen-jenjang" class="rounded-md border-outline-variant text-sm flex-1">
                @foreach ($jenjangList as $key => $label)
                    <option value="{{ $key }}" {{ $jenjang == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="topic" id="gen-topic" required class="rounded-md border-outline-variant text-sm flex-1">
                <option value="">Pilih materi...</option>
            </select>
            <button type="submit" id="generate-btn" @if($remaining <= 0) disabled @endif
        class="bg-math-teal text-white px-6 py-2 rounded-md font-bold text-sm hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
    <span class="material-symbols-outlined text-[18px] align-middle" id="generate-icon">auto_awesome</span>
    <span id="generate-text">Generate 5 Soal</span>
</button>
        </form>
    </div>

    {{-- Soal Upload Guru/Admin --}}
    @if ($files->count() > 0)
    <h2 class="font-headline text-xl font-bold text-navy-deep mb-4">Soal dari Guru</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        @foreach ($files as $f)
        <div class="bg-white p-5 rounded-xl shadow-sm border border-outline-variant/30 flex items-center justify-between">
            <div>
    <p class="font-bold text-navy-deep">{{ $f->title }}</p>
    <p class="text-xs text-on-surface-variant">{{ $f->jenjang }}</p>
</div>
            <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="text-math-teal">
                <span class="material-symbols-outlined">download</span>
            </a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Soal Hasil AI --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
        <h2 class="font-headline text-xl font-bold text-navy-deep">Soal Hasil AI ({{ $aiQuestions->count() }})</h2>

        @if ($availableTopics->count() > 0)
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="jenjang" value="{{ $jenjang }}">
            <label class="text-xs text-on-surface-variant whitespace-nowrap">Filter Materi:</label>
            <select name="topic" onchange="this.form.submit()" class="rounded-md border-outline-variant text-sm">
                <option value="">Semua Materi</option>
                @foreach ($availableTopics as $t)
                    <option value="{{ $t }}" {{ $topic == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse ($aiQuestions as $q)
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-5">
            <p class="text-xs text-math-teal font-bold uppercase tracking-wide mb-1">{{ $q->topic }}</p>
            <p class="font-medium text-navy-deep mb-3">{{ $q->question_text }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-3">
                @foreach ($q->options as $key => $opt)
                <div class="p-2 rounded-md border border-outline-variant">
                    <span class="font-bold">{{ $key }}.</span> {{ $opt }}
                </div>
                @endforeach
            </div>

            <button type="button"
                    onclick="document.getElementById('answer-{{ $q->id }}').classList.toggle('hidden'); this.querySelector('.toggle-icon').classList.toggle('rotate-180')"
                    class="flex items-center gap-1 text-sm font-bold text-navy-deep hover:text-math-teal transition-colors">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
                Lihat Kunci Jawaban & Pembahasan
                <span class="material-symbols-outlined text-[18px] toggle-icon transition-transform">expand_more</span>
            </button>

            <div id="answer-{{ $q->id }}" class="hidden mt-3 pt-3 border-t border-outline-variant">
                <div class="p-3 rounded-md bg-status-success/10 border border-status-success/30 mb-2">
                    <span class="font-bold text-status-success">Jawaban Benar: {{ $q->correct_answer }}.</span>
                    {{ $q->options[$q->correct_answer] ?? '' }}
                </div>
                @if ($q->explanation)
                <p class="text-sm text-on-surface-variant italic">{{ $q->explanation }}</p>
                @endif
            </div>
        </div>
        @empty
        <p class="text-on-surface-variant text-center py-8">
            @if ($topic)
                Belum ada soal AI untuk materi "{{ $topic }}" pada jenjang ini.
            @else
                Belum ada soal AI untuk jenjang ini. Coba generate di atas.
            @endif
        </p>
        @endforelse
    </div>
</section>

<script>
const allTopicsBankSoal = @json(\App\Models\MaterialTopic::orderBy('semester')->orderBy('order_index')->get(['jenjang', 'title']));

function refreshBankSoalTopics() {
    const jenjang = document.getElementById('gen-jenjang').value;
    const topicSelect = document.getElementById('gen-topic');
    const filtered = allTopicsBankSoal.filter(t => t.jenjang === jenjang);

    topicSelect.innerHTML = filtered.length
        ? '<option value="">Pilih materi...</option>' + filtered.map(t => `<option value="${t.title}">${t.title}</option>`).join('')
        : '<option value="">Belum ada topik untuk kelas ini</option>';
}

document.getElementById('gen-jenjang').addEventListener('change', refreshBankSoalTopics);
document.addEventListener('DOMContentLoaded', refreshBankSoalTopics);
</script>
<script>
document.getElementById('generate-form').addEventListener('submit', function () {
    document.getElementById('generate-icon').classList.add('animate-spin');
    document.getElementById('generate-text').textContent = 'Sedang generate soal...';
    document.getElementById('generate-btn').disabled = true;
});
</script>
@endsection