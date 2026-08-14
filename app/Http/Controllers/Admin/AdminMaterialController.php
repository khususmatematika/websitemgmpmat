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
        $materials = Material::latest()->get();
        return view('admin.materi.index', ['materials' => $materials] + $this->nav());
    }

    public function create()
    {
        return view('admin.materi.create', ['jenjangList' => MathTopics::JENJANG] + $this->nav());
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
        Storage::disk('public')->delete($materi->file_path);
        if ($materi->cover_path) {
            Storage::disk('public')->delete($materi->cover_path);
        }
        $materi->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }

    public function saveCover(Request $request, Material $materi)
    {
        $data = $request->validate([
            'cover_data' => 'required|string',
        ]);

        if (!str_starts_with($data['cover_data'], 'data:image')) {
            return response()->json(['status' => 'error'], 422);
        }

        if ($materi->cover_path) {
            Storage::disk('public')->delete($materi->cover_path);
        }

        $imageData = substr($data['cover_data'], strpos($data['cover_data'], ',') + 1);
        $imageData = base64_decode($imageData);
        $coverPath = 'materials-cover/' . uniqid() . '.jpg';
        Storage::disk('public')->put($coverPath, $imageData);

        $materi->update(['cover_path' => $coverPath]);

        return response()->json(['status' => 'ok', 'cover_url' => asset('storage/' . $coverPath)]);
    }
}