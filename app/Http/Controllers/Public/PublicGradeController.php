<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AssessmentComponent;
use App\Models\JournalAttendance;
use App\Models\MaterialTopic;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;

class PublicGradeController extends Controller
{
    public function showLogin()
    {
        if (session()->has('student_portal_id')) {
            return redirect()->route('nilai.show');
        }
        return view('public.nilai.login');
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

        foreach ($student->classes as $class) {
            $componentsByTopic = AssessmentComponent::where('class_id', $class->id)
                ->get()
                ->groupBy('material_topic_id');

            foreach ($componentsByTopic as $topicId => $comps) {
                $topic = MaterialTopic::find($topicId);
                if (!$topic) {
                    continue;
                }

                $hasAnyScore = StudentScore::whereIn('assessment_component_id', $comps->pluck('id'))
                    ->where('student_id', $student->id)
                    ->exists();
                $hasAttendanceComponent = $comps->contains('is_attendance', true);

                if (!$hasAnyScore && !$hasAttendanceComponent) {
                    continue;
                }

                $journalIds = TeachingJournal::where('class_id', $class->id)->pluck('id');
                $rows = [];
                $weightedSum = 0;
                $totalWeight = 0;

                foreach ($comps as $c) {
                    if ($c->is_attendance) {
                        $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                            ->where('student_id', $student->id)
                            ->selectRaw('status, count(*) as total')
                            ->groupBy('status')
                            ->pluck('total', 'status');
                        $hadir = (int) ($counts['Hadir'] ?? 0);
                        $total = array_sum($counts->toArray());
                        $score = $total > 0 ? round(($hadir / $total) * 100, 2) : null;
                    } else {
                        $score = StudentScore::where('assessment_component_id', $c->id)
                            ->where('student_id', $student->id)
                            ->value('score');
                    }

                    $rows[] = ['name' => $c->name, 'weight' => $c->weight, 'score' => $score];

                    if ($score !== null) {
                        $weightedSum += $score * $c->weight;
                        $totalWeight += $c->weight;
                    }
                }

                $finalScore = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null;

                // ===== STATISTIK KELAS untuk kombinasi kelas + materi ini =====
                $classStudentIds = $class->students()->pluck('students.id');
                $allFinalScores = [];

                foreach ($classStudentIds as $sid) {
                    $wSum = 0;
                    $wTotal = 0;
                    foreach ($comps as $c) {
                        if ($c->is_attendance) {
                            $counts2 = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                                ->where('student_id', $sid)
                                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
                            $hadir2 = (int) ($counts2['Hadir'] ?? 0);
                            $total2 = array_sum($counts2->toArray());
                            $s = $total2 > 0 ? ($hadir2 / $total2) * 100 : null;
                        } else {
                            $s = StudentScore::where('assessment_component_id', $c->id)
                                ->where('student_id', $sid)->value('score');
                        }
                        if ($s !== null) {
                            $wSum += $s * $c->weight;
                            $wTotal += $c->weight;
                        }
                    }
                    if ($wTotal > 0) {
                        $allFinalScores[] = round($wSum / $wTotal, 2);
                    }
                }

                $statistics = [
                    'count' => count($allFinalScores),
                    'average' => count($allFinalScores) > 0 ? round(array_sum($allFinalScores) / count($allFinalScores), 2) : null,
                    'highest' => count($allFinalScores) > 0 ? max($allFinalScores) : null,
                    'lowest' => count($allFinalScores) > 0 ? min($allFinalScores) : null,
                ];

                $results[] = [
                    'class' => $class->name,
                    'class_id' => $class->id,
                    'topic' => $topic->title,
                    'topic_id' => $topic->id,
                    'components' => $rows,
                    'final' => $finalScore,
                    'statistics' => $statistics,
                    'is_class_table_published' => \App\Models\GradePublication::isPublished($class->id, $topicId),
                ];
            }
        }

        // ===== REKAP KEHADIRAN + DETAIL PER PERTEMUAN =====
        $attendanceSummary = [];
        foreach ($student->classes as $class) {
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

        return view('public.nilai.show', [
            'student' => $student,
            'results' => $results,
            'attendanceSummary' => $attendanceSummary,
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
        if (!session()->has('student_portal_id')) {
            return redirect()->route('nilai.login');
        }

        $isPublished = \App\Models\GradePublication::isPublished($classId, $topicId);
        abort_unless($isPublished, 403, 'Tabel nilai kelas ini belum diaktifkan oleh guru.');

        $class = \App\Models\SchoolClass::with('students')->findOrFail($classId);
        $topic = MaterialTopic::findOrFail($topicId);

        $components = AssessmentComponent::where('class_id', $classId)
            ->where('material_topic_id', $topicId)
            ->orderBy('order_index')->get();

        $journalIds = TeachingJournal::where('class_id', $classId)->pluck('id');

        $rows = [];
        foreach ($class->students as $student) {
            $scores = [];
            $weightedSum = 0;
            $totalWeight = 0;

            foreach ($components as $c) {
                if ($c->is_attendance) {
                    $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                        ->where('student_id', $student->id)
                        ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
                    $hadir = (int) ($counts['Hadir'] ?? 0);
                    $total = array_sum($counts->toArray());
                    $score = $total > 0 ? round(($hadir / $total) * 100, 2) : null;
                } else {
                    $score = StudentScore::where('assessment_component_id', $c->id)
                        ->where('student_id', $student->id)->value('score');
                }

                $scores[] = $score;
                if ($score !== null) {
                    $weightedSum += $score * $c->weight;
                    $totalWeight += $c->weight;
                }
            }

            $rows[] = [
                'name' => $student->name,
                'scores' => $scores,
                'final' => $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null,
                'is_me' => $student->id === session('student_portal_id'),
            ];
        }

        return view('public.nilai.class-table', [
            'class' => $class,
            'topic' => $topic,
            'components' => $components,
            'rows' => $rows,
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('student_portal_id');
        return redirect()->route('nilai.login');
    }
}