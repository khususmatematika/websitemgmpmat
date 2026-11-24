<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AssessmentComponent;
use App\Models\JournalAttendance;
use App\Models\MaterialTopic;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\TeachingJournal;
use App\Services\GradeCalculator;
use App\Support\TopicJournals;
use Illuminate\Http\Request;

class PublicGradeController extends Controller
{
    public function showLogin()
    {
        if (session()->has('student_portal_id')) {
            return redirect()->route('nilai.show');
        }
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('nis', $data['nis'])->first();

        if (!$student || !$student->checkPassword($data['password'])) {
            return back()->withErrors(['nis' => 'NIS atau password salah.'])->withInput();
        }

        session(['student_portal_id' => $student->id]);

        return redirect()->route('nilai.show');
    }

    public function show()
    {
        $studentId = session('student_portal_id');
        if (!$studentId) {
            return redirect()->route('nilai.login');
        }

        $student = Student::with('classes')->find($studentId);
        if (!$student) {
            session()->forget('student_portal_id');
            return redirect()->route('nilai.login');
        }

        $results = [];

        foreach ($student->classes->sortBy('name') as $class) {
            $componentsByTopic = AssessmentComponent::where('class_id', $class->id)
                ->orderBy('order_index')
                ->get()
                ->groupBy('material_topic_id');

            $classStudentIds = $class->students()->pluck('students.id');

            foreach ($componentsByTopic as $topicId => $comps) {
                $topic = MaterialTopic::find($topicId);
                if (!$topic) {
                    continue;
                }

                $hasAnyScore = StudentScore::whereIn('assessment_component_id', $comps->pluck('id'))
                    ->where('student_id', $student->id)
                    ->exists();

                if (!$hasAnyScore && !$comps->contains('is_attendance', true)) {
                    continue;
                }

                // Kehadiran dihitung HANYA dari jurnal materi ini
                $topicJournalIds = TopicJournals::ids($class->id, $topic);

                $adjMap = \App\Models\ScoreAdjustment::where('class_id', $class->id)
                    ->where('material_topic_id', $topicId)
                    ->get()->keyBy('student_id');

                $totalWeight = $comps->sum('weight');

                $rows = [];
                $weightedSum = 0;

                foreach ($comps as $c) {
                    if ($c->is_attendance) {
                        $score = GradeCalculator::attendanceScore($student->id, $topicJournalIds);
                    } else {
                        $raw = StudentScore::where('assessment_component_id', $c->id)
                            ->where('student_id', $student->id)->value('score');
                        $score = $raw !== null ? (int) round($raw) : null;
                    }

                    $rows[] = ['name' => $c->name, 'weight' => $c->weight, 'score' => $score];
                    $weightedSum += ($score ?? 0) * $c->weight;
                }

                $bonus = (int) ($adjMap[$student->id]->bonus ?? 0);
                $deduction = (int) ($adjMap[$student->id]->deduction ?? 0);

                $baseScore = $totalWeight > 0 ? (int) round($weightedSum / $totalWeight) : null;
                $finalScore = $baseScore !== null ? $baseScore + $bonus - $deduction : null;

                // Statistik kelas untuk kombinasi kelas + materi ini
                $allFinalScores = [];
                foreach ($classStudentIds as $sid) {
                    $f = GradeCalculator::topicFinal($comps, $sid, $topicJournalIds, $adjMap[$sid] ?? null);
                    if ($f !== null) {
                        $allFinalScores[] = $f;
                    }
                }

                $results[] = [
                    'class' => $class->name,
                    'class_id' => $class->id,
                    'topic' => $topic->title,
                    'topic_id' => $topic->id,
                    'components' => $rows,
                    'base' => $baseScore,
                    'final' => $finalScore,
                    'bonus' => $bonus,
                    'deduction' => $deduction,
                    'statistics' => [
                        'count' => count($allFinalScores),
                        'average' => count($allFinalScores) ? (int) round(array_sum($allFinalScores) / count($allFinalScores)) : null,
                        'highest' => count($allFinalScores) ? max($allFinalScores) : null,
                        'lowest' => count($allFinalScores) ? min($allFinalScores) : null,
                    ],
                    'is_class_table_published' => \App\Models\GradePublication::isPublished($class->id, $topicId),
                ];
            }
        }

        // ===== REKAP KEHADIRAN + DETAIL PER PERTEMUAN (keseluruhan per kelas) =====
        $attendanceSummary = [];
        foreach ($student->classes->sortBy('name') as $class) {
            $journals = TeachingJournal::where('class_id', $class->id)
                ->orderBy('journal_date')
                ->get();

            $journalIds = $journals->pluck('id');
            $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                ->where('student_id', $student->id)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')->pluck('total', 'status');

            $hadir = (int) ($counts['Hadir'] ?? 0);
            $sakit = (int) ($counts['Sakit'] ?? 0);
            $izin = (int) ($counts['Izin'] ?? 0);
            $alpa = (int) ($counts['Alpa'] ?? 0);
            $total = $hadir + $sakit + $izin + $alpa;

            $meetings = [];
            foreach ($journals as $j) {
                $status = JournalAttendance::where('teaching_journal_id', $j->id)
                    ->where('student_id', $student->id)
                    ->value('status');

                if ($status !== null) {
                    $meetings[] = [
                        'date' => $j->journal_date->translatedFormat('d F Y'),
                        'materi' => $j->materi,
                        'status' => $status,
                    ];
                }
            }

            $attendanceSummary[] = [
                'class_id' => $class->id,
                'class' => $class->name,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : null,
                'meetings' => $meetings,
            ];
        }

        // ===== NILAI AKHIR SEMESTER per kelas (materi berbobot + ASAS berbobot) =====
        $overallByClass = collect();
        foreach ($student->classes->sortBy('name') as $class) {
            $recap = GradeCalculator::classRecap($class, $student->id);
            $row = collect($recap['rows'])->firstWhere('student_id', $student->id);

            if (!$row || $row['final'] === null || $recap['topics']->isEmpty()) {
                continue;
            }

            $detailTopics = [];
foreach ($recap['topics'] as $t) {
    $detailTopics[] = [
        'title' => $t->title,
        'score' => $row['finals'][$t->id] ?? null,
        'weight' => $recap['topicWeights'][$t->id],
    ];
}

            $overallByClass->push([
                'class' => $class->name,
                'average' => $row['final'],
                'topic_count' => $recap['topics']->count(),
                'asas' => $row['asas'],
                'asas_weight' => $recap['asasWeight'],
                'topics' => $detailTopics,
            ]);
        }

        return view('public.nilai.show', [
            'student' => $student,
            'results' => $results,
            'attendanceSummary' => $attendanceSummary,
            'overallByClass' => $overallByClass->values(),
        ]);
    }

