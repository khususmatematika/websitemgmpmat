<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JournalAttendance;
use App\Models\SchoolClass;
use App\Models\TeachingJournal;
use Illuminate\Support\Facades\Auth;

class GuruDashboardController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\GuruNav::items(),
            'guard' => 'guru',
            'panelTitle' => 'Panel Guru',
        ];
    }

    public function index()
    {
        $teacher = Auth::guard('guru')->user();

        $classes = $teacher->classes()->withCount('students')->orderBy('classes.name')->get()->unique('id');
        $totalClasses = $classes->count();
        $totalStudents = $classes->sum('students_count');

        $currentMonth = now()->format('Y-m');

        $attendanceByClass = $classes->map(function ($c) use ($teacher, $currentMonth) {
            $journalIds = TeachingJournal::where('teacher_id', $teacher->id)
                ->where('class_id', $c->id)
                ->whereRaw("DATE_FORMAT(journal_date, '%Y-%m') = ?", [$currentMonth])
                ->pluck('id');

            $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $hadir = (int) ($counts['Hadir'] ?? 0);
            $sakit = (int) ($counts['Sakit'] ?? 0);
            $izin = (int) ($counts['Izin'] ?? 0);
            $alpa = (int) ($counts['Alpa'] ?? 0);
            $total = $hadir + $sakit + $izin + $alpa;

            return [
                'name' => $c->name,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : null,
            ];
        });

        return view('guru.dashboard', [
            'totalClasses' => $totalClasses,
            'totalStudents' => $totalStudents,
            'classes' => $classes,
            'attendanceByClass' => $attendanceByClass,
            'currentMonthLabel' => now()->translatedFormat('F Y'),
        ] + $this->nav());
    }
}