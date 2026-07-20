<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruMaterialController extends Controller
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
        $teacherId = Auth::guard('guru')->id();
        $materials = Material::where('uploaded_by_type', 'teacher')
            ->where('uploaded_by_id', $teacherId)
            ->latest()->get();

        return view('guru.materi.index', ['materials' => $materials] + $this->nav());
    }

    public function create()
{
    return view('guru.materi.create', ['jenjangList' => \App\Support\MathTopics::JENJANG] + $this->nav());
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
        'semester' => 'required|in:Ganjil,Genap',
        'file' => 'required|file|mimes:pdf|max:51200', // 50MB
    ]);

    $file = $request->file('file');
    $path = $file->store('materials', 'public');

    Material::create([
        'title' => $data['title'],
        'jenjang' => $data['jenjang'],
        'semester' => $data['semester'],
        'file_path' => $path,
        'file_size' => $file->getSize(),
        'uploaded_by_type' => 'teacher',
        'uploaded_by_id' => Auth::guard('guru')->id(),
    ]);

    return redirect()->route('guru.materi.index')->with('status', 'Materi berhasil diunggah.');
}

    public function destroy(Material $materi)
    {
        // Guru hanya bisa hapus materi miliknya sendiri
        abort_if($materi->uploaded_by_type !== 'teacher' || $materi->uploaded_by_id !== Auth::guard('guru')->id(), 403);

        \Storage::disk('public')->delete($materi->file_path);
        $materi->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }
}