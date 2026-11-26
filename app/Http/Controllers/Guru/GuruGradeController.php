<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AssessmentComponent;
use App\Models\GradePublication;
use App\Models\MaterialTopic;
use App\Models\SchoolClass;
use App\Models\ScoreAdjustment;
use App\Models\Student;
use App\Models\StudentScore;
use App\Services\GradeCalculator;
use App\Support\TopicJournals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuruGradeController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\GuruNav::items(),
            'guard' => 'guru',
            'panelTitle' => 'Panel Guru',
        ];
    }

    /** Periode yang sedang dilihat: ?periode=ID, atau periode aktif. */
    protected function period(Request $request): ?AcademicYear
    {
        if ($request->filled('periode')) {
            $p = AcademicYear::find($request->periode);
            if ($p) return $p;
        }
        return AcademicYear::active();
    }

    protected function requireActivePeriod(): AcademicYear
    {
        $active = AcademicYear::active();
        abort_unless($active, 422, 'Belum ada tahun ajaran aktif. Hubungi Admin.');
        return $active;
    }

    protected function topicsForClass(SchoolClass $schoolClass, ?string $semester = null)
    {
        $fase = $schoolClass->fase ?: ($schoolClass->jenjang === 'X' ? 'E' : 'F');
        $base = MaterialTopic::where('jenjang', "{$schoolClass->jenjang}-{$fase}");

        $filtered = (clone $base)
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->orderBy('semester')->orderBy('order_index')->get();

        return $filtered->isNotEmpty() ? $filtered : $base->orderBy('semester')->orderBy('order_index')->get();
    }

    public function index(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $myClasses = SchoolClass::taughtBy($teacherId, true)->orderBy('name')->get();

        $periods = AcademicYear::orderByDesc('start_date')->orderByDesc('id')->get();
        $period = $this->period($request);

        $selectedClassId = $request->get('class_id');
        $topics = collect();

        if ($selectedClassId) {
            $class = SchoolClass::find($selectedClassId);
            if ($class) $topics = $this->topicsForClass($class, $period?->semester);
        }

        return view('guru.nilai.index', [
            'myClasses' => $myClasses,
            'selectedClassId' => $selectedClassId,
            'topics' => $topics,
            'periods' => $periods,
            'period' => $period,
        ] + $this->nav());
    }

    public function manage(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $classId = (int) $request->get('class_id');
        $materialTopicId = (int) $request->get('material_topic_id');

        abort_unless($classId && $materialTopicId, 400, 'Kelas dan materi wajib dipilih.');

        $period = $this->period($request);
        abort_unless($period, 422, 'Belum ada tahun ajaran aktif. Hubungi Admin.');
        $isReadOnly = $period->id !== AcademicYear::activeId();

        $class = SchoolClass::findOrFail($classId);
        $topic = MaterialTopic::findOrFail($materialTopicId);

        $students = Student::whereIn('id', GradeCalculator::studentIds($classId, $period->id))
            ->orderBy('name')->get();

        $components = AssessmentComponent::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->where('academic_year_id', $period->id)
            ->orderBy('order_index')
            ->get();

        // Kehadiran HANYA dari jurnal materi ini pada periode ini
        $journalIds = TopicJournals::ids($classId, $topic, $period->id, $teacherId);

        $attendancePercent = [];
        foreach ($students as $student) {
            $attendancePercent[$student->id] = GradeCalculator::attendanceScore($student->id, $journalIds) ?? 0;
        }

        $existingScores = StudentScore::whereIn('assessment_component_id', $components->pluck('id'))
            ->get()
            ->groupBy('assessment_component_id')
            ->map(fn($group) => $group->keyBy('student_id'));

        $adjustments = ScoreAdjustment::where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->where('academic_year_id', $period->id)
            ->get()
            ->keyBy('student_id');

        $isPublished = GradePublication::isPublished($classId, $materialTopicId, $period->id);

        return view('guru.nilai.manage', [
            'class' => $class,
            'topic' => $topic,
            'period' => $period,
            'isReadOnly' => $isReadOnly,
            'components' => $components,
            'students' => $students,
            'attendancePercent' => $attendancePercent,
            'existingScores' => $existingScores,
            'adjustments' => $adjustments,
            'isPublished' => $isPublished,
        ] + $this->nav());
    }

    public function storeComponent(Request $request)
    {
        $active = $this->requireActivePeriod();

        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'is_attendance' => 'nullable|boolean',
        ]);

        $maxOrder = AssessmentComponent::where('class_id', $data['class_id'])
            ->where('material_topic_id', $data['material_topic_id'])
            ->where('academic_year_id', $active->id)
            ->max('order_index');

        AssessmentComponent::create([
            'teacher_id' => Auth::guard('guru')->id(),
            'class_id' => $data['class_id'],
            'material_topic_id' => $data['material_topic_id'],
            'academic_year_id' => $active->id,
            'name' => $data['name'],
            'weight' => $data['weight'],
            'is_attendance' => $request->boolean('is_attendance'),
            'order_index' => ($maxOrder ?? 0) + 1,
        ]);

        return redirect()->route('guru.nilai.manage', [
            'class_id' => $data['class_id'],
            'material_topic_id' => $data['material_topic_id'],
        ])->with('status', 'Jenis penilaian berhasil ditambahkan.');
    }

    public function updateComponent(Request $request, AssessmentComponent $assessmentComponent)
    {
        abort_if($assessmentComponent->teacher_id !== Auth::guard('guru')->id(), 403);
        abort_if((int) $assessmentComponent->academic_year_id !== (int) AcademicYear::activeId(), 403, 'Data arsip tidak dapat diubah.');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $assessmentComponent->update($data);

        return redirect()->route('guru.nilai.manage', [
            'class_id' => $assessmentComponent->class_id,
            'material_topic_id' => $assessmentComponent->material_topic_id,
        ])->with('status', 'Bobot penilaian berhasil diperbarui.');
    }

    public function destroyComponent(AssessmentComponent $assessmentComponent)
    {
        abort_if($assessmentComponent->teacher_id !== Auth::guard('guru')->id(), 403);
        abort_if((int) $assessmentComponent->academic_year_id !== (int) AcademicYear::activeId(), 403, 'Data arsip tidak dapat dihapus.');

        $classId = $assessmentComponent->class_id;
        $topicId = $assessmentComponent->material_topic_id;
        $assessmentComponent->delete();

        return redirect()->route('guru.nilai.manage', ['class_id' => $classId, 'material_topic_id' => $topicId])
            ->with('status', 'Jenis penilaian berhasil dihapus.');
    }

    public function saveScores(Request $request)
    {
        $active = $this->requireActivePeriod();

        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
            'scores' => 'nullable|array',
            'adjustments' => 'nullable|array',
        ]);

        foreach ($request->input('scores', []) as $componentId => $studentScores) {
            $component = AssessmentComponent::find($componentId);
            if (!$component || $component->is_attendance) continue;
            if ((int) $component->academic_year_id !== (int) $active->id) continue;

            foreach ($studentScores as $studentId => $value) {
                if ($value === '' || $value === null) continue;

                StudentScore::updateOrCreate(
                    ['assessment_component_id' => $componentId, 'student_id' => $studentId],
                    ['score' => (int) round((float) $value)]
                );
            }
        }

        foreach ($request->input('adjustments', []) as $studentId => $val) {
            ScoreAdjustment::updateOrCreate(
                [
                    'academic_year_id' => $active->id,
                    'class_id' => $data['class_id'],
                    'material_topic_id' => $data['material_topic_id'],
                    'student_id' => $studentId,
                ],
                [
                    'bonus' => (int) round((float) ($val['bonus'] ?? 0)),
                    'deduction' => (int) round((float) ($val['deduction'] ?? 0)),
                ]
            );
        }

        return redirect()->route('guru.nilai.manage', [
            'class_id' => $data['class_id'],
            'material_topic_id' => $data['material_topic_id'],
        ])->with('status', 'Nilai berhasil disimpan.');
    }

    public function togglePublish(Request $request)
    {
        $active = $this->requireActivePeriod();

        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
        ]);

        $pub = GradePublication::firstOrCreate(
            [
                'academic_year_id' => $active->id,
                'class_id' => $data['class_id'],
                'material_topic_id' => $data['material_topic_id'],
            ],
            ['teacher_id' => Auth::guard('guru')->id(), 'is_published' => false]
        );

        $pub->update(['is_published' => !$pub->is_published]);

        return redirect()->route('guru.nilai.manage', $data)
            ->with('status', $pub->is_published ? 'Nilai berhasil diaktifkan untuk dilihat siswa.' : 'Nilai disembunyikan dari siswa.');
    }

    public function export(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $classId = (int) $request->get('class_id');
        $materialTopicId = (int) $request->get('material_topic_id');
        $period = $this->period($request);
        abort_unless($period, 422, 'Belum ada tahun ajaran aktif.');

        $class = SchoolClass::findOrFail($classId);
        $topic = MaterialTopic::findOrFail($materialTopicId);

        $students = Student::whereIn('id', GradeCalculator::studentIds($classId, $period->id))
            ->orderBy('name')->get();

        $components = AssessmentComponent::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->where('academic_year_id', $period->id)
            ->orderBy('order_index')->get();

        $journalIds = TopicJournals::ids($classId, $topic, $period->id, $teacherId);
        $totalWeight = $components->sum('weight');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai');

        $col = 1;
        $sheet->setCellValue([$col++, 1], 'Nama Siswa');
        foreach ($components as $c) {
            $sheet->setCellValue([$col++, 1], $c->name . ' (' . $c->weight . '%)');
        }
        $sheet->setCellValue([$col++, 1], 'Tambahan');
        $sheet->setCellValue([$col++, 1], 'Pengurangan');
        $sheet->setCellValue([$col, 1], 'Nilai Akhir');

        $row = 2;
        foreach ($students as $student) {
            $col = 1;
            $sheet->setCellValue([$col++, $row], $student->name);
            $weightedSum = 0;

            foreach ($components as $c) {
                if ($c->is_attendance) {
                    $score = GradeCalculator::attendanceScore($student->id, $journalIds);
                } else {
                    $raw = StudentScore::where('assessment_component_id', $c->id)
                        ->where('student_id', $student->id)->value('score');
                    $score = $raw !== null ? (int) round($raw) : null;
                }

                $sheet->setCellValue([$col++, $row], $score ?? '-');
                $weightedSum += ($score ?? 0) * $c->weight;
            }

            $adjustment = ScoreAdjustment::where('class_id', $classId)
                ->where('material_topic_id', $materialTopicId)
                ->where('academic_year_id', $period->id)
                ->where('student_id', $student->id)->first();

            $bonus = (int) ($adjustment->bonus ?? 0);
            $deduction = (int) ($adjustment->deduction ?? 0);

            $sheet->setCellValue([$col++, $row], $bonus);
            $sheet->setCellValue([$col++, $row], $deduction);

            $final = $totalWeight > 0 ? (int) round(($weightedSum / $totalWeight) + $bonus - $deduction) : '-';
            $sheet->setCellValue([$col, $row], $final);
            $row++;
        }

        $filename = 'Nilai-' . str_replace([' ', '/'], '-', $class->name . '-' . $topic->title . '-' . $period->name . '-' . $period->semester) . '.xlsx';
        $tempPath = storage_path('app/temp-' . $filename);
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}