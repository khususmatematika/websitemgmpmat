<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\Report;
use App\Support\ActiveActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $posts = ForumPost::withCount('likes')->latest()->get();
        $actor = ActiveActor::current();

        return view('public.forum.index', [
            'posts' => $posts,
            'identifier' => $actor['identifier'] ?? 'guest:' . session()->getId(),
            'reasons' => \App\Support\ReportReasons::LIST,
            'isLoggedIn' => $actor !== null,
            'currentActor' => $actor,
            'isAdmin' => Auth::guard('admin')->check(),
        ]);
    }

    public function create(Request $request)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        return view('public.forum.create', ['actor' => ActiveActor::current()]);
    }

    public function store(Request $request)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        $data = $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120',
        ]);

        $actor = ActiveActor::current();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('forum', 'public');
        }

        ForumPost::create([
            'author_name' => $actor['name'],
            'actor_type' => $actor['type'],
            'owner_id' => $actor['id'],
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null,
        ]);

        return redirect()->route('forum.public')->with('status', 'Postingan berhasil dibuat.');
    }

    public function editPost(Request $request, ForumPost $post)
    {
        if ($resp = $this->requireOwnerOrRedirect($request, $post)) return $resp;

        return view('public.forum.edit-post', ['post' => $post]);
    }

    public function updatePost(Request $request, ForumPost $post)
    {
        if ($resp = $this->requireOwner($request, $post, true)) return $resp;

        $data = $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('forum', 'public');
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        $post->update([
            'content' => $data['content'],
            'image_path' => array_key_exists('image_path', $data) ? $data['image_path'] : $post->image_path,
        ]);

        return redirect()->route('forum.public')->with('status', 'Postingan berhasil diperbarui.');
    }

    public function destroyPost(Request $request, ForumPost $post)
    {
        $isAdmin = Auth::guard('admin')->check();

        if (!$isAdmin) {
            if ($resp = $this->requireOwner($request, $post, true)) return $resp;
        }

        $post->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->route('forum.public')->with('status', 'Postingan berhasil dihapus.');
    }

    public function comment(Request $request, ForumPost $post)
    {
        if ($resp = $this->requireLogin($request, $request->wantsJson())) return $resp;

        $data = $request->validate([
            'content' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:forum_comments,id',
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

        $post->comments()->create([
            'commenter_name' => $actor['name'],
            'actor_type' => $actor['type'],
            'owner_id' => $actor['id'],
            'content' => $data['content'] ?? '',
            'image_path' => $imagePath,
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'total_comments' => $post->totalCommentCount()]);
        }

        return back()->with('status', 'Komentar berhasil dikirim.');
    }

    public function updateComment(Request $request, ForumComment $comment)
    {
        if ($resp = $this->requireOwner($request, $comment, true)) return $resp;

        $data = $request->validate([
            'content' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $imagePath = $comment->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comment-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            $imagePath = null;
        }

        $comment->update([
            'content' => $data['content'] ?? '',
            'image_path' => $imagePath,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Komentar berhasil diperbarui.');
    }

    public function destroyComment(Request $request, ForumComment $comment)
    {
        $isAdmin = Auth::guard('admin')->check();

        if (!$isAdmin) {
            if ($resp = $this->requireOwner($request, $comment, true)) return $resp;
        }

        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Komentar berhasil dihapus.');
    }

    public function likePost(Request $request, ForumPost $post)
    {
        if ($resp = $this->requireLogin($request, true)) return $resp;
        return $this->toggleLike($post);
    }

    public function likeComment(Request $request, ForumComment $comment)
    {
        if ($resp = $this->requireLogin($request, true)) return $resp;
        return $this->toggleLike($comment);
    }

    protected function toggleLike($model)
    {
        $identifier = ActiveActor::current()['identifier'];
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
            'reason' => 'required|string|in:' . implode(',', \App\Support\ReportReasons::LIST),
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
            'reason' => 'required|string|in:' . implode(',', \App\Support\ReportReasons::LIST),
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

    protected function requireLogin(Request $request, bool $forceJson = false)
    {
        if (ActiveActor::isLoggedIn()) {
            return null;
        }

        if ($forceJson || $request->wantsJson()) {
            return response()->json([
                'error' => 'login_required',
                'message' => 'Silakan masuk terlebih dahulu untuk melanjutkan.',
                'login_url' => route('login'),
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
    }

    protected function isOwner($model): bool
    {
        $actor = ActiveActor::current();
        if (!$actor) return false;

        return (string) $model->owner_id === (string) $actor['id']
            && $model->actor_type === $actor['type'];
    }

    protected function requireOwner(Request $request, $model, bool $forceJson = false)
    {
        if ($resp = $this->requireLogin($request, $forceJson)) return $resp;

        if (!$this->isOwner($model)) {
            if ($forceJson || $request->wantsJson()) {
                return response()->json(['error' => 'forbidden', 'message' => 'Anda tidak berhak mengubah konten ini.'], 403);
            }
            abort(403, 'Anda tidak berhak mengubah konten ini.');
        }

        return null;
    }

    protected function requireOwnerOrRedirect(Request $request, $model)
    {
        if ($resp = $this->requireLogin($request)) return $resp;

        if (!$this->isOwner($model)) {
            return redirect()->route('forum.public')->with('error', 'Anda tidak berhak mengedit postingan ini.');
        }

        return null;
    }
}