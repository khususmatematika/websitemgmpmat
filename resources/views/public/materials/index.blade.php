@extends('layouts.app')
@section('title', 'Materi - SMAN 1 Turen Math Portal')

@section('content')
<div class="flex flex-col lg:flex-row min-h-[calc(100vh-72px)]">

    <aside class="lg:w-72 bg-surface-container-low border-r border-outline-variant p-6 space-y-4 shrink-0">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-math-teal mb-2">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Beranda
        </a>
        <h3 class="font-label text-xs text-on-surface-variant uppercase tracking-wider">Jenjang Pendidikan</h3>
        <div class="space-y-1">
            @foreach ($jenjangList as $key => $label)
            @php $isActive = $jenjang === $key; @endphp
            <div>
                <a href="{{ route('materials.public', ['jenjang' => $key, 'semester' => $semester]) }}"
                   class="w-full flex items-center justify-between px-4 py-3 rounded-lg font-medium transition-all
                          {{ $isActive ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-variant' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined">filter_{{ $loop->iteration }}</span>
                        {{ $label }}
                    </span>
                    @if (!$isActive)
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                    @endif
                </a>

                @if ($isActive)
                <div class="pl-8 mt-2 space-y-3">
                    @foreach (['Ganjil', 'Genap'] as $sem)
                    <div>
                        <a href="{{ route('materials.public', ['jenjang' => $key, 'semester' => $sem]) }}"
                           class="text-xs font-bold uppercase tracking-tighter py-1 block {{ $semester === $sem ? 'text-math-teal' : 'text-on-surface-variant' }}">
                            Semester {{ $sem === 'Ganjil' ? '1' : '2' }}
                        </a>
                        @if ($semester === $sem)
                        <div class="space-y-1 mt-1">
                            @forelse (($topicsByJenjangSemester[$key][$sem] ?? []) as $topic)
                                <p class="text-sm text-on-surface pl-1">{{ $topic->title }}</p>
                            @empty
                                <p class="text-xs text-on-surface-variant italic pl-1">dst.</p>
                            @endforelse
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-10">
        <div class="max-w-container-max mx-auto space-y-6">
            <section class="flex items-start justify-between flex-wrap gap-3">
                <div>
                    <h1 class="font-headline text-2xl font-bold text-navy-deep">Mathematics Material Portal</h1>
                    <p class="text-on-surface-variant text-sm">Access curriculum-aligned modules, exercises, and teacher handbooks.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-md text-sm font-medium text-on-surface-variant hover:border-math-teal transition-colors">
                        <span class="material-symbols-outlined text-[18px]">sort</span>
                        Recently Added
                    </button>
                    <button type="button" class="w-10 h-10 flex items-center justify-center bg-white border border-outline-variant rounded-md text-on-surface-variant hover:border-math-teal transition-colors">
                        <span class="material-symbols-outlined text-[18px]">grid_view</span>
                    </button>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-5 lg:max-h-[75vh] lg:overflow-y-auto lg:pr-2">
                    @forelse ($materials as $m)
                    <div class="bg-white rounded-xl border border-outline-variant/30 p-4 hover:shadow-md transition-all">
                        <div class="relative w-20 h-24 mb-3">
                            @if ($m->cover_path)
                                <img src="{{ asset('storage/'.$m->cover_path) }}" class="w-full h-full object-cover rounded-md border border-outline-variant/50">
                            @else
                                <div class="w-full h-full bg-surface-container rounded-md border border-outline-variant/50 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-error text-2xl">picture_as_pdf</span>
                                </div>
                            @endif
                            <span class="absolute -top-1.5 -left-1.5 bg-status-error text-white text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm">PDF</span>
                            @if ($m->created_at->gt(now()->subDays(7)))
                            <span class="absolute -top-1.5 -right-1.5 bg-status-success text-white text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm">NEW</span>
                            @endif
                        </div>

                        <h3 class="font-bold text-navy-deep text-sm mb-2 line-clamp-2">{{ $m->title }}</h3>
                        <div class="space-y-1 mb-4">
                            <div class="flex items-center gap-1.5 text-[11px] text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">person</span>
                                {{ $m->uploader_name }}
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                {{ $m->created_at->format('M d, Y') }} &middot; {{ $m->file_size_human }}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="openMaterialModal('{{ route('materials.preview', $m) }}', '{{ addslashes($m->title) }}')"
                                    class="flex-1 py-2 bg-navy-deep text-white text-xs font-bold rounded-md hover:bg-math-teal transition-colors">
                                Preview
                            </button>
                            <a href="{{ route('materials.preview', $m) }}" download
                               class="px-3 py-2 border border-navy-deep text-navy-deep rounded-md hover:bg-navy-deep hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-16 bg-white rounded-xl border border-dashed border-outline-variant">
                        <span class="material-symbols-outlined text-outline-variant text-5xl mb-3">folder_off</span>
                        <p class="text-on-surface-variant">Belum ada file PDF materi untuk kelas & semester ini.</p>
                    </div>
                    @endforelse
                </div>

                <div class="lg:col-span-2">
                    <div class="lg:sticky lg:top-24 bg-white rounded-xl shadow-[0px_4px_12px_rgba(27,54,93,0.05)] border border-outline-variant overflow-hidden flex flex-col h-[500px] lg:h-[calc(100vh-140px)]">
                        <div class="p-4 border-b border-outline-variant bg-surface-container-low flex items-center justify-between">
                            <h4 class="font-bold text-navy-deep text-sm" id="preview-title">Embed Viewer</h4>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="toggleFullscreenPreview()" class="text-on-surface-variant hover:text-math-teal">
                                    <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                                </button>
                                <button type="button" onclick="printPreview()" class="text-on-surface-variant hover:text-math-teal">
                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1" id="preview-container">
                            <div class="w-full h-full flex flex-col items-center justify-center p-8 text-center">
                                <span class="material-symbols-outlined text-math-teal text-4xl mb-4">visibility</span>
                                <p class="font-bold text-navy-deep">Select a module to preview</p>
                                <p class="text-xs text-on-surface-variant mt-2 max-w-[200px]">Instantly view course materials without downloading.</p>
                            </div>
                        </div>
                        <div class="p-4 border-t border-outline-variant bg-white">
                            <a id="download-full-btn" href="#" class="hidden w-full py-3 bg-math-teal text-white rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-navy-deep transition-all active:scale-95 shadow-md">
                                <span class="material-symbols-outlined">download</span>
                                Download Full PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- Popup Embed Viewer (mobile & fallback) --}}
