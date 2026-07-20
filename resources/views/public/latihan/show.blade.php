@extends('layouts.app')
@section('title', 'Latihan Soal')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
<div class="max-w-2xl mx-auto py-16 px-margin-mobile"
     x-data="quizApp({{ Illuminate\Support\Js::from($questions) }}, '{{ route('latihan.finish', $session) }}', '{{ csrf_token() }}')">

    <template x-if="!finished">
        <div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold text-on-surface-variant">Soal <span x-text="current + 1"></span> dari <span x-text="questions.length"></span></span>
                <span class="text-sm font-bold text-math-teal" x-text="session_topic"></span>
            </div>

            <div class="h-2 w-full bg-surface-container rounded-full overflow-hidden mb-8">
                <div class="h-full bg-math-teal transition-all duration-300" :style="`width: ${((current + 1) / questions.length) * 100}%`"></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
                <p class="font-medium text-navy-deep text-lg mb-6" x-text="questions[current].question"></p>

                <div class="space-y-3">
                    <template x-for="(text, key) in questions[current].options" :key="key">
                        <button type="button"
                                @click="selectAnswer(key)"
                                :class="answers[current] === key ? 'border-math-teal bg-math-teal/10' : 'border-outline-variant hover:border-math-teal/50'"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-sm font-bold shrink-0"
                                  :class="answers[current] === key ? 'border-math-teal bg-math-teal text-white' : 'border-outline-variant text-on-surface-variant'"
                                  x-text="key"></span>
                            <span x-text="text" class="text-sm"></span>
                        </button>
                    </template>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="prev()" x-show="current > 0"
                            class="px-6 py-2 rounded-md font-bold text-sm text-on-surface-variant border border-outline-variant">
                        Sebelumnya
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" @click="next()" x-show="current < questions.length - 1"
                            class="px-6 py-2 rounded-md font-bold text-sm bg-navy-deep text-white">
                        Selanjutnya
                    </button>
                    <button type="button" @click="submitQuiz()" x-show="current === questions.length - 1" :disabled="submitting"
                            class="px-6 py-2 rounded-md font-bold text-sm bg-math-teal text-white disabled:opacity-50">
                        <span x-text="submitting ? 'Menghitung...' : 'Selesai & Lihat Skor'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-if="finished">
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-8 text-center">
            <span class="material-symbols-outlined text-math-teal text-6xl mb-4">emoji_events</span>
            <h2 class="font-headline text-2xl font-bold text-navy-deep mb-2">Latihan Selesai!</h2>
            <p class="text-on-surface-variant mb-6">Kamu menjawab benar <span x-text="result.correct"></span> dari <span x-text="result.total"></span> soal.</p>
            <div class="text-5xl font-bold text-math-teal mb-8" x-text="result.score + ' / 100'"></div>
            <div class="flex flex-col md:flex-row gap-3 justify-center">
                <a href="{{ route('leaderboard.public', ['jenjang' => $session->jenjang, 'topic' => $session->topic]) }}"
                   class="bg-navy-deep text-white px-6 py-3 rounded-md font-bold">Lihat Leaderboard</a>
                <a href="{{ route('latihan.create') }}" class="border-2 border-navy-deep text-navy-deep px-6 py-3 rounded-md font-bold">Latihan Lagi</a>
            </div>
        </div>
    </template>
</div>

<script>
function quizApp(questions, finishUrl, csrfToken) {
    return {
        questions: questions,
        current: 0,
        answers: {},
        finished: false,
        submitting: false,
        result: { score: 0, correct: 0, total: 0 },
        session_topic: '{{ $session->topic }}',

        selectAnswer(key) {
            this.answers[this.current] = key;
        },
        next() {
            if (this.current < this.questions.length - 1) this.current++;
        },
        prev() {
            if (this.current > 0) this.current--;
        },
        async submitQuiz() {
            this.submitting = true;
            try {
                const res = await fetch(finishUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ answers: this.answers }),
                });
                const data = await res.json();
                this.result = data;
                this.finished = true;
            } catch (err) {
                alert('Gagal mengirim jawaban, coba lagi.');
                console.error(err);
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
@endsection