    public function showChangePassword()
    {
        if (!session()->has('student_portal_id')) {
            return redirect()->route('nilai.login');
        }
        return view('public.nilai.change-password');
    }

    public function updatePassword(Request $request)
    {
        $studentId = session('student_portal_id');
        if (!$studentId) {
            return redirect()->route('nilai.login');
        }

        $student = Student::find($studentId);

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [], [
            'new_password' => 'Password Baru',
        ]);

        if (!$student->checkPassword($request->current_password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $student->password = $request->new_password;
        $student->save();

        return redirect()->route('nilai.show')->with('status', 'Password berhasil diperbarui.');
    }

    public function showClassTable($classId, $topicId)
    {
        if (!\App\Support\ActiveActor::isLoggedIn()) {
            return redirect()->route('login');
        }

        $isPublished = \App\Models\GradePublication::isPublished($classId, $topicId);
        abort_unless($isPublished, 403, 'Tabel nilai kelas ini belum diaktifkan oleh guru.');

        $class = \App\Models\SchoolClass::findOrFail($classId);
        $topic = MaterialTopic::findOrFail($topicId);

        $students = Student::whereHas('classes', function ($q) use ($classId) {
            $q->where('classes.id', $classId);
        })->orderBy('name')->get();

        $components = AssessmentComponent::where('class_id', $classId)
            ->where('material_topic_id', $topicId)
            ->orderBy('order_index')->get();

        $topicJournalIds = TopicJournals::ids((int) $classId, $topic);
        $totalWeight = $components->sum('weight');

        $adjMap = \App\Models\ScoreAdjustment::where('class_id', $classId)
            ->where('material_topic_id', $topicId)
            ->get()->keyBy('student_id');

        $actor = \App\Support\ActiveActor::current();
        $myId = $actor['type'] === 'student' ? $actor['id'] : null;

        $rows = [];
        $allFinals = [];

        foreach ($students as $student) {
            $scores = [];
            $weightedSum = 0;

            foreach ($components as $c) {
                if ($c->is_attendance) {
                    $score = GradeCalculator::attendanceScore($student->id, $topicJournalIds);
                } else {
                    $raw = StudentScore::where('assessment_component_id', $c->id)
                        ->where('student_id', $student->id)->value('score');
                    $score = $raw !== null ? (int) round($raw) : null;
                }

                $scores[] = $score;
                $weightedSum += ($score ?? 0) * $c->weight;
            }

            $bonus = (int) ($adjMap[$student->id]->bonus ?? 0);
            $deduction = (int) ($adjMap[$student->id]->deduction ?? 0);

            $base = $totalWeight > 0 ? (int) round($weightedSum / $totalWeight) : null;
            $final = $base !== null ? $base + $bonus - $deduction : null;

            if ($final !== null) {
                $allFinals[] = $final;
            }

            $rows[] = [
                'name' => $student->name,
                'scores' => $scores,
                'base' => $base,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'final' => $final,
                'is_me' => $student->id === $myId,
            ];
        }

        return view('public.nilai.class-table', [
            'class' => $class,
            'topic' => $topic,
            'components' => $components,
            'rows' => $rows,
            'classOverallAverage' => count($allFinals) ? (int) round(array_sum($allFinals) / count($allFinals)) : null,
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('student_portal_id');
        return redirect()->route('nilai.login');
    }
}