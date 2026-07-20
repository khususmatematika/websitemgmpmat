<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\QuestionBank;
use App\Services\GeminiService;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
    $jenjang = $request->get('jenjang', 'X-E');
    $topic = $request->get('topic');

    $files = QuestionBank::where('type', 'file')
        ->where('jenjang', $jenjang)
        ->when($topic, fn($q) => $q->where('topic', $topic))
        ->latest()->get();

    $aiQuestions = QuestionBank::where('type', 'ai_question')
        ->where('jenjang', $jenjang)
        ->when($topic, fn($q) => $q->where('topic', $topic))
        ->latest()->get();

    // Daftar materi yang benar-benar punya soal AI untuk jenjang ini (untuk dropdown filter)
    $availableTopics = QuestionBank::where('type', 'ai_question')
        ->where('jenjang', $jenjang)
        ->distinct()
        ->orderBy('topic')
        ->pluck('topic');

    $identifier = $this->identifier($request);
    $remaining = AiUsageLog::remaining($identifier, 'bank_soal', 3);

    return view('public.bank-soal.index', [
        'files' => $files,
        'aiQuestions' => $aiQuestions,
        'jenjang' => $jenjang,
        'topic' => $topic,
        'jenjangList' => MathTopics::JENJANG,
        'availableTopics' => $availableTopics,
        'remaining' => $remaining,
    ]);
    }

    public function generate(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'topic' => 'required|string',
        ]);

        $identifier = $this->identifier($request);

        if (!AiUsageLog::attempt($identifier, 'bank_soal', 3)) {
            return back()->with('error', 'Batas generate AI hari ini sudah tercapai (3x/hari). Silakan coba lagi besok.');
        }

        try {
            $jenjangLabel = MathTopics::JENJANG[$data['jenjang']];
            $questions = $gemini->generateQuestions($jenjangLabel, $data['topic'], 5);

            foreach ($questions as $q) {
                QuestionBank::create([
                    'type' => 'ai_question',
                    'jenjang' => $data['jenjang'],
                    'topic' => $data['topic'],
                    'question_text' => $q['question'],
                    'options' => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                    'explanation' => $q['explanation'] ?? null,
                    'uploaded_by_type' => 'student',
                    'uploaded_by_id' => null,
                ]);
            }

            return back()->with('status', '5 soal baru berhasil digenerate dan ditambahkan ke Bank Soal.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function identifier(Request $request): string
    {
        if (!$request->session()->has('student_identifier')) {
            $request->session()->put('student_identifier', (string) Str::uuid());
        }
        return $request->session()->get('student_identifier');
    }
}