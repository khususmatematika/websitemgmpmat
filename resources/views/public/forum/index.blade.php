@extends('layouts.app')
@section('title', 'Forum Diskusi - SMAN 1 Turen Math Portal')

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-3xl mx-auto">
       <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <span class="text-secondary font-label text-xs uppercase tracking-widest mb-2 block">Diskusi Siswa</span>
            <h1 class="font-headline text-3xl font-bold text-navy-deep mb-2">Forum Diskusi</h1>
            <p class="text-on-surface-variant">Bertanya, berdiskusi, dan berbagi seputar matematika.</p>
        </div>
        <a href="{{ route('forum.create') }}"
           class="flex items-center gap-2 bg-secondary text-white px-6 py-3 rounded-md font-bold hover:brightness-110 transition-all whitespace-nowrap">
            <span class="material-symbols-outlined">add_comment</span>
            Buat Postingan
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-6">
        @forelse ($posts as $post)
        <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-navy-deep/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-navy-deep">person</span>
                </div>
                <div>
                    <p class="font-bold text-navy-deep text-sm">{{ $post->author_name }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                <button onclick="document.getElementById('report-post-{{ $post->id }}').classList.toggle('hidden')"
                        class="material-symbols-outlined text-[18px] text-on-surface-variant hover:text-status-error ml-auto">flag</button>
            </div>

            <p class="text-sm text-on-surface whitespace-pre-line mb-3">{{ $post->content }}</p>

            @if ($post->image_path)
                <img src="{{ asset('storage/'.$post->image_path) }}" class="w-full rounded-lg mb-3 max-h-96 object-cover">
            @endif

            {{-- Form Laporkan Post --}}
            <div id="report-post-{{ $post->id }}" class="hidden bg-error-container/20 rounded-lg p-4 mb-3">
                <form action="{{ route('forum.posts.report', $post) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="text-xs font-bold text-status-error">Laporkan postingan ini</label>
                    <select name="reason" required class="w-full text-sm rounded-md border-outline-variant">
                        <option value="">Pilih alasan...</option>
                        @foreach ($reasons as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="reporter_name" placeholder="Nama Anda (opsional)" class="w-full text-sm rounded-md border-outline-variant">
                    <button class="bg-status-error text-white text-sm font-bold px-4 py-2 rounded-md">Kirim Laporan</button>
                </form>
            </div>

            <div class="flex items-center gap-4 pt-3 border-t border-outline-variant">
                <button type="button"
                        class="like-btn flex items-center gap-1 text-sm font-bold {{ $post->isLikedBy($identifier) ? 'text-status-error' : 'text-on-surface-variant' }}"
                        data-url="{{ route('forum.posts.like', $post) }}">
                    <span class="material-symbols-outlined like-icon" style="font-variation-settings: 'FILL' {{ $post->isLikedBy($identifier) ? 1 : 0 }};">favorite</span>
                    <span class="like-count">{{ $post->likes_count }}</span> Suka
                </button>
                <button type="button" onclick="document.getElementById('comments-{{ $post->id }}').classList.toggle('hidden')"
                        class="flex items-center gap-1 text-sm font-bold text-on-surface-variant">
                    <span class="material-symbols-outlined">chat_bubble</span>
                    {{ $post->totalCommentCount() }} Komentar
                </button>
            </div>

            {{-- Komentar (toggle tampil/sembunyi) --}}
            <div id="comments-{{ $post->id }}" class="hidden mt-4 pt-4 border-t border-outline-variant space-y-3">
                @forelse ($post->comments as $comment)
                    @include('public.forum.partials.comment', ['comment' => $comment, 'post' => $post, 'reasons' => $reasons, 'depth' => 0, 'identifier' => $identifier])
                @empty
                    <p class="text-xs text-on-surface-variant">Belum ada komentar.</p>
                @endforelse

                <form action="{{ route('forum.posts.comment', $post) }}" method="POST" class="ajax-comment-form space-y-2 pt-2">
                 @csrf
                 <input type="text" name="commenter_name" required placeholder="Nama kamu"
                 class="w-full text-sm rounded-md border-outline-variant">
                 <textarea name="content" required rows="2" placeholder="Tulis komentar..."
                  class="w-full text-sm rounded-md border-outline-variant"></textarea>
                  <button type="submit" class="bg-navy-deep text-white text-sm font-bold px-4 py-2 rounded-md">Kirim Komentar</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-on-surface-variant text-center py-12">Belum ada postingan forum. Jadilah yang pertama!</p>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const savedName = localStorage.getItem('student_display_name');

    function applySavedName() {
        if (!savedName) return;
        document.querySelectorAll('input[name="commenter_name"], input[name="author_name"]').forEach(el => {
            if (!el.value) el.value = savedName;
        });
    }
    applySavedName();
    document.body.addEventListener('click', () => setTimeout(applySavedName, 50));
    document.body.addEventListener('change', (e) => {
        if (e.target.name === 'commenter_name' || e.target.name === 'author_name') {
            localStorage.setItem('student_display_name', e.target.value);
        }
    });

    // Like via AJAX — tidak reload halaman
    document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.like-btn');
        if (!btn) return;

        const url = btn.dataset.url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            const icon = btn.querySelector('.like-icon');
            const countEl = btn.querySelector('.like-count');

            countEl.textContent = data.likes_count;
            icon.style.fontVariationSettings = `'FILL' ${data.liked ? 1 : 0}`;
            btn.classList.toggle('text-status-error', data.liked);
            btn.classList.toggle('text-on-surface-variant', !data.liked);
        } catch (err) {
            console.error('Gagal memproses like:', err);
        }
    });

    // Submit komentar/balasan via AJAX — halaman tidak reload, tetap di posisi yang sama
    document.body.addEventListener('submit', async (e) => {
        const form = e.target.closest('.ajax-comment-form');
        if (!form) return;
        e.preventDefault();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!res.ok) {
                alert('Gagal mengirim. Pastikan semua field terisi.');
                return;
            }

            // Simpan post ID mana yang sedang terbuka komentarnya, supaya tetap terbuka setelah reload
            const postCard = form.closest('[id^="comments-"]');
            if (postCard) {
                sessionStorage.setItem('forum_open_comments', postCard.id);
            }
            sessionStorage.setItem('forum_scroll_y', window.scrollY);

            location.reload();
        } catch (err) {
            console.error('Gagal mengirim komentar:', err);
            alert('Terjadi kesalahan, coba lagi.');
        }
    });

    // Setelah reload: buka kembali panel komentar yang tadi aktif & kembalikan posisi scroll
    const openId = sessionStorage.getItem('forum_open_comments');
    if (openId) {
        const el = document.getElementById(openId);
        if (el) el.classList.remove('hidden');
        sessionStorage.removeItem('forum_open_comments');
    }
    const scrollY = sessionStorage.getItem('forum_scroll_y');
    if (scrollY) {
        window.scrollTo(0, parseInt(scrollY));
        sessionStorage.removeItem('forum_scroll_y');
    }
});
</script>
@endpush
@endsection