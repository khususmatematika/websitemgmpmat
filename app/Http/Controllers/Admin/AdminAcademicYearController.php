<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAcademicYearController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\AdminNav::items(),
            'guard' => 'admin',
            'panelTitle' => 'Panel Admin',
        ];
    }

    public function index()
    {
        $periods = AcademicYear::orderByDesc('start_date')->orderByDesc('id')->get()->map(function ($p) {
            $p->journals_count = DB::table('teaching_journals')->where('academic_year_id', $p->id)->count();
            $p->components_count = DB::table('assessment_components')->where('academic_year_id', $p->id)->count();
            return $p;
        });

        return view('admin.tahun-ajaran.index', [
            'periods' => $periods,
            'active' => AcademicYear::active(),
        ] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => 'required|in:Ganjil,Genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'name.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
        ]);

        if (AcademicYear::where('name', $data['name'])->where('semester', $data['semester'])->exists()) {
            return back()->withErrors(['name' => 'Periode ini sudah ada.'])->withInput();
        }

        AcademicYear::create($data + ['is_active' => false]);

        return back()->with('status', 'Periode ditambahkan. Klik "Aktifkan" untuk menjadikannya periode berjalan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $academicYear->update($data);

        return back()->with('status', 'Tanggal periode diperbarui.');
    }

    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        return back()->with('status', "Periode aktif sekarang: {$academicYear->label}.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Periode aktif tidak bisa dihapus. Aktifkan periode lain dulu.');
        }

        $hasData = DB::table('teaching_journals')->where('academic_year_id', $academicYear->id)->exists()
            || DB::table('assessment_components')->where('academic_year_id', $academicYear->id)->exists();

        if ($hasData) {
            return back()->with('error', 'Periode ini sudah berisi jurnal/nilai dan dipertahankan sebagai arsip.');
        }

        $academicYear->delete();

        return back()->with('status', 'Periode dihapus.');
    }

    public function promoteForm()
    {
        $classes = SchoolClass::withCount('students')->orderBy('jenjang')->orderBy('name')->get();

        return view('admin.tahun-ajaran.promote', ['classes' => $classes] + $this->nav());
    }

    public function promote(Request $request)
    {
        $moves = collect($request->input('moves', []))
            ->filter(fn($target) => $target !== null && $target !== '' && $target !== 'keep');

        if ($moves->isEmpty()) {
            return back()->with('error', 'Belum ada kelas yang dipilih untuk dipindahkan.');
        }

        // Snapshot dulu supaya pemindahan berantai (X->XI, XI->XII) tidak bertabrakan
        $plan = [];
        foreach ($moves as $sourceId => $target) {
            $targetId = $target === 'graduate' ? null : (int) $target;
            if ($targetId !== null && !SchoolClass::whereKey($targetId)->exists()) {
                continue;
            }
            foreach (DB::table('class_student')->where('class_id', $sourceId)->pluck('student_id') as $sid) {
                $plan[] = [(int) $sid, (int) $sourceId, $targetId];
            }
        }

        $moved = 0;
        $released = 0;

        DB::transaction(function () use ($plan, &$moved, &$released) {
            foreach ($plan as [$sid, $src, $tgt]) {
                DB::table('class_student')->where('student_id', $sid)->where('class_id', $src)->delete();
            }
            foreach ($plan as [$sid, $src, $tgt]) {
                if ($tgt === null) {
                    $released++;
                    continue;
                }
                if (!DB::table('class_student')->where('student_id', $sid)->where('class_id', $tgt)->exists()) {
                    DB::table('class_student')->insert(['student_id' => $sid, 'class_id' => $tgt]);
                }
                $moved++;
            }
        });

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('status', "Naik kelas selesai: {$moved} siswa dipindahkan, {$released} siswa dilepas dari kelas (lulus).");
    }
}