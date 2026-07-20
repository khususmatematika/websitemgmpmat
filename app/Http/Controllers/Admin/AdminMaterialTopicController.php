<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialTopic;
use App\Support\MathTopics;
use Illuminate\Http\Request;

class AdminMaterialTopicController extends Controller
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
        $jenjang = $request->get('jenjang', 'X-E');
        $semester = $request->get('semester', 'Ganjil');

        $topics = MaterialTopic::where('jenjang', $jenjang)
            ->where('semester', $semester)
            ->orderBy('order_index')
            ->get();

        return view('admin.topik-materi.index', [
            'topics' => $topics,
            'jenjang' => $jenjang,
            'semester' => $semester,
            'jenjangList' => MathTopics::JENJANG,
        ] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenjang' => 'required|in:X-E,XI-F,XII-F,XI-F+,XII-F+',
            'semester' => 'required|in:Ganjil,Genap',
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = MaterialTopic::where('jenjang', $data['jenjang'])
            ->where('semester', $data['semester'])
            ->max('order_index');

        MaterialTopic::create($data + ['order_index' => ($maxOrder ?? 0) + 1]);

        return back()->with('status', 'Topik berhasil ditambahkan.');
    }

    public function destroy(MaterialTopic $topik_materi)
    {
        $topik_materi->delete();
        return back()->with('status', 'Topik berhasil dihapus.');
    }
}