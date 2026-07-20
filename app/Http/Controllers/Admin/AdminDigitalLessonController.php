<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalLesson;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDigitalLessonController extends Controller
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
        $lessons = DigitalLesson::latest()->get();
        return view('admin.pembelajaran-digital.index', ['lessons' => $lessons] + $this->nav());
    }

    public function create()
    {
        return view('admin.pembelajaran-digital.create', ['topics' => MathTopics::TOPICS, 'jenjangList' => MathTopics::JENJANG] + $this->nav());
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
            'uploaded_by_type' => 'admin',
            'uploaded_by_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.pembelajaran-digital.index')->with('status', 'Media pembelajaran berhasil ditambahkan.');
    }

    public function destroy(DigitalLesson $pembelajaran_digital)
    {
        $pembelajaran_digital->delete();
        return back()->with('status', 'Media berhasil dihapus.');
    }
}