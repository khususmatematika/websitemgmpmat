<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentExcelImporter;
use App\Services\StudentTemplateGenerator;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    protected function nav(): array
{
    return [
        'navItems' => \App\Support\AdminNav::items(),
        'guard' => 'admin',
        'panelTitle' => 'Panel Admin',
    ];
}

    public function index(Request $request)
    {
        $search = $request->get('search');

        $students = Student::with('classes')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.siswa.index', ['students' => $students, 'search' => $search] + $this->nav());
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.siswa.create', ['classes' => $classes] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nis' => 'nullable|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'class_ids' => 'nullable|array|max:2',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $student = Student::create(['nis' => $data['nis'] ?? null, 'name' => $data['name']]);

        if (!empty($data['class_ids'])) {
            $student->classes()->sync($data['class_ids']);
        }

        return redirect()->route('admin.siswa.index')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $siswa)
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.siswa.edit', ['student' => $siswa->load('classes'), 'classes' => $classes] + $this->nav());
    }

    public function update(Request $request, Student $siswa)
    {
        $data = $request->validate([
            'nis' => 'nullable|string|unique:students,nis,' . $siswa->id,
            'name' => 'required|string|max:255',
            'class_ids' => 'nullable|array|max:2',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $siswa->update(['nis' => $data['nis'] ?? null, 'name' => $data['name']]);
        $siswa->classes()->sync($data['class_ids'] ?? []);

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $siswa)
    {
        $siswa->delete();
        return back()->with('status', 'Siswa berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $generator = new StudentTemplateGenerator();
        $path = $generator->generate();

        return response()->download($path, 'template-import-siswa.xlsx')->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        $importer = new StudentExcelImporter();
        $importer->import($request->file('file')->getRealPath());

        $message = "{$importer->successCount} siswa berhasil diimpor.";
        if (!empty($importer->errors)) {
            $message .= ' Namun ada ' . count($importer->errors) . ' baris bermasalah.';
        }

        return redirect()->route('admin.siswa.index')
            ->with('status', $message)
            ->with('import_errors', $importer->errors);
    }
}