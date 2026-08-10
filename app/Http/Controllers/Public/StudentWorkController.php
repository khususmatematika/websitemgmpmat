<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StudentWork;
use App\Models\StudentWorkComment;
use App\Support\ActiveActor;
use Illuminate\Http\Request;

class StudentWorkController extends Controller
{
    public function index(Request $request)
    {
        $works = StudentWork::where('status', 'approved')
            ->withCount('likes')
            ->latest()
            ->get();

        $actor = ActiveActor::current();

        return view('public.student-works.index', [
            'works' => $works,
            'identifier' => $actor['identifier'] ?? 'guest:' . session()->getId(),
            'reasons' => \App\Support\ReportReasons::LIST,
            'isLoggedIn' => $actor !== null,
        ]);
    }

    public function create(Request $request)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        return view('public.student-works.create', ['actor' => ActiveActor::current()]);
    }

    public function store(Request $request)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,pdf|max:20480',
        ]);

        $actor = ActiveActor::current();

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
            'student_name' => $actor['name'],
            'actor_type' => $actor['type'],
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
        if ($resp = $this->requireLogin($request, true)) return $resp;

        $identifier = ActiveActor::current()['identifier'];
        $existing = $studentWork->likes()->where('liker_identifier', $identifier)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $studentWork->likes()->create(['liker_identifier' => $identifier]);
        }

        return response()->json([
            'liked' => !$existing,
            'likes_count' => $studentWork->likes()->count(),
        ]);
    }

    public function comment(Request $request, StudentWork $studentWork)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        $data = $request->validate([
            'content' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:student_work_comments,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if (empty($data['content']) && !$request->hasFile('image')) {
            return back()->withErrors(['content' => 'Isi komentar atau lampirkan foto.']);
        }

        $actor = ActiveActor::current();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comment-images', 'public');
        }

        $studentWork->comments()->create([
            'commenter_name' => $actor['name'],
            'actor_type' => $actor['type'],
            'content' => $data['content'] ?? '',
            'image_path' => $imagePath,
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('status', 'Komentar berhasil dikirim.');
    }

    public function report(Request $request, StudentWork $studentWork)
    {
        $data = $request->validate([
            'reason' => 'required|string|in:' . implode(',', \App\Support\ReportReasons::LIST),
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
            'reason' => 'required|string|in:' . implode(',', \App\Support\ReportReasons::LIST),
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

    protected function requireLogin(Request $request, bool $forceJson = false)
    {
        if (ActiveActor::isLoggedIn()) {
            return null;
        }

        if ($forceJson || $request->wantsJson()) {
            return response()->json([
                'error' => 'login_required',
                'message' => 'Silakan masuk terlebih dahulu untuk melanjutkan.',
                'login_url' => route('login.select'),
            ], 401);
        }

        return redirect()->route('login.select')->with('error', 'Silakan masuk terlebih dahulu.');
    }
}