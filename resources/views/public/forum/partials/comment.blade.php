<div style="margin-left: {{ min($depth, 4) * 28 }}px" class="{{ $depth > 0 ? 'mt-2' : '' }}">
    <div class="flex justify-between items-start gap-2 rounded-lg p-3 border-l-4
                {{ $depth > 0 ? 'bg-surface-container border-math-teal/40' : 'bg-surface-container-low border-navy-deep/20' }}">
        <div class="flex-1">
            @if ($depth > 0 && $comment->parent)
                <p class="text-[11px] text-math-teal font-bold flex items-center gap-1 mb-1">
                    <span class="material-symbols-outlined text-[14px]">subdirectory_arrow_right</span>
                    Membalas {{ $comment->parent->commenter_name }}
                </p>
            @endif
            <p class="text-xs font-bold text-navy-deep">{{ $comment->commenter_name }}</p>
            <p class="text-sm text-on-surface">{{ $comment->content }}</p>

            <div class="flex items-center gap-3 mt-2">
                <button type="button"
                        class="like-btn flex items-center gap-1 text-xs font-bold {{ $comment->isLikedBy($identifier) ? 'text-status-error' : 'text-on-surface-variant' }}"
                        data-url="{{ route('forum.comments.like', $comment) }}">
                    <span class="material-symbols-outlined like-icon text-[16px]" style="font-variation-settings: 'FILL' {{ $comment->isLikedBy($identifier) ? 1 : 0 }};">favorite</span>
                    <span class="like-count">{{ $comment->likes()->count() }}</span>
                </button>
                <button type="button" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')"
                        class="text-xs font-bold text-math-teal">Balas</button>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('report-comment-{{ $comment->id }}').classList.toggle('hidden')"
                class="material-symbols-outlined text-[16px] text-on-surface-variant hover:text-status-error shrink-0">flag</button>
    </div>

    {{-- Form Laporkan Komentar --}}
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

    {{-- Form Balas Komentar Ini --}}
    <div id="reply-form-{{ $comment->id }}" class="hidden mt-2">
    <form action="{{ route('forum.posts.comment', $post) }}" method="POST" class="ajax-comment-form space-y-2 bg-white border border-outline-variant rounded-lg p-3">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <input type="text" name="commenter_name" required placeholder="Nama kamu"
               class="w-full text-sm rounded-md border-outline-variant">
        <textarea name="content" required rows="2" placeholder="Tulis balasan..."
                  class="w-full text-sm rounded-md border-outline-variant"></textarea>
        <button type="submit" class="bg-math-teal text-white text-xs font-bold px-4 py-2 rounded-md">Kirim Balasan</button>
    </form>
</div>

    {{-- Rekursif: render semua balasan dari komentar ini --}}
    @foreach ($comment->repliesRecursive as $reply)
        @include('public.forum.partials.comment', ['comment' => $reply, 'post' => $post, 'reasons' => $reasons, 'depth' => $depth + 1, 'identifier' => $identifier])
    @endforeach
</div>