@extends('layouts.dashboard')
@section('title', 'Detail Modul Ajar')

@section('dashboard-content')
<a href="{{ route('guru.modul-ajar.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    Kembali ke Daftar Modul Ajar
</a>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-math-teal uppercase tracking-wide">{{ $module->mapel }} &middot; Fase {{ $module->fase }}</span>
            <h1 class="font-headline text-2xl font-bold text-navy-deep mt-1">{{ $module->materi }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-on-surface-variant">
                <span class="flex items-center gap-1 bg-surface-container px-2 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">groups</span>{{ $module->kelas }}
                </span>
                <span class="flex items-center gap-1 bg-surface-container px-2 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">event_repeat</span>{{ $module->duration_label }}
                </span>
                <span class="flex items-center gap-1 bg-surface-container px-2 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">calendar_month</span>{{ $module->academic_year }} &middot; {{ $module->semester }}
                </span>
            </div>
        </div>
        @if ($module->status === 'completed')
        <a href="{{ route('guru.modul-ajar.print', $module) }}" target="_blank"
           class="flex items-center justify-center gap-2 bg-navy-deep text-white px-5 py-3 rounded-md font-bold text-sm hover:bg-math-teal transition-colors whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
            Cetak PDF
        </a>
        @endif
    </div>
</div>

<div id="progress-panel"
     data-status="{{ $module->status }}"
     data-generate-url="{{ route('guru.modul-ajar.generate-step', $module) }}"
     data-total-batches="{{ $module->total_batches }}"
     data-current-batch="{{ count($module->batches ?? []) }}">

    @if ($module->status === 'processing')
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-8 text-center" id="processing-box">
        <div class="w-16 h-16 rounded-full bg-math-teal/10 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-math-teal text-3xl animate-spin">progress_activity</span>
        </div>
        <p class="font-bold text-navy-deep" id="processing-text">
            Sedang generate bagian {{ count($module->batches ?? []) + 1 }} dari {{ $module->total_batches }}...
        </p>
        <p class="text-xs text-on-surface-variant mt-1">Mohon tunggu, proses ini bisa memakan waktu 30-60 detik per bagian.</p>
        <div class="h-2 w-full max-w-md mx-auto bg-surface-container rounded-full overflow-hidden mt-5">
            <div class="h-full bg-math-teal transition-all duration-500"
                 style="width: {{ (count($module->batches ?? []) / $module->total_batches) * 100 }}%" id="progress-bar"></div>
        </div>
    </div>
    @endif

    @if ($module->status === 'failed')
    <div class="bg-white rounded-xl shadow-sm border border-status-error/30 p-6" id="failed-box">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-status-error text-2xl">error</span>
            <div class="flex-1">
                <p class="font-bold text-status-error mb-1">Generate Sempat Gagal</p>
                <p class="text-sm text-on-surface-variant mb-3">{{ $module->error_message }}</p>
                <p class="text-xs text-on-surface-variant mb-4">
                    Progres tersimpan: <strong>{{ count($module->batches ?? []) }} dari {{ $module->total_batches }}</strong> bagian sudah berhasil. Lanjutkan tanpa mengulang dari awal.
                </p>
                <button type="button" onclick="resumeGeneration()" id="resume-btn"
                        class="bg-math-teal text-white px-6 py-2.5 rounded-md font-bold text-sm hover:brightness-110">
                    Lanjutkan Generate
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<div id="batches-container" class="space-y-6">
    @foreach ($module->batches as $batch)
    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="bg-navy-deep px-6 py-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-math-teal text-[20px]">event_note</span>
            <h2 class="font-headline text-white font-bold text-sm">Pertemuan {{ $batch['meeting_range'] }}</h2>
        </div>
        <div class="p-6 prose prose-sm max-w-none text-on-surface prose-headings:text-navy-deep prose-headings:font-headline prose-h3:text-base prose-h3:mt-4 prose-h3:mb-2 prose-ul:my-2">
            {!! $batch['content'] !!}
        </div>
    </div>
    @endforeach
</div>

<script>
const panel = document.getElementById('progress-panel');
const generateUrl = panel.dataset.generateUrl;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

async function callGenerateStep() {
    try {
        const res = await fetch(generateUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.failed) { location.reload(); return; }
        if (data.done) { location.reload(); return; }

        const bar = document.getElementById('progress-bar');
        const text = document.getElementById('processing-text');
        if (bar) bar.style.width = ((data.module.current_batch / data.module.total_batches) * 100) + '%';
        if (text) text.textContent = `Sedang generate bagian ${data.module.current_batch + 1} dari ${data.module.total_batches}...`;

        callGenerateStep();
    } catch (err) {
        console.error('Gagal memanggil generate-step:', err);
    }
}

function resumeGeneration() {
    document.getElementById('failed-box').innerHTML = `
        <div class="text-center py-4">
            <span class="material-symbols-outlined text-math-teal text-3xl animate-spin">progress_activity</span>
            <p class="text-on-surface-variant mt-2 text-sm">Melanjutkan generate...</p>
        </div>`;
    callGenerateStep();
}

if (panel.dataset.status === 'processing') {
    callGenerateStep();
}
</script>
@endsection