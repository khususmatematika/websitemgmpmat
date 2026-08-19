<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AssessmentComponent;
use App\Models\JournalAttendance;
use App\Models\MaterialTopic;
use App\Models\SchoolClass;
use App\Models\ScoreAdjustment;
use App\Models\StudentScore;
use App\Models\TeachingJournal;
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

    protected function topicsForClass(SchoolClass $schoolClass)
    {
        $fase = $schoolClass->fase ?: ($schoolClass->jenjang === 'X' ? 'E' : 'F');
        $jenjangCode = "{$schoolClass->jenjang}-{$fase}";

        return MaterialTopic::where('jenjang', $jenjangCode)
            ->orderBy('semester')->orderBy('order_index')->get();
    }

    public function index(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $myClasses = SchoolClass::whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
            ->orderBy('name')->get();

        $selectedClassId = $request->get('class_id');
        $topics = collect();

        if ($selectedClassId) {
            $class = SchoolClass::find($selectedClassId);
            if ($class) $topics = $this->topicsForClass($class);
        }

        return view('guru.nilai.index', [
            'myClasses' => $myClasses,
            'selectedClassId' => $selectedClassId,
            'topics' => $topics,
        ] + $this->nav());
    }

    public function manage(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $classId = $request->get('class_id');
        $materialTopicId = $request->get('material_topic_id');

        abort_unless($classId && $materialTopicId, 400, 'Kelas dan materi wajib dipilih.');

        $class = SchoolClass::with('students')->findOrFail($classId);
        $topic = MaterialTopic::findOrFail($materialTopicId);

        $components = AssessmentComponent::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->orderBy('order_index')
            ->get();

        $journalIds = TeachingJournal::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->pluck('id');

        $attendancePercent = [];
        foreach ($class->students as $student) {
            $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                ->where('student_id', $student->id)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')->pluck('total', 'status');

            $hadir = (int) ($counts['Hadir'] ?? 0);
            $total = array_sum($counts->toArray());
            $attendancePercent[$student->id] = $total > 0 ? round(($hadir / $total) * 100, 0) : 0;
        }

        $existingScores = StudentScore::whereIn('assessment_component_id', $components->pluck('id'))
            ->get()
            ->groupBy('assessment_component_id')
            ->map(fn($group) => $group->keyBy('student_id'));

        // WAJIB: ambil data tambahan/pengurangan yang sudah tersimpan
        $adjustments = ScoreAdjustment::where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->get()
            ->keyBy('student_id');

        return view('guru.nilai.manage', [
            'class' => $class,
            'topic' => $topic,
            'components' => $components,
            'students' => $class->students,
            'attendancePercent' => $attendancePercent,
            'existingScores' => $existingScores,
            'adjustments' => $adjustments,
        ] + $this->nav());
    }

    public function storeComponent(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'is_attendance' => 'nullable|boolean',
        ]);

        $maxOrder = AssessmentComponent::where('class_id', $data['class_id'])
            ->where('material_topic_id', $data['material_topic_id'])
            ->max('order_index');

        AssessmentComponent::create([
            'teacher_id' => Auth::guard('guru')->id(),
            'class_id' => $data['class_id'],
            'material_topic_id' => $data['material_topic_id'],
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

        $classId = $assessmentComponent->class_id;
        $topicId = $assessmentComponent->material_topic_id;
        $assessmentComponent->delete();

        return redirect()->route('guru.nilai.manage', ['class_id' => $classId, 'material_topic_id' => $topicId])
            ->with('status', 'Jenis penilaian berhasil dihapus.');
    }

    public function saveScores(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
            'scores' => 'nullable|array',
            'adjustments' => 'nullable|array',
        ]);

        foreach ($request->input('scores', []) as $componentId => $studentScores) {
            $component = AssessmentComponent::find($componentId);
            if (!$component || $component->is_attendance) continue;

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
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'material_topic_id' => 'required|exists:material_topics,id',
        ]);

        $pub = \App\Models\GradePublication::firstOrCreate(
            ['class_id' => $data['class_id'], 'material_topic_id' => $data['material_topic_id']],
            ['teacher_id' => Auth::guard('guru')->id(), 'is_published' => false]
        );

        $pub->update(['is_published' => !$pub->is_published]);

        return redirect()->route('guru.nilai.manage', $data)
            ->with('status', $pub->is_published ? 'Nilai berhasil diaktifkan untuk dilihat siswa.' : 'Nilai disembunyikan dari siswa.');
    }

    public function export(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();
        $classId = $request->get('class_id');
        $materialTopicId = $request->get('material_topic_id');

        $class = SchoolClass::with('students')->findOrFail($classId);
        $topic = MaterialTopic::findOrFail($materialTopicId);

        $components = AssessmentComponent::where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('material_topic_id', $materialTopicId)
            ->orderBy('order_index')->get();

        $journalIds = TeachingJournal::where('teacher_id', $teacherId)->where('class_id', $classId)->pluck('id');
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
        foreach ($class->students as $student) {
            $col = 1;
            $sheet->setCellValue([$col++, $row], $student->name);

            $weightedSum = 0;

            foreach ($components as $c) {
                if ($c->is_attendance) {
                    $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                        ->where('student_id', $student->id)
                        ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
                    $hadir = (int) ($counts['Hadir'] ?? 0);
                    $total = array_sum($counts->toArray());
                    $final = $totalWeight > 0 ? round(($weightedSum / $totalWeight) + $bonus - $deduction, 2) : '-';
                } else {
                    $score = StudentScore::where('assessment_component_id', $c->id)
                        ->where('student_id', $student->id)->value('score');
                }

                $sheet->setCellValue([$col++, $row], $score ?? '-');
                $weightedSum += ($score ?? 0) * $c->weight;
            }

            $adjustment = ScoreAdjustment::where('class_id', $classId)
                ->where('material_topic_id', $materialTopicId)
                ->where('student_id', $student->id)->first();

            $bonus = $adjustment->bonus ?? 0;
            $deduction = $adjustment->deduction ?? 0;

            $sheet->setCellValue([$col++, $row], $bonus);
            $sheet->setCellValue([$col++, $row], $deduction);

            $final = $totalWeight > 0 ? round(($weightedSum / $totalWeight) + $bonus - $deduction, 2) : '-';
            $sheet->setCellValue([$col, $row], $final);
            $row++;
        }

        $filename = 'Nilai-' . str_replace(' ', '-', $class->name) . '-' . str_replace(' ', '-', $topic->title) . '.xlsx';
        $tempPath = storage_path('app/temp-' . $filename);
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}