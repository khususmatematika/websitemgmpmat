<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PreviousSemesterGrade;
use App\Models\SchoolClass;
use App\Support\SemesterHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruPreviousGradeController extends Controller
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
        $ok = SchoolClass::taughtBy(Auth::guard('guru')->id(), true)->whereKey($kelas->id)->exists();
        abort_unless($ok, 403, 'Anda tidak mengajar kelas ini.');
    }

    public function index()
    {
        $classes = SchoolClass::taughtBy(Auth::guard('guru')->id(), true)
            ->withCount('students')->orderBy('name')->get();

        $semester = AcademicYear::active()?->semester;

        $classes->each(function ($c) use ($semester) {
            $c->current_semester = SemesterHelper::number($c->jenjang, $semester);
            $c->is_advanced = SemesterHelper::isAdvanced($c);
            $c->prev_semesters = SemesterHelper::previousSemesters($c, $c->current_semester);
            $c->first_semester = SemesterHelper::firstSemester($c);
        });

        return view('guru.nilai-lalu.index', ['classes' => $classes] + $this->nav());
    }

    public function show(SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $period = AcademicYear::active();
        $current = SemesterHelper::number($kelas->jenjang, $period?->semester);
        $semesters = SemesterHelper::previousSemesters($kelas, $current);
        $track = SemesterHelper::track($kelas);

        $students = $kelas->students()->orderBy('name')->get();

        $existing = PreviousSemesterGrade::whereIn('student_id', $students->pluck('id'))
            ->where('track', $track)
            ->get()
            ->groupBy('student_id')
            ->map(fn($g) => $g->pluck('score', 'semester_number'));

        return view('guru.nilai-lalu.show', [
            'kelas' => $kelas,
            'period' => $period,
            'current' => $current,
            'semesters' => $semesters,
            'isAdvanced' => SemesterHelper::isAdvanced($kelas),
            'firstSemester' => SemesterHelper::firstSemester($kelas),
            'students' => $students,
            'existing' => $existing,
        ] + $this->nav());
    }

    public function save(Request $request, SchoolClass $kelas)
    {
        $this->authorizeClass($kelas);

        $request->validate([
            'scores' => 'nullable|array',
            'scores.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $track = SemesterHelper::track($kelas);
        $first = SemesterHelper::firstSemester($kelas);
        $allowedStudents = $kelas->students()->pluck('students.id')->all();

        foreach ($request->input('scores', []) as $studentId => $bySemester) {
            if (!in_array((int) $studentId, $allowedStudents, true)) {
                continue;
            }

            foreach ($bySemester as $semNo => $score) {
                $semNo = (int) $semNo;
                // Mat Lanjut hanya menerima semester 3 ke atas
                if ($semNo < $first || $semNo > 6) {
                    continue;
                }

                if ($score === null || $score === '') {
                    PreviousSemesterGrade::where('student_id', $studentId)
                        ->where('track', $track)
                        ->where('semester_number', $semNo)->delete();
                    continue;
                }

                PreviousSemesterGrade::updateOrCreate(
                    ['student_id' => $studentId, 'track' => $track, 'semester_number' => $semNo],
                    ['score' => (int) round((float) $score)]
                );
            }
        }

        return redirect()->route('guru.nilai-lalu.show', $kelas)->with('status', 'Nilai semester lalu berhasil disimpan.');
    }
}