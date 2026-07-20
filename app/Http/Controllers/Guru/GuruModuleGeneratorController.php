<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AiGeneratedModule;
use App\Models\AiUsageLog;
use App\Models\Letterhead;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruModuleGeneratorController extends Controller
{
    const MEETINGS_PER_BATCH = 3;

    protected function nav(): array
{
    return [
        'navItems' => \App\Support\GuruNav::items(),
        'guard' => 'guru',
        'panelTitle' => 'Panel Guru',
    ];
}

    protected function quotaIdentifier(): string
    {
        return 'teacher:' . Auth::guard('guru')->id();
    }

    public function index()
    {
        $teacherId = Auth::guard('guru')->id();
        $modules = AiGeneratedModule::where('teacher_id', $teacherId)->latest()->get();
        $remaining = AiUsageLog::remaining($this->quotaIdentifier(), 'guru_ai', 5);

        return view('guru.modul-ajar.index', ['modules' => $modules, 'remaining' => $remaining] + $this->nav());
    }

    public function create()
    {
        $teacher = Auth::guard('guru')->user();
        $letterhead = Letterhead::current();
        $remaining = AiUsageLog::remaining($this->quotaIdentifier(), 'guru_ai', 5);

        return view('guru.modul-ajar.create', [
            'teacher' => $teacher,
            'letterhead' => $letterhead,
            'remaining' => $remaining,
        ] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'fase' => 'required|string|max:10',
            'kelas' => 'required|string|max:100',
            'mapel' => 'required|string|max:100',
            'materi' => 'required|string|max:255',
            'meetings_count' => 'required|integer|min:1|max:20',
            'duration_minutes' => 'required|integer|min:45',
            'learning_model' => 'nullable|string|max:255',
            'integration' => 'nullable|string|max:255',
            'learning_outcomes' => 'required|string',
            'teacher_name' => 'required|string|max:255',
            'teacher_nip' => 'nullable|string|max:50',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'signing_place' => 'required|string|max:100',
            'reference_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $identifier = $this->quotaIdentifier();

        if (!AiUsageLog::attempt($identifier, 'guru_ai', 5)) {
            return back()->with('error', 'Batas generate AI hari ini sudah tercapai (5x/hari, gabungan seluruh fitur AI guru). Coba lagi besok.')->withInput();
        }

        $referenceFilePath = null;
        if ($request->hasFile('reference_file')) {
            $referenceFilePath = $request->file('reference_file')->store('module-references', 'public');
        }

        $module = AiGeneratedModule::create([
            'teacher_id' => Auth::guard('guru')->id(),
            'school_name' => $data['school_name'],
            'academic_year' => $data['academic_year'],
            'semester' => $data['semester'],
            'fase' => $data['fase'],
            'kelas' => $data['kelas'],
            'mapel' => $data['mapel'],
            'materi' => $data['materi'],
            'meetings_count' => $data['meetings_count'],
            'completed_meetings' => 0,
            'duration_minutes' => $data['duration_minutes'],
            'learning_model' => $data['learning_model'] ?? null,
            'integration' => $data['integration'] ?? null,
            'learning_outcomes' => $data['learning_outcomes'],
            'teacher_name' => $data['teacher_name'],
            'teacher_nip' => $data['teacher_nip'] ?? null,
            'headmaster_name' => $data['headmaster_name'] ?? null,
            'headmaster_nip' => $data['headmaster_nip'] ?? null,
            'signing_place' => $data['signing_place'],
            'reference_file_path' => $referenceFilePath,
            'status' => 'processing',
            'batches' => [],
            'topic_map' => '',
        ]);

        return redirect()->route('guru.modul-ajar.show', $module);
    }

    /**
     * Generate SATU batch berikutnya yang belum selesai.
     * Dipanggil otomatis oleh JS (fetch) segera setelah halaman show dibuka,
     * dan bisa dipanggil ulang manual lewat tombol "Lanjutkan" kalau sempat gagal.
     */
    public function generateStep(Request $request, AiGeneratedModule $aiGeneratedModule, GeminiService $gemini)
    {
        abort_if($aiGeneratedModule->teacher_id !== Auth::guard('guru')->id(), 403);

        if ($aiGeneratedModule->status === 'completed') {
            return response()->json(['done' => true, 'module' => $this->moduleProgress($aiGeneratedModule)]);
        }

        $totalMeetings = $aiGeneratedModule->meetings_count;
        $completed = $aiGeneratedModule->completed_meetings;
        $startMeeting = $completed + 1;
        $endMeeting = min($totalMeetings, $completed + self::MEETINGS_PER_BATCH);

        try {
            $durationPerMeeting = (int) round($aiGeneratedModule->duration_minutes / $totalMeetings);

            $batchResult = $gemini->generateModuleBatch([
                'school_name' => $aiGeneratedModule->school_name,
                'academic_year' => $aiGeneratedModule->academic_year,
                'semester' => $aiGeneratedModule->semester,
                'fase' => $aiGeneratedModule->fase,
                'kelas' => $aiGeneratedModule->kelas,
                'mapel' => $aiGeneratedModule->mapel,
                'materi' => $aiGeneratedModule->materi,
                'meetings_count' => $totalMeetings,
                'duration_per_meeting' => $durationPerMeeting,
                'learning_model' => $aiGeneratedModule->learning_model ?? '-',
                'integration' => $aiGeneratedModule->integration ?? '-',
                'learning_outcomes' => $aiGeneratedModule->learning_outcomes,
                'reference_note' => null,
            ], $startMeeting, $endMeeting, (string) $aiGeneratedModule->topic_map);

            $batches = $aiGeneratedModule->batches ?? [];
            $batches[] = [
                'meeting_range' => $batchResult['meeting_range'],
                'content' => $batchResult['content'],
            ];

            $newCompleted = $endMeeting;
            $newStatus = $newCompleted >= $totalMeetings ? 'completed' : 'processing';

            $aiGeneratedModule->update([
                'batches' => $batches,
                'completed_meetings' => $newCompleted,
                'topic_map' => trim($aiGeneratedModule->topic_map . ' ' . $batchResult['topic_summary']),
                'status' => $newStatus,
                'error_message' => null,
            ]);

            return response()->json([
                'done' => $newStatus === 'completed',
                'module' => $this->moduleProgress($aiGeneratedModule->fresh()),
            ]);
        } catch (\Exception $e) {
            $aiGeneratedModule->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'done' => false,
                'failed' => true,
                'error' => $e->getMessage(),
                'module' => $this->moduleProgress($aiGeneratedModule->fresh()),
            ], 200);
        }
    }

    protected function moduleProgress(AiGeneratedModule $m): array
    {
        return [
            'status' => $m->status,
            'completed_meetings' => $m->completed_meetings,
            'meetings_count' => $m->meetings_count,
            'total_batches' => $m->total_batches,
            'current_batch' => count($m->batches ?? []),
        ];
    }

    public function show(AiGeneratedModule $aiGeneratedModule)
    {
        abort_if($aiGeneratedModule->teacher_id !== Auth::guard('guru')->id(), 403);

        return view('guru.modul-ajar.show', ['module' => $aiGeneratedModule] + $this->nav());
    }

    public function printPdf(AiGeneratedModule $aiGeneratedModule)
    {
        abort_if($aiGeneratedModule->teacher_id !== Auth::guard('guru')->id(), 403);
        abort_if($aiGeneratedModule->status !== 'completed', 400, 'Modul belum selesai digenerate.');

        $pdf = Pdf::loadView('guru.modul-ajar.print', ['module' => $aiGeneratedModule])
            ->setPaper('a4', 'portrait');

        $filename = 'Modul-Ajar-' . str_replace(' ', '-', $aiGeneratedModule->materi) . '.pdf';

        return $pdf->stream($filename);
    }

    public function destroy(AiGeneratedModule $aiGeneratedModule)
    {
        abort_if($aiGeneratedModule->teacher_id !== Auth::guard('guru')->id(), 403);

        if ($aiGeneratedModule->reference_file_path) {
            Storage::disk('public')->delete($aiGeneratedModule->reference_file_path);
        }
        $aiGeneratedModule->delete();

        return redirect()->route('guru.modul-ajar.index')->with('status', 'Modul Ajar berhasil dihapus.');
    }
}