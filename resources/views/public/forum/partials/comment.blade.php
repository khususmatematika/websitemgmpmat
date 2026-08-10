<div style="margin-left: {{ min($depth, 4) * 28 }}px" class="{{ $depth > 0 ? 'mt-2' : '' }}">
    <div class="flex justify-between items-start gap-2 rounded-lg p-3 border-l-4
                {{ $depth > 0 ? 'bg-surface-container border-math-teal/40' : 'bg-surface-container-low border-navy-deep/20' }}">
        <div class="flex-1 min-w-0">
            @if ($depth > 0 && $comment->parent)
                <p class="text-[11px] text-math-teal font-bold flex items-center gap-1 mb-1">
                    <span class="material-symbols-outlined text-[14px]">subdirectory_arrow_right</span>
                    Membalas {{ $comment->parent->commenter_name }}
                </p>
            @endif
            <p class="text-xs font-bold text-navy-deep flex items-center gap-1">
                {{ $comment->commenter_name }}
                @if ($comment->actor_type === 'teacher')
                <span class="bg-navy-deep text-white text-[9px] px-1.5 py-0.5 rounded-full">Guru</span>
                @endif
            </p>
            @if ($comment->content)
            <p class="text-sm text-on-surface">{{ $comment->content }}</p>
            @endif
            @if ($comment->image_path)
            <img src="{{ asset('storage/'.$comment->image_path) }}" class="mt-2 rounded-lg max-h-48 border border-outline-variant/50">
            @endif

            <div class="flex items-center gap-3 mt-2">
                <button type="button"
                        class="like-btn flex items-center gap-1 text-xs font-bold {{ $comment->isLikedBy($identifier) ? 'text-status-error' : 'text-on-surface-variant' }}"
                        data-url="{{ route('forum.comments.like', $comment) }}">
                    <span class="material-symbols-outlined like-icon text-[16px]" style="font-variation-settings: 'FILL' {{ $comment->isLikedBy($identifier) ? 1 : 0 }};">favorite</span>
                    <span class="like-count">{{ $comment->likes()->count() }}</span>
                </button>
                @if ($isLoggedIn)
                <button type="button" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')"
                        class="text-xs font-bold text-math-teal">Balas</button>
                @endif
            </div>
        </div>
        <button type="button" onclick="document.getElementById('report-comment-{{ $comment->id }}').classList.toggle('hidden')"
                class="material-symbols-outlined text-[16px] text-on-surface-variant hover:text-status-error shrink-0">flag</button>
    </div>

    <div id="report-comment-{{ $comment->id }}" class="hidden bg-error-container/20 rounded-lg p-3 mt-2">
        <form action="{{ route('forum.comments.report', $comment) }}" method="POST" class="space-y-2">
            @csrf
            <select name="reason" required class="w-full text-xs rounded-md border-outline-variant">
                <option value="">Pilih alasan laporan komentar...</option>
                @foreach ($reasons as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
            <button class="bg-status-error text-white text-xs font-bold px-3 py-1.5 rounded-md">Laporkan Komentar</button>
        </form>
    </div>

    @if ($isLoggedIn)
    <div id="reply-form-{{ $comment->id }}" class="hidden mt-2">
        <form action="{{ route('forum.posts.comment', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-2 bg-white border border-outline-variant rounded-lg p-3">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <textarea name="content" rows="2" placeholder="Tulis balasan..." class="w-full text-sm rounded-md border-outline-variant"></textarea>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1 text-xs text-on-surface-variant cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">image</span>
                    <span id="reply-file-label-{{ $comment->id }}">Tambah Foto</span>
                    <input type="file" name="image" accept="image/*" class="hidden" onchange="updateFileLabel(this, 'reply-file-label-{{ $comment->id }}')">
                </label>
                <button class="ml-auto bg-math-teal text-white text-xs font-bold px-4 py-2 rounded-md">Kirim Balasan</button>
            </div>
        </form>
    </div>
    @endif

    @foreach ($comment->repliesRecursive as $reply)
        @include('public.forum.partials.comment', ['comment' => $reply, 'post' => $post, 'reasons' => $reasons, 'depth' => $depth + 1, 'identifier' => $identifier, 'isLoggedIn' => $isLoggedIn])
    @endforeach
</div>