<?php
namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AsasScore;
use App\Models\AssessmentComponent;
use App\Models\JournalAttendance;
use App\Models\MaterialTopic;
use App\Models\RecapWeight;
use App\Models\SchoolClass;
use App\Models\ScoreAdjustment;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\TeachingJournal;
use App\Support\TopicJournals;
use Illuminate\Support\Facades\DB;

class GradeCalculator
{
    public static function attendanceScore(int $studentId, $journalIds): ?int
    {
        if ($journalIds->isEmpty()) {
            return null;
        }

        $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
            ->where('student_id', $studentId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $hadir = (int) ($counts['Hadir'] ?? 0);
        $total = array_sum($counts->toArray());

        return $total > 0 ? (int) round(($hadir / $total) * 100) : null;
    }

    /**
     * Siswa yang tercatat pada kelas di suatu periode.
     * Periode aktif: anggota kelas saat ini. Periode arsip: siswa yang punya nilai/presensi pada periode itu,
     * supaya data lama tetap utuh meski siswa sudah naik kelas.
     */
    public static function studentIds(int $classId, ?int $periodId)
    {
        $current = DB::table('class_student')->where('class_id', $classId)->pluck('student_id');

        if (!$periodId || $periodId === AcademicYear::activeId()) {
            return $current->unique()->values();
        }

        $scoreTbl = (new StudentScore)->getTable();
        $compTbl = (new AssessmentComponent)->getTable();
        $attTbl = (new JournalAttendance)->getTable();
        $jrnTbl = (new TeachingJournal)->getTable();

        $fromScores = DB::table($scoreTbl)
            ->join($compTbl, "$compTbl.id", '=', "$scoreTbl.assessment_component_id")
            ->where("$compTbl.class_id", $classId)
            ->where("$compTbl.academic_year_id", $periodId)
            ->pluck("$scoreTbl.student_id");

        $fromAtt = DB::table($attTbl)
            ->join($jrnTbl, "$jrnTbl.id", '=', "$attTbl.teaching_journal_id")
            ->where("$jrnTbl.class_id", $classId)
            ->where("$jrnTbl.academic_year_id", $periodId)
            ->pluck("$attTbl.student_id");

        return $fromScores->merge($fromAtt)->unique()->values();
    }

    /** Nilai akhir satu siswa pada satu materi (sudah termasuk tambahan/pengurangan). */
    public static function topicFinal($comps, int $studentId, $journalIds, $adj): ?int
    {
        $totalWeight = $comps->sum('weight');
        if ($totalWeight <= 0) {
            return null;
        }

        $weighted = 0;
        foreach ($comps as $c) {
            if ($c->is_attendance) {
                $s = self::attendanceScore($studentId, $journalIds);
            } else {
                $raw = StudentScore::where('assessment_component_id', $c->id)
                    ->where('student_id', $studentId)->value('score');
                $s = $raw !== null ? (int) round($raw) : null;
            }
            $weighted += ($s ?? 0) * $c->weight;
        }

        $base = (int) round($weighted / $totalWeight);

        return $base + (int) ($adj->bonus ?? 0) - (int) ($adj->deduction ?? 0);
    }

    /** Rekap satu kelas pada satu periode. $onlyStudentId membatasi ke satu siswa. */
    public static function classRecap(SchoolClass $class, ?int $onlyStudentId = null, ?int $periodId = null): array
    {
        $periodId = $periodId ?? AcademicYear::activeId();

        $students = Student::whereIn('id', self::studentIds($class->id, $periodId))
            ->when($onlyStudentId, fn($q) => $q->where('id', $onlyStudentId))
            ->orderBy('name')->get();

        $componentsByTopic = AssessmentComponent::where('class_id', $class->id)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->orderBy('order_index')->get()->groupBy('material_topic_id');

        $topics = MaterialTopic::whereIn('id', $componentsByTopic->keys())
            ->orderBy('semester')->orderBy('order_index')->get();

        $weights = RecapWeight::where('class_id', $class->id)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->get();
        $storedTopicWeights = $weights->whereNotNull('material_topic_id')->pluck('weight', 'material_topic_id');
        $asasWeight = (float) ($weights->whereNull('material_topic_id')->first()->weight ?? 0);

        $topicWeights = [];
        foreach ($topics as $t) {
            $topicWeights[$t->id] = (float) ($storedTopicWeights[$t->id] ?? 1);
        }

        $asas = AsasScore::where('class_id', $class->id)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->pluck('score', 'student_id');

        $adjByTopic = ScoreAdjustment::where('class_id', $class->id)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->get()->groupBy('material_topic_id')->map(fn($g) => $g->keyBy('student_id'));

        $journalsByTopic = [];
        foreach ($topics as $t) {
            $journalsByTopic[$t->id] = TopicJournals::ids($class->id, $t, $periodId);
        }

        $rows = [];
        foreach ($students as $student) {
            $sum = 0;
            $weightSum = 0;
            $finals = [];

            foreach ($topics as $t) {
                $f = self::topicFinal(
                    $componentsByTopic[$t->id],
                    $student->id,
                    $journalsByTopic[$t->id],
                    $adjByTopic[$t->id][$student->id] ?? null
                );
                $finals[$t->id] = $f;
                $sum += ($f ?? 0) * $topicWeights[$t->id];
                $weightSum += $topicWeights[$t->id];
            }

            $asasScore = $asas[$student->id] ?? null;
            $totalW = $weightSum + $asasWeight;

            $rows[] = [
                'student_id' => $student->id,
                'name' => $student->name,
                'finals' => $finals,
                'materi_avg' => $weightSum > 0 ? (int) round($sum / $weightSum) : null,
                'asas' => $asasScore !== null ? (int) $asasScore : null,
                'final' => $totalW > 0 ? (int) round(($sum + ($asasScore ?? 0) * $asasWeight) / $totalW) : null,
            ];
        }

        return [
            'topics' => $topics,
            'topicWeights' => $topicWeights,
            'asasWeight' => $asasWeight,
            'rows' => $rows,
        ];
    }
}