<div id="material-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 lg:hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeMaterialModal()">
    <div class="bg-white rounded-2xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-8 h-8 rounded-lg bg-error-container/30 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-error text-[18px]">picture_as_pdf</span>
                </span>
                <h4 id="material-modal-title" class="font-bold text-navy-deep truncate">Materi</h4>
            </div>
            <button onclick="closeMaterialModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container shrink-0">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>
        <div id="material-modal-frame" class="flex-1 w-full"></div>
    </div>
</div>

<script>
let currentPreviewUrl = null;

function openMaterialModal(url, title) {
    currentPreviewUrl = url;

    if (window.innerWidth >= 1024) {
        document.getElementById('preview-title').textContent = title;
        document.getElementById('preview-container').innerHTML = `<iframe id="preview-iframe" src="${url}" class="w-full h-full" frameborder="0"></iframe>`;
        const btn = document.getElementById('download-full-btn');
        btn.href = url;
        btn.classList.remove('hidden');
        return;
    }
    document.getElementById('material-modal-title').textContent = title;
    document.getElementById('material-modal-frame').innerHTML = `<iframe src="${url}" class="w-full h-full" frameborder="0"></iframe>`;
    document.getElementById('material-modal').classList.remove('hidden');
}
function closeMaterialModal() {
    document.getElementById('material-modal').classList.add('hidden');
    document.getElementById('material-modal-frame').innerHTML = '';
}
function toggleFullscreenPreview() {
    if (currentPreviewUrl) window.open(currentPreviewUrl, '_blank');
}
function printPreview() {
    const frame = document.getElementById('preview-iframe');
    if (frame) frame.contentWindow.print();
}
</script>
@endsection