<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
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
        $ok = SchoolClass::whereKey($kelas->id)
            ->whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
            ->exists();
        abort_unless($ok, 403, 'Anda tidak mengajar kelas ini.');
    }

    public function index()
    {
        $teacherId = Auth::guard('guru')->id();
        $classes = SchoolClass::whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
            ->withCount('students')->orderBy('name')->get();

        return view('guru.rekap.index', ['classes' => $classes] + $this->nav());
    }

    public function show(SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        return view('guru.rekap.show', [
            'kelas' => $kelas,
            'recap' => GradeCalculator::classRecap($kelas),
        ] + $this->nav());
    }

    public function save(Request $request, SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $request->validate([
            'topic_weights' => 'nullable|array',
            'topic_weights.*' => 'nullable|numeric|min:0|max:1000',
            'asas_weight' => 'nullable|numeric|min:0|max:1000',
            'asas' => 'nullable|array',
            'asas.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->input('topic_weights', []) as $topicId => $w) {
            RecapWeight::updateOrCreate(
                ['class_id' => $kelas->id, 'material_topic_id' => $topicId],
                ['weight' => $w === null || $w === '' ? 1 : $w]
            );
        }

        RecapWeight::updateOrCreate(
            ['class_id' => $kelas->id, 'material_topic_id' => null],
            ['weight' => $request->input('asas_weight') ?: 0]
        );

        foreach ($request->input('asas', []) as $studentId => $score) {
            if ($score === null || $score === '') {
                AsasScore::where('class_id', $kelas->id)->where('student_id', $studentId)->delete();
                continue;
            }
            AsasScore::updateOrCreate(
                ['class_id' => $kelas->id, 'student_id' => $studentId],
                ['score' => (int) round((float) $score)]
            );
        }

        return redirect()->route('guru.rekap.show', $kelas)->with('status', 'Bobot dan nilai ASAS berhasil disimpan.');
    }

    public function export(SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);
        $recap = GradeCalculator::classRecap($kelas);

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

        $filename = 'Rekap-Nilai-' . str_replace(' ', '-', $kelas->name) . '.xlsx';
        $tempPath = storage_path('app/temp-' . $filename);
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}