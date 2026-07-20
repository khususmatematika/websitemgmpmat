<div style="margin-left: {{ min($depth, 4) * 28 }}px" class="{{ $depth > 0 ? 'mt-2' : 'mt-3' }}">
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
            <button type="button" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')"
                    class="text-xs font-bold text-math-teal mt-1">Balas</button>
        </div>
        <button type="button" onclick="document.getElementById('report-comment-{{ $comment->id }}').classList.toggle('hidden')"
                class="material-symbols-outlined text-[16px] text-on-surface-variant hover:text-status-error shrink-0">flag</button>
    </div>

    <div id="report-comment-{{ $comment->id }}" class="hidden bg-error-container/20 rounded-lg p-3 mt-2">
        <form action="{{ route('student-work-comments.report', $comment) }}" method="POST" class="space-y-2">
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

    <div id="reply-form-{{ $comment->id }}" class="hidden mt-2">
        <form action="{{ route('student-works.comment', $work) }}" method="POST" class="space-y-2 bg-white border border-outline-variant rounded-lg p-3">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <input type="text" name="commenter_name" required placeholder="Nama kamu"
                   class="w-full text-sm rounded-md border-outline-variant reply-name-input">
            <textarea name="content" required rows="2" placeholder="Tulis balasan..."
                      class="w-full text-sm rounded-md border-outline-variant"></textarea>
            <button class="bg-math-teal text-white text-xs font-bold px-4 py-2 rounded-md">Kirim Balasan</button>
        </form>
    </div>

    @foreach ($comment->replies as $reply)
        @include('public.student-works.partials.comment', ['comment' => $reply, 'work' => $work, 'reasons' => $reasons, 'depth' => $depth + 1])
    @endforeach
</div>