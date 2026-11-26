<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AsasScore;
use App\Models\RecapWeight;
use App\Models\SchoolClass;
use App\Services\GradeCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuruRecapController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\GuruNav::items(),
            'guard' => 'guru',
            'panelTitle' => 'Panel Guru',
        ];
    }

    protected function authorizeClass(SchoolClass $kelas): void
    {
        $teacherId = Auth::guard('guru')->id();
        $ok = SchoolClass::taughtBy($teacherId, true)->whereKey($kelas->id)->exists();
        abort_unless($ok, 403, 'Anda tidak mengajar kelas ini.');
    }

    protected function period(Request $request): ?AcademicYear
    {
        if ($request->filled('periode')) {
            $p = AcademicYear::find($request->periode);
            if ($p) return $p;
        }
        return AcademicYear::active();
    }

    public function index()
    {
        $teacherId = Auth::guard('guru')->id();
        $classes = SchoolClass::taughtBy($teacherId, true)
    ->withCount('students')->orderBy('name')->get();

        return view('guru.rekap.index', ['classes' => $classes] + $this->nav());
    }

    public function show(Request $request, SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $period = $this->period($request);
        abort_unless($period, 422, 'Belum ada tahun ajaran aktif. Hubungi Admin.');

        return view('guru.rekap.show', [
            'kelas' => $kelas,
            'recap' => GradeCalculator::classRecap($kelas, null, $period->id),
            'period' => $period,
            'periods' => AcademicYear::orderByDesc('start_date')->orderByDesc('id')->get(),
            'isReadOnly' => $period->id !== AcademicYear::activeId(),
        ] + $this->nav());
    }

    public function save(Request $request, SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $active = AcademicYear::active();
        abort_unless($active, 422, 'Belum ada tahun ajaran aktif. Hubungi Admin.');

        $request->validate([
            'topic_weights' => 'nullable|array',
            'topic_weights.*' => 'nullable|numeric|min:0|max:1000',
            'asas_weight' => 'nullable|numeric|min:0|max:1000',
            'asas' => 'nullable|array',
            'asas.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->input('topic_weights', []) as $topicId => $w) {
            RecapWeight::updateOrCreate(
                ['academic_year_id' => $active->id, 'class_id' => $kelas->id, 'material_topic_id' => $topicId],
                ['weight' => $w === null || $w === '' ? 1 : $w]
            );
        }

        RecapWeight::updateOrCreate(
            ['academic_year_id' => $active->id, 'class_id' => $kelas->id, 'material_topic_id' => null],
            ['weight' => $request->input('asas_weight') ?: 0]
        );

        foreach ($request->input('asas', []) as $studentId => $score) {
            if ($score === null || $score === '') {
                AsasScore::where('academic_year_id', $active->id)
                    ->where('class_id', $kelas->id)->where('student_id', $studentId)->delete();
                continue;
            }
            AsasScore::updateOrCreate(
                ['academic_year_id' => $active->id, 'class_id' => $kelas->id, 'student_id' => $studentId],
                ['score' => (int) round((float) $score)]
            );
        }

        return redirect()->route('guru.rekap.show', $kelas)->with('status', 'Bobot dan nilai ASAS berhasil disimpan.');
    }

    public function export(Request $request, SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $period = $this->period($request);
        abort_unless($period, 422, 'Belum ada tahun ajaran aktif.');

        $recap = GradeCalculator::classRecap($kelas, null, $period->id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap');

        $col = 1;
        $sheet->setCellValue([$col++, 1], 'Nama Siswa');
        foreach ($recap['topics'] as $t) {
            $sheet->setCellValue([$col++, 1], $t->title . ' (bobot ' . $recap['topicWeights'][$t->id] . ')');
        }
        $sheet->setCellValue([$col++, 1], 'Rata-rata Materi');
        $sheet->setCellValue([$col++, 1], 'ASAS (bobot ' . $recap['asasWeight'] . ')');
        $sheet->setCellValue([$col, 1], 'Nilai Akhir');

        $r = 2;
        foreach ($recap['rows'] as $row) {
            $col = 1;
            $sheet->setCellValue([$col++, $r], $row['name']);
            foreach ($recap['topics'] as $t) {
                $sheet->setCellValue([$col++, $r], $row['finals'][$t->id] ?? '-');
            }
            $sheet->setCellValue([$col++, $r], $row['materi_avg'] ?? '-');
            $sheet->setCellValue([$col++, $r], $row['asas'] ?? '-');
            $sheet->setCellValue([$col, $r], $row['final'] ?? '-');
            $r++;
        }

        $filename = 'Rekap-Nilai-' . str_replace([' ', '/'], '-', $kelas->name . '-' . $period->name . '-' . $period->semester) . '.xlsx';
        $tempPath = storage_path('app/temp-' . $filename);
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}