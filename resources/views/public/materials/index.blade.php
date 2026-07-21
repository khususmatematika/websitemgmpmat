@extends('layouts.app')
@section('title', 'Materi - SMAN 1 Turen Math Portal')

@section('content')
<div class="flex flex-col lg:flex-row min-h-[calc(100vh-72px)]">

    {{-- Sidebar: Kelas + Topik Kurikulum --}}
    <aside class="lg:w-72 bg-surface-container-low border-r border-outline-variant p-6 space-y-4">
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
                    <span class="text-xs bg-white/60 px-2 py-0.5 rounded-full">{{ $materialCountByJenjang[$key] ?? 0 }}</span>
                </a>

                @if ($isActive)
                <div class="pl-8 mt-2 space-y-3">
                    @foreach (['Ganjil', 'Genap'] as $sem)
                    <div>
                        <a href="{{ route('materials.public', ['jenjang' => $key, 'semester' => $sem]) }}"
                           class="text-xs font-bold uppercase tracking-tighter py-1 block {{ $semester === $sem ? 'text-math-teal' : 'text-on-surface-variant' }}">
                            Semester {{ $sem }}
                        </a>
                        @if ($semester === $sem)
                        <div class="space-y-1 mt-1">
                            @forelse (($topicsByJenjangSemester[$key][$sem] ?? []) as $topic)
                                <p class="text-sm text-on-surface pl-1">{{ $topic->title }}</p>
                            @empty
                                <p class="text-xs text-on-surface-variant italic pl-1">Belum ada topik.</p>
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

    {{-- Main: Kartu PDF --}}
    <main class="flex-1 p-6 md:p-10">
        <div class="max-w-4xl mx-auto space-y-8">
            <section>
                <h1 class="font-headline text-2xl font-bold text-navy-deep">Mathematics Material Portal</h1>
                <p class="text-on-surface-variant">{{ $jenjangList[$jenjang] }} &middot; Semester {{ $semester }}</p>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($materials as $m)
                    <div class="group bg-white p-5 rounded-xl shadow-[0px_4px_12px_rgba(27,54,93,0.05)] border border-transparent hover:border-math-teal transition-all">
                        <div class="w-12 h-12 bg-error-container/20 rounded-lg flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-error">picture_as_pdf</span>
                        </div>
                        <h3 class="font-bold text-lg text-navy-deep group-hover:text-math-teal mb-2">{{ $m->title }}</h3>
                        <p class="text-xs text-on-surface-variant mb-4">{{ $m->created_at->format('d M Y') }} &middot; {{ $m->file_size_human }}</p>
                        <div class="flex gap-2">
                            <button type="button"
                                    onclick="previewMaterial('{{ route('materials.preview', $m) }}', '{{ addslashes($m->title) }}')"
                                    class="flex-1 py-2 bg-navy-deep text-white text-sm font-bold rounded-md hover:bg-math-teal transition-colors">
                                Preview
                            </button>
                            <a href="{{ route('materials.preview', $m) }}" download
                               class="px-3 py-2 border border-navy-deep text-navy-deep rounded-md hover:bg-navy-deep hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">download</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-on-surface-variant col-span-full text-center py-12">Belum ada file PDF materi untuk kelas & semester ini.</p>
                    @endforelse
                </div>

                <div class="lg:col-span-2">
    <div class="lg:sticky lg:top-24 bg-white rounded-xl shadow-[0px_4px_12px_rgba(27,54,93,0.05)] border border-outline-variant overflow-hidden flex flex-col h-[500px] lg:h-[calc(100vh-140px)]">
                        <div class="p-4 border-b border-outline-variant bg-surface-container-low">
                            <h4 class="font-bold text-navy-deep text-sm truncate" id="preview-title">Embed Viewer</h4>
                        </div>
                        <div class="flex-1" id="preview-container">
                            <div class="w-full h-full flex flex-col items-center justify-center p-8 text-center">
                                <span class="material-symbols-outlined text-math-teal text-3xl mb-4">visibility</span>
                                <p class="font-bold text-navy-deep">Pilih materi untuk preview</p>
                                <p class="text-xs text-on-surface-variant mt-2">PDF akan tampil langsung di panel ini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function previewMaterial(url, title) {
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-container').innerHTML =
        `<iframe src="${url}" class="w-full h-full" frameborder="0"></iframe>`;
}
</script>
@endsection