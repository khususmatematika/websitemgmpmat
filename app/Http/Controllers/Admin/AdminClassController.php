<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AdminClassController extends Controller
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
        $classes = SchoolClass::withCount('students')->orderBy('jenjang')->orderBy('name')->get();
        return view('admin.kelas.index', ['classes' => $classes] + $this->nav());
    }

    public function create()
    {
        return view('admin.kelas.create', $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:classes,name',
            'jenjang' => 'required|in:X,XI,XII',
            'fase' => 'required|in:E,F,F+',
        ]);

        SchoolClass::create($data);

        return redirect()->route('admin.kelas.index')->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $kelas)
    {
        return view('admin.kelas.edit', ['kelas' => $kelas] + $this->nav());
    }

    public function update(Request $request, SchoolClass $kelas)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:classes,name,' . $kelas->id,
            'jenjang' => 'required|in:X,XI,XII',
            'fase' => 'required|in:E,F,F+',
        ]);

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $kelas)
    {
        $kelas->delete();
        return back()->with('status', 'Kelas berhasil dihapus.');
    }
}