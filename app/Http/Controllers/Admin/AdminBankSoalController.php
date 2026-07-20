<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Support\MathTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminBankSoalController extends Controller
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
        $files = QuestionBank::where('type', 'file')->latest()->get();
        return view('admin.bank-soal.index', ['files' => $files] + $this->nav());
    }

    public function create()
    {
        return view('admin.bank-soal.create', ['jenjangList' => MathTopics::JENJANG] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'topic' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $path = $request->file('file')->store('question-bank', 'public');

        QuestionBank::create([
            'type' => 'file',
            'jenjang' => $data['jenjang'],
            'topic' => $data['topic'],
            'title' => $data['title'],
            'file_path' => $path,
            'uploaded_by_type' => 'admin',
            'uploaded_by_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.bank-soal.index')->with('status', 'Soal berhasil diunggah.');
    }

    public function destroy(QuestionBank $bankSoal)
    {
        Storage::disk('public')->delete($bankSoal->file_path);
        $bankSoal->delete();
        return back()->with('status', 'Soal berhasil dihapus.');
    }
}