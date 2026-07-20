<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\DigitalLesson;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruDigitalLessonController extends Controller
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
        $lessons = DigitalLesson::where('uploaded_by_type', 'teacher')->where('uploaded_by_id', $teacherId)->latest()->get();
        return view('guru.pembelajaran-digital.index', ['lessons' => $lessons] + $this->nav());
    }

    public function create()
    {
        return view('guru.pembelajaran-digital.create', ['topics' => MathTopics::TOPICS, 'jenjangList' => MathTopics::JENJANG] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'topic' => 'required|string',
            'embed_url' => 'required|url',
        ]);

        DigitalLesson::create($data + [
            'uploaded_by_type' => 'teacher',
            'uploaded_by_id' => Auth::guard('guru')->id(),
        ]);

        return redirect()->route('guru.pembelajaran-digital.index')->with('status', 'Media pembelajaran berhasil ditambahkan.');
    }

    public function destroy(DigitalLesson $pembelajaran_digital)
    {
        abort_if($pembelajaran_digital->uploaded_by_type !== 'teacher' || $pembelajaran_digital->uploaded_by_id !== Auth::guard('guru')->id(), 403);
        $pembelajaran_digital->delete();
        return back()->with('status', 'Media berhasil dihapus.');
    }
}