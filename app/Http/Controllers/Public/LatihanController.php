<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Leaderboard;
use App\Models\QuizSession;
use App\Services\GeminiService;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LatihanController extends Controller
{
    public function create(Request $request)
{
    $identifier = $this->identifier($request);
    $remaining = AiUsageLog::remaining($identifier, 'latihan', 3);

    return view('public.latihan.create', [
        'jenjangList' => MathTopics::JENJANG,
        'remaining' => $remaining,
    ]);
}

    public function start(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'student_name' => 'required|string|max:100',
            'class_name' => 'required|string|max:50',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'topic' => 'required|string',
        ]);

        $identifier = $this->identifier($request);

        if (!AiUsageLog::attempt($identifier, 'latihan', 3)) {
            return back()->with('error', 'Batas latihan hari ini sudah tercapai (3x/hari). Silakan coba lagi besok.')->withInput();
        }

        try {
            $jenjangLabel = MathTopics::JENJANG[$data['jenjang']];
            $questions = $gemini->generateQuestions($jenjangLabel, $data['topic'], 10);

            $session = QuizSession::create([
                'student_name' => $data['student_name'],
                'class_name' => $data['class_name'],
                'jenjang' => $data['jenjang'],
                'topic' => $data['topic'],
                'questions' => $questions,
                'status' => 'in_progress',
                'student_identifier' => $identifier,
            ]);

            return redirect()->route('latihan.show', $session);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(QuizSession $quizSession)
    {
        if ($quizSession->status === 'completed') {
            return redirect()->route('latihan.show', $quizSession);
        }

        return view('public.latihan.show', [
            'session' => $quizSession,
            'questions' => $quizSession->questionsForClient(),
        ]);
    }

    public function finish(Request $request, QuizSession $quizSession)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|string|in:A,B,C,D',
        ]);

        if ($quizSession->status === 'completed') {
            return response()->json([
                'score' => $quizSession->score,
                'total' => count($quizSession->questions),
            ]);
        }

        $correct = 0;
        foreach ($quizSession->questions as $i => $q) {
            if (($data['answers'][$i] ?? null) === $q['correct_answer']) {
                $correct++;
            }
        }

        $total = count($quizSession->questions);
        $score = (int) round(($correct / $total) * 100);

        $quizSession->update([
            'answers' => $data['answers'],
            'score' => $score,
            'status' => 'completed',
        ]);

        Leaderboard::create([
            'quiz_session_id' => $quizSession->id,
            'student_name' => $quizSession->student_name,
            'jenjang' => $quizSession->jenjang,
            'topic' => $quizSession->topic,
            'score' => $score,
        ]);

        return response()->json([
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
        ]);
    }

    public function leaderboard(Request $request)
    {
        $jenjang = $request->get('jenjang', 'X-E');
        $topic = $request->get('topic');

        $entries = Leaderboard::where('jenjang', $jenjang)
            ->when($topic, fn($q) => $q->where('topic', $topic))
            ->orderByDesc('score')
            ->limit(50)
            ->get();

        return view('public.latihan.leaderboard', [
            'entries' => $entries,
            'jenjang' => $jenjang,
            'topic' => $topic,
            'jenjangList' => MathTopics::JENJANG,
            'topics' => MathTopics::TOPICS,
        ]);
    }

    protected function identifier(Request $request): string
    {
        if (!$request->session()->has('student_identifier')) {
            $request->session()->put('student_identifier', (string) Str::uuid());
        }
        return $request->session()->get('student_identifier');
    }
}