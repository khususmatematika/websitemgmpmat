<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\Report;
use App\Support\ReportReasons;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $posts = ForumPost::withCount('likes')
            ->latest()
            ->get();

        $identifier = $this->identifier($request);

        return view('public.forum.index', [
            'posts' => $posts,
            'identifier' => $identifier,
            'reasons' => ReportReasons::LIST,
        ]);
    }

    public function create()
    {
        return view('public.forum.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'author_name' => 'required|string|max:100',
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120', // 5MB
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('forum', 'public');
        }

        ForumPost::create([
            'author_name' => $data['author_name'],
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null,
        ]);

        return redirect()->route('forum.public')->with('status', 'Postingan berhasil dibuat.');
    }

    public function comment(Request $request, ForumPost $post)
    {
    $data = $request->validate([
        'commenter_name' => 'required|string|max:100',
        'content' => 'required|string|max:500',
        'parent_id' => 'nullable|exists:forum_comments,id',
    ]);

    $comment = $post->comments()->create($data);

    if ($request->wantsJson()) {
        return response()->json([
            'status' => 'ok',
            'total_comments' => $post->totalCommentCount(),
        ]);
    }

    return back()->with('status', 'Komentar berhasil dikirim.');
    }

    public function likePost(Request $request, ForumPost $post)
    {
        return $this->toggleLike($request, $post);
    }

    public function likeComment(Request $request, ForumComment $comment)
    {
        return $this->toggleLike($request, $comment);
    }

    protected function toggleLike(Request $request, $model)
    {
        $identifier = $this->identifier($request);
        $existing = $model->likes()->where('liker_identifier', $identifier)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $model->likes()->create(['liker_identifier' => $identifier]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $model->likes()->count(),
        ]);
    }

    public function reportPost(Request $request, ForumPost $post)
    {
        $data = $request->validate([
            'reason' => 'required|string|in:' . implode(',', ReportReasons::LIST),
            'reporter_name' => 'nullable|string|max:100',
        ]);

        Report::create([
            'reportable_type' => ForumPost::class,
            'reportable_id' => $post->id,
            'reporter_name' => $data['reporter_name'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Laporan berhasil dikirim, terima kasih.');
    }

    public function reportComment(Request $request, ForumComment $comment)
    {
        $data = $request->validate([
            'reason' => 'required|string|in:' . implode(',', ReportReasons::LIST),
            'reporter_name' => 'nullable|string|max:100',
        ]);

        Report::create([
            'reportable_type' => ForumComment::class,
            'reportable_id' => $comment->id,
            'reporter_name' => $data['reporter_name'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Laporan komentar berhasil dikirim.');
    }

    // Identitas sama dengan Karya Siswa — 1 identitas per browser dipakai lintas fitur
    protected function identifier(Request $request): string
    {
        if (!$request->session()->has('student_identifier')) {
            $request->session()->put('student_identifier', (string) Str::uuid());
        }
        return $request->session()->get('student_identifier');
    }
}