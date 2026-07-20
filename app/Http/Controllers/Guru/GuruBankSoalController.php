<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuruBankSoalController extends Controller
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
        $files = QuestionBank::where('type', 'file')
            ->where('uploaded_by_type', 'teacher')
            ->where('uploaded_by_id', $teacherId)
            ->latest()->get();

        return view('guru.bank-soal.index', ['files' => $files] + $this->nav());
    }

    public function create()
    {
        return view('guru.bank-soal.create', ['jenjangList' => MathTopics::JENJANG] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'topic' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ]);

        $path = $request->file('file')->store('question-bank', 'public');

        QuestionBank::create([
            'type' => 'file',
            'jenjang' => $data['jenjang'],
            'topic' => $data['topic'],
            'title' => $data['title'],
            'file_path' => $path,
            'uploaded_by_type' => 'teacher',
            'uploaded_by_id' => Auth::guard('guru')->id(),
        ]);

        return redirect()->route('guru.bank-soal.index')->with('status', 'Soal berhasil diunggah.');
    }

    public function destroy(QuestionBank $bankSoal)
    {
        abort_if($bankSoal->uploaded_by_type !== 'teacher' || $bankSoal->uploaded_by_id !== Auth::guard('guru')->id(), 403);

        Storage::disk('public')->delete($bankSoal->file_path);
        $bankSoal->delete();

        return back()->with('status', 'Soal berhasil dihapus.');
    }
}