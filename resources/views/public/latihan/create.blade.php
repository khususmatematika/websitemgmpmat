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

    <div class="bg-white rounded-xl border border-outline-variant/30 p-4 flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-full bg-math-teal/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-math-teal">person</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant">Mengikuti latihan sebagai</p>
            <p class="font-bold text-navy-deep text-sm">{{ $actor['name'] }}</p>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('latihan.start') }}" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4" id="latihan-form">
        @csrf

        @if ($actor['type'] === 'student' && $studentClasses->count() > 0)
            {{-- SISWA: kelas otomatis dari akun --}}
            @if ($studentClasses->count() === 1)
                @php $only = $studentClasses->first(); @endphp
                <input type="hidden" name="class_name" value="{{ $only->name }}">
                <input type="hidden" name="jenjang" id="jenjang-hidden" value="{{ $classTopicsMap[$only->id]['jenjang'] }}">
                <div class="p-3 bg-surface-container-low rounded-md text-sm">
                    <span class="text-on-surface-variant">Kelas:</span> <strong class="text-navy-deep">{{ $only->name }}</strong>
                </div>
            @else
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">Kelas</label>
                    <select id="class-select" required class="mt-1 w-full rounded-md border-outline-variant">
                        <option value="">Pilih kelas...</option>
                        @foreach ($studentClasses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="class_name" id="class-name-hidden">
                    <input type="hidden" name="jenjang" id="jenjang-hidden">
                </div>
            @endif

            <div>
                <label class="text-sm font-medium text-on-surface-variant">Materi</label>
                <select name="topic" id="topic-select" required {{ $studentClasses->count() > 1 ? 'disabled' : '' }}
                        class="mt-1 w-full rounded-md border-outline-variant disabled:bg-surface-container disabled:cursor-not-allowed">
                    <option value="">{{ $studentClasses->count() > 1 ? 'Pilih kelas dahulu' : 'Pilih materi...' }}</option>
                    @if ($studentClasses->count() === 1)
                        @foreach ($classTopicsMap[$studentClasses->first()->id]['topics'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        @else
            {{-- GURU / tanpa kelas terdaftar: input manual --}}
            <div>
                <label class="text-sm font-medium text-on-surface-variant">Nama/Kode Kelas</label>
                <input type="text" name="class_name" required placeholder="mis. XI IPA 2" value="{{ old('class_name') }}"
                       class="mt-1 w-full rounded-md border-outline-variant">
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
                <select name="topic" id="topic-select-manual" required disabled
                        class="mt-1 w-full rounded-md border-outline-variant disabled:bg-surface-container disabled:cursor-not-allowed">
                    <option value="">Pilih kelas dahulu di atas</option>
                </select>
            </div>
        @endif

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
const classTopicsMap = @json($classTopicsMap);

// Siswa dengan >1 kelas: pilih kelas -> isi hidden fields + isi dropdown materi
const classSelect = document.getElementById('class-select');
if (classSelect) {
    classSelect.addEventListener('change', function () {
        const classId = this.value;
        const topicSelect = document.getElementById('topic-select');
        const nameHidden = document.getElementById('class-name-hidden');
        const jenjangHidden = document.getElementById('jenjang-hidden');

        if (!classId || !classTopicsMap[classId]) {
            topicSelect.innerHTML = '<option value="">Pilih kelas dahulu</option>';
            topicSelect.disabled = true;
            return;
        }

        const data = classTopicsMap[classId];
        nameHidden.value = data.name;
        jenjangHidden.value = data.jenjang;

        topicSelect.innerHTML = data.topics.length
            ? '<option value="">Pilih materi...</option>' + data.topics.map(t => `<option value="${t}">${t}</option>`).join('')
            : '<option value="">Belum ada topik untuk kelas ini</option>';
        topicSelect.disabled = false;
    });
}

// Guru / manual: pilih jenjang -> filter materi dari seluruh Topik Kurikulum
const jenjangSelectManual = document.getElementById('jenjang-select');
if (jenjangSelectManual) {
    const allTopics = @json(\App\Models\MaterialTopic::orderBy('semester')->orderBy('order_index')->get(['jenjang', 'title']));

    jenjangSelectManual.addEventListener('change', function () {
        const jenjang = this.value;
        const topicSelect = document.getElementById('topic-select-manual');

        if (!jenjang) {
            topicSelect.innerHTML = '<option value="">Pilih kelas dahulu di atas</option>';
            topicSelect.disabled = true;
            return;
        }

        const filtered = allTopics.filter(t => t.jenjang === jenjang);
        topicSelect.innerHTML = filtered.length
            ? '<option value="">Pilih materi...</option>' + filtered.map(t => `<option value="${t.title}">${t.title}</option>`).join('')
            : '<option value="">Belum ada topik untuk kelas ini</option>';
        topicSelect.disabled = false;
    });
}

document.getElementById('latihan-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px] align-middle">progress_activity</span> Menyiapkan 10 soal...';
});
</script>
@endsection