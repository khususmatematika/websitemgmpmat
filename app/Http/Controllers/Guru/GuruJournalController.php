<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JournalAttendance;
use App\Models\Letterhead;
use App\Models\MaterialTopic;
use App\Models\SchoolClass;
use App\Models\TeacherClass;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruJournalController extends Controller
{
    protected function nav(): array
{
    return [
        'navItems' => \App\Support\GuruNav::items(),
        'guard' => 'guru',
        'panelTitle' => 'Panel Guru',
    ];
}

    protected function todayDayName(): string
    {
        $map = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        return $map[now()->timezone('Asia/Jakarta')->format('l')];
    }

    protected function topicsForClass(SchoolClass $schoolClass)
{
    $jenjangCode = $this->resolveJenjangCode($schoolClass);

    return MaterialTopic::where('jenjang', $jenjangCode)
        ->orderBy('semester')
        ->orderBy('order_index')
        ->get();
}

/**
 * Kode jenjang untuk MaterialTopic dibentuk dari jenjang + fase kelas,
 * sesuai konfigurasi yang diatur Admin di Data Kelas (mis. "XI" + "F+" = "XI-F+").
 */
protected function resolveJenjangCode(SchoolClass $schoolClass): string
{
    $fase = $schoolClass->fase ?: ($schoolClass->jenjang === 'X' ? 'E' : 'F');

    return "{$schoolClass->jenjang}-{$fase}";
}

    public function index(Request $request)
{
    $teacherId = Auth::guard('guru')->id();
    $selectedDate = $request->get('date', now()->timezone('Asia/Jakarta')->toDateString());
    $selectedDayName = $this->dayNameFromDate($selectedDate);

    $schedulesForDate = TeacherClass::with('schoolClass')
        ->where('teacher_id', $teacherId)
        ->where('day', $selectedDayName)
        ->orderBy('start_time')
        ->get();

    $filledClassIds = TeachingJournal::where('teacher_id', $teacherId)
        ->where('journal_date', $selectedDate)
        ->pluck('class_id')
        ->toArray();

    $filterClass = $request->get('class_id');
    $filterMonth = $request->get('month', now()->format('Y-m'));

    $journals = TeachingJournal::with('schoolClass')
        ->where('teacher_id', $teacherId)
        ->when($filterClass, fn($q) => $q->where('class_id', $filterClass))
        ->whereRaw("DATE_FORMAT(journal_date, '%Y-%m') = ?", [$filterMonth])
        ->orderByDesc('journal_date')
        ->get();

    $myClasses = SchoolClass::whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
        ->orderBy('name')->get();

    return view('guru.jurnal.index', [
        'schedulesForDate' => $schedulesForDate,
        'selectedDate' => $selectedDate,
        'selectedDayName' => $selectedDayName,
        'filledClassIds' => $filledClassIds,
        'journals' => $journals,
        'myClasses' => $myClasses,
        'filterClass' => $filterClass,
        'filterMonth' => $filterMonth,
    ] + $this->nav());
}

protected function dayNameFromDate(string $date): string
{
    $map = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $englishDay = \Carbon\Carbon::parse($date)->format('l');
    return $map[$englishDay];
}

    public function create(Request $request, TeacherClass $teacherClass)
    {
    abort_if($teacherClass->teacher_id !== Auth::guard('guru')->id(), 403);

    $date = $request->query('date');
    if (!$date) {
        $date = now()->timezone('Asia/Jakarta')->toDateString();
    }

    $existing = TeachingJournal::where('class_id', $teacherClass->class_id)
        ->where('journal_date', $date)
        ->first();

    if ($existing) {
        return redirect()->route('guru.jurnal.edit', $existing);
    }

    $students = $teacherClass->schoolClass->students()->orderBy('name')->get();
    $topics = $this->topicsForClass($teacherClass->schoolClass);

    return view('guru.jurnal.create', [
        'teacherClass' => $teacherClass,
        'date' => $date,
        'students' => $students,
        'topics' => $topics,
    ] + $this->nav());
    }

    public function store(Request $request, TeacherClass $teacherClass)
    {
        abort_if($teacherClass->teacher_id !== Auth::guard('guru')->id(), 403);

        $data = $request->validate([
            'journal_date' => 'required|date',
            'materi' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string',
            'attendance' => 'nullable|array',
            'attendance.*' => 'in:Hadir,Sakit,Izin,Alpa',
        ]);

        $journal = TeachingJournal::create([
            'teacher_id' => $teacherClass->teacher_id,
            'class_id' => $teacherClass->class_id,
            'journal_date' => $data['journal_date'],
            'materi' => $data['materi'] ?? null,
            'kegiatan' => $data['kegiatan'] ?? null,
        ]);

        $students = $teacherClass->schoolClass->students;
        foreach ($students as $student) {
            JournalAttendance::create([
                'teaching_journal_id' => $journal->id,
                'student_id' => $student->id,
                'status' => $data['attendance'][$student->id] ?? 'Hadir',
            ]);
        }

        return redirect()->route('guru.jurnal.index')->with('status', 'Jurnal mengajar berhasil disimpan.');
    }

    public function edit(TeachingJournal $teachingJournal)
    {
        abort_if($teachingJournal->teacher_id !== Auth::guard('guru')->id(), 403);

        $teachingJournal->load('schoolClass', 'attendances.student');
        $topics = $this->topicsForClass($teachingJournal->schoolClass);

        return view('guru.jurnal.edit', [
            'journal' => $teachingJournal,
            'topics' => $topics,
        ] + $this->nav());
    }

    public function update(Request $request, TeachingJournal $teachingJournal)
    {
        abort_if($teachingJournal->teacher_id !== Auth::guard('guru')->id(), 403);

        $data = $request->validate([
            'materi' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string',
            'attendance' => 'nullable|array',
            'attendance.*' => 'in:Hadir,Sakit,Izin,Alpa',
        ]);

        $teachingJournal->update([
            'materi' => $data['materi'] ?? null,
            'kegiatan' => $data['kegiatan'] ?? null,
        ]);

        foreach ($data['attendance'] ?? [] as $studentId => $status) {
            JournalAttendance::updateOrCreate(
                ['teaching_journal_id' => $teachingJournal->id, 'student_id' => $studentId],
                ['status' => $status]
            );
        }

        return redirect()->route('guru.jurnal.index')->with('status', 'Jurnal berhasil diperbarui.');
    }

    public function attendance(Request $request)
    {
        $teacherId = Auth::guard('guru')->id();

        $myClasses = SchoolClass::whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
            ->orderBy('name')->get();

        $filterClass = $request->get('class_id') ?: ($myClasses->first()->id ?? null);
        $filterMonth = $request->get('month', now()->format('Y-m'));

        $summary = [];
        $totalPertemuan = 0;

        if ($filterClass) {
            $class = SchoolClass::with('students')->find($filterClass);

            $journalIds = TeachingJournal::where('teacher_id', $teacherId)
                ->where('class_id', $filterClass)
                ->whereRaw("DATE_FORMAT(journal_date, '%Y-%m') = ?", [$filterMonth])
                ->pluck('id');

            $totalPertemuan = $journalIds->count();

            if ($class) {
                foreach ($class->students as $student) {
                    $counts = JournalAttendance::whereIn('teaching_journal_id', $journalIds)
                        ->where('student_id', $student->id)
                        ->selectRaw('status, count(*) as total')
                        ->groupBy('status')
                        ->pluck('total', 'status');

                    $hadir = (int) ($counts['Hadir'] ?? 0);
                    $sakit = (int) ($counts['Sakit'] ?? 0);
                    $izin = (int) ($counts['Izin'] ?? 0);
                    $alpa = (int) ($counts['Alpa'] ?? 0);
                    $tercatat = $hadir + $sakit + $izin + $alpa;

                    $summary[] = [
                        'student' => $student,
                        'hadir' => $hadir,
                        'sakit' => $sakit,
                        'izin' => $izin,
                        'alpa' => $alpa,
                        'persentase' => $tercatat > 0 ? round(($hadir / $tercatat) * 100, 1) : 0,
                    ];
                }

                usort($summary, fn($a, $b) => strcmp($a['student']->name, $b['student']->name));
            }
        }

        return view('guru.jurnal.kehadiran', [
            'myClasses' => $myClasses,
            'filterClass' => $filterClass,
            'filterMonth' => $filterMonth,
            'summary' => $summary,
            'totalPertemuan' => $totalPertemuan,
        ] + $this->nav());
    }

   public function printPdf(Request $request)
{
    $teacherId = Auth::guard('guru')->id();
    $teacher = Auth::guard('guru')->user();

    $classId = $request->get('class_id'); // null/kosong berarti "semua kelas"
    $month = $request->get('month', now()->format('Y-m'));

    $letterhead = Letterhead::current();
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');

    if ($classId) {
        // Cetak 1 kelas saja (perilaku lama)
        $class = SchoolClass::findOrFail($classId);

        $journals = TeachingJournal::with('attendances.student')
            ->where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->whereRaw("DATE_FORMAT(journal_date, '%Y-%m') = ?", [$month])
            ->orderBy('journal_date')
            ->get();

        $groupedByClass = collect([
            ['class' => $class, 'journals' => $journals],
        ]);

        $filename = 'Jurnal-' . str_replace(' ', '-', $class->name) . '-' . $month . '.pdf';
    } else {
        // Cetak SEMUA kelas guru ini dalam bulan tersebut
        $myClasses = SchoolClass::whereHas('teachers', fn($q) => $q->where('teacher_id', $teacherId))
            ->orderBy('name')->get();

        $groupedByClass = $myClasses->map(function ($class) use ($teacherId, $month) {
            $journals = TeachingJournal::with('attendances.student')
                ->where('teacher_id', $teacherId)
                ->where('class_id', $class->id)
                ->whereRaw("DATE_FORMAT(journal_date, '%Y-%m') = ?", [$month])
                ->orderBy('journal_date')
                ->get();

            return ['class' => $class, 'journals' => $journals];
        })->filter(fn($g) => $g['journals']->count() > 0)->values();

        $filename = 'Jurnal-Semua-Kelas-' . str_replace(' ', '-', $teacher->name) . '-' . $month . '.pdf';
    }

    $pdf = Pdf::loadView('guru.jurnal.print', [
        'teacher' => $teacher,
        'groupedByClass' => $groupedByClass,
        'letterhead' => $letterhead,
        'monthLabel' => $monthLabel,
        'isAllClasses' => !$classId,
    ])->setPaper('a4', 'portrait');

    return $pdf->stream($filename);
}
}