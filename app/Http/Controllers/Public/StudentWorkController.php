<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StudentWork;
use App\Models\StudentWorkComment;
use App\Support\ReportReasons;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentWorkController extends Controller
{
    public function index(Request $request)
    {
        $works = StudentWork::where('status', 'approved')
            ->withCount('likes')
            ->latest()
            ->get();

        $identifier = $this->identifier($request);

        return view('public.student-works.index', [
            'works' => $works,
            'identifier' => $identifier,
            'reasons' => ReportReasons::LIST,
        ]);
    }

    public function create()
    {
        return view('public.student-works.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,pdf|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        $fileType = match (true) {
            in_array($ext, ['jpg', 'jpeg', 'png']) => 'image',
            $ext === 'mp4' => 'video',
            $ext === 'pdf' => 'pdf',
            default => 'image',
        };

        $path = $file->store('student-works', 'public');

        StudentWork::create([
            'student_name' => $data['student_name'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_type' => $fileType,
            'status' => 'pending',
        ]);

        return redirect()->route('student-works.public')
            ->with('status', 'Karya berhasil diunggah! Menunggu persetujuan Admin sebelum tayang ke publik.');
    }

    public function like(Request $request, StudentWork $studentWork)
    {
    $identifier = $this->identifier($request);

    $existing = $studentWork->likes()->where('liker_identifier', $identifier)->first();

    if ($existing) {
        $existing->delete();
        $liked = false;
    } else {
        $studentWork->likes()->create(['liker_identifier' => $identifier]);
        $liked = true;
    }

    if ($request->wantsJson()) {
        return response()->json([
            'liked' => $liked,
            'likes_count' => $studentWork->likes()->count(),
        ]);
    }

    return back();
    }

    public function comment(Request $request, StudentWork $studentWork)
    {
    $data = $request->validate([
        'commenter_name' => 'required|string|max:100',
        'content' => 'required|string|max:500',
        'parent_id' => 'nullable|exists:student_work_comments,id',
    ]);

    $studentWork->comments()->create($data);

    return back()->with('status', 'Komentar berhasil dikirim.');
    }

    public function report(Request $request, StudentWork $studentWork)
    {
        $data = $request->validate([
            'reason' => 'required|string|in:' . implode(',', ReportReasons::LIST),
            'reporter_name' => 'nullable|string|max:100',
        ]);

        Report::create([
            'reportable_type' => StudentWork::class,
            'reportable_id' => $studentWork->id,
            'reporter_name' => $data['reporter_name'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Laporan berhasil dikirim, terima kasih. Admin akan meninjau konten ini.');
    }

    public function reportComment(Request $request, StudentWorkComment $comment)
    {
        $data = $request->validate([
            'reason' => 'required|string|in:' . implode(',', ReportReasons::LIST),
            'reporter_name' => 'nullable|string|max:100',
        ]);

        Report::create([
            'reportable_type' => StudentWorkComment::class,
            'reportable_id' => $comment->id,
            'reporter_name' => $data['reporter_name'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Laporan komentar berhasil dikirim.');
    }

    // Identitas pengunjung berbasis session (bukan akun) — dipakai untuk cegah like berulang
    protected function identifier(Request $request): string
    {
        if (!$request->session()->has('student_identifier')) {
            $request->session()->put('student_identifier', (string) Str::uuid());
        }
        return $request->session()->get('student_identifier');
    }
}