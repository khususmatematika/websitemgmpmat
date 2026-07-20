<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminMaterialController extends Controller
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
        // Admin melihat SEMUA materi, dari guru manapun maupun upload admin sendiri
        $materials = Material::latest()->get();
        return view('admin.materi.index', ['materials' => $materials] + $this->nav());
    }

    public function create()
{
    return view('admin.materi.create', ['jenjangList' => \App\Support\MathTopics::JENJANG] + $this->nav());
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
        'semester' => 'required|in:Ganjil,Genap',
        'file' => 'required|file|mimes:pdf|max:51200',
    ]);

    $file = $request->file('file');
    $path = $file->store('materials', 'public');

    Material::create([
        'title' => $data['title'],
        'jenjang' => $data['jenjang'],
        'semester' => $data['semester'],
        'file_path' => $path,
        'file_size' => $file->getSize(),
        'uploaded_by_type' => 'admin',
        'uploaded_by_id' => Auth::guard('admin')->id(),
    ]);

    return redirect()->route('admin.materi.index')->with('status', 'Materi berhasil diunggah.');
}

    public function destroy(Material $materi)
    {
        // Admin boleh hapus materi siapa saja, tanpa cek kepemilikan
        Storage::disk('public')->delete($materi->file_path);
        $materi->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }
}