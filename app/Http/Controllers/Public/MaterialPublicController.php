<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\DigitalLesson;
use App\Models\MaterialTopic;
use App\Support\MathTopics;
use Illuminate\Http\Request;

class MaterialPublicController extends Controller
{
    public function index(Request $request)
    {
        $jenjang = $request->get('jenjang', 'X-E');
        $semester = $request->get('semester', 'Ganjil');

        $topicsByJenjangSemester = MaterialTopic::orderBy('order_index')
            ->get()
            ->groupBy('jenjang')
            ->map(fn($group) => $group->groupBy('semester'));

        $materialCountByJenjang = Material::selectRaw('jenjang, count(*) as total')
            ->groupBy('jenjang')
            ->pluck('total', 'jenjang');

        $materials = Material::where('jenjang', $jenjang)
            ->where('semester', $semester)
            ->latest()
            ->get();

        return view('public.materials.index', [
            'materials' => $materials,
            'topicsByJenjangSemester' => $topicsByJenjangSemester,
            'materialCountByJenjang' => $materialCountByJenjang,
            'jenjang' => $jenjang,
            'semester' => $semester,
            'jenjangList' => MathTopics::JENJANG,
        ]);
    }

    public function preview(Material $material)
    {
        return response()->file(storage_path('app/public/' . $material->file_path));
    }

    public function digitalLessons(Request $request)
    {
        $jenjang = $request->get('jenjang', 'X-E');
        $topic = $request->get('topic');

        $lessons = DigitalLesson::where('jenjang', $jenjang)
            ->when($topic, fn($q) => $q->where('topic', $topic))
            ->latest()
            ->get();

        return view('public.digital-lessons.index', [
            'lessons' => $lessons,
            'jenjang' => $jenjang,
            'topic' => $topic,
            'jenjangList' => MathTopics::JENJANG,
            'topics' => MathTopics::TOPICS,
        ]);
    }
}