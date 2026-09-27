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
        return view('admin.pembelajaran-digital.create', ['jenjangList' => MathTopics::JENJANG] + $this->nav());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'input_type' => 'required|in:url,code',
        ]);

        $inputType = $request->input('input_type');

        if ($inputType === 'url') {
            $request->validate(['embed_url' => 'required|url']);
        } else {
            $request->validate(['embed_code' => 'required|string']);
        }

        DigitalLesson::create([
            'title' => $request->title,
            'jenjang' => $request->jenjang,
            'input_type' => $inputType,
            'embed_url' => $inputType === 'url' ? $request->embed_url : null,
            'embed_code' => $inputType === 'code' ? $request->embed_code : null,
            'uploaded_by_type' => 'admin',
            'uploaded_by_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.pembelajaran-digital.index')->with('status', 'Media berhasil ditambahkan.');
    }

    public function edit(DigitalLesson $pembelajaran_digital)
    {
        return view('admin.pembelajaran-digital.edit', [
            'lesson' => $pembelajaran_digital,
            'jenjangList' => MathTopics::JENJANG,
        ] + $this->nav());
    }

    public function update(Request $request, DigitalLesson $pembelajaran_digital)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'input_type' => 'required|in:url,code',
        ]);

        $inputType = $request->input('input_type');

        if ($inputType === 'url') {
            $request->validate(['embed_url' => 'required|url']);
        } else {
            $request->validate(['embed_code' => 'required|string']);
        }

        $pembelajaran_digital->update([
            'title' => $request->title,
            'jenjang' => $request->jenjang,
            'input_type' => $inputType,
            'embed_url' => $inputType === 'url' ? $request->embed_url : null,
            'embed_code' => $inputType === 'code' ? $request->embed_code : null,
        ]);

        return redirect()->route('admin.pembelajaran-digital.index')->with('status', 'Media berhasil diperbarui.');
    }

    public function destroy(DigitalLesson $pembelajaran_digital)
    {
        $pembelajaran_digital->delete();
        return back()->with('status', 'Media berhasil dihapus.');
    }
}