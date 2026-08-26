@extends('layouts.app')
@section('title', 'Profil Guru - SMAN 1 Turen Math Portal')

@push('styles')
<style>
    .carousel-wrapper {
    overflow: hidden;
    width: 100%;
}
    .carousel-stage {
        perspective: 1200px;
        height: 340px;
        overflow: visible;
        position: relative;
        isolation: isolate;
    }
    @media (min-width: 768px) {
        .carousel-stage { height: 560px; perspective: 1800px; }
    }
    @media (min-width: 1280px) {
        .carousel-stage { height: 620px; }
    }
    .team-card {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 170px;
        height: 240px;
        margin-top: -120px;
        margin-left: -85px;
        border-radius: 1.25rem;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.55s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.55s ease, filter 0.55s ease;
        box-shadow: 0 20px 50px -12px rgba(15, 37, 68, 0.35);
        background: #333;
    }
    @media (min-width: 768px) {
        .team-card { width: 340px; height: 480px; margin-top: -240px; margin-left: -170px; border-radius: 1.5rem; }
    }
    @media (min-width: 1280px) {
        .team-card { width: 400px; height: 560px; margin-top: -280px; margin-left: -200px; }
    }
    .team-card img {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .team-card .card-overlay {
        position: absolute; left: 0; right: 0; bottom: 0;
        padding: 12px 10px 10px;
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 45%, transparent 100%);
        color: white;
        pointer-events: none;
    }
    @media (min-width: 768px) {
        .team-card .card-overlay { padding: 18px 16px 16px; }
    }
    .team-card.is-active::after {
        content: '';
        position: absolute; inset: 0;
        background: rgba(45, 106, 79, 0);
        transition: background 0.25s ease;
    }
    .team-card.is-active:hover::after { background: rgba(45, 106, 79, 0.15); }

    .wa-hint {
        position: absolute; top: 6px; right: 6px;
        width: 24px; height: 24px; border-radius: 50%;
        background: #25D366; display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: scale(0.8); transition: all 0.25s ease;
        box-shadow: 0 4px 10px rgba(37, 211, 102, 0.4);
    }
    @media (min-width: 768px) {
        .wa-hint { top: 12px; right: 12px; width: 34px; height: 34px; }
    }
    .team-card.is-active:hover .wa-hint { opacity: 1; transform: scale(1); }

    .dash-dot {
        width: 22px; height: 3px; border-radius: 3px;
        background: #c4c6cf; transition: all 0.3s ease;
        cursor: pointer;
    }
    .dash-dot.active { width: 34px; background: #20B2AA; }

    .drag-hint { letter-spacing: 0.15em; }

    .play-pause-btn {
        backdrop-filter: blur(4px);
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .play-pause-btn:hover { transform: translate(-50%, -50%) scale(1.08); }
</style>
@endpush

@section('content')
<section class="py-16 md:py-20 px-4 md:px-margin-desktop max-w-container-max mx-auto">

    <div class="text-center mb-10 md:mb-14">
        <span class="text-math-teal font-label text-xs uppercase tracking-widest">SMAN 1 Turen &middot; {{ str_pad($teachers->count(), 2, '0', STR_PAD_LEFT) }} Guru</span>
        <h1 class="font-headline text-3xl md:text-4xl font-bold text-navy-deep mt-2">Profil Guru Matematika</h1>
    </div>

    @if ($teachers->count() > 0)
    <div class="carousel-wrapper">
        <div class="carousel-stage relative select-none" id="carousel-stage">
        @foreach ($teachers as $i => $teacher)
        <div class="team-card" data-index="{{ $i }}"
             data-wa="{{ $teacher->whatsapp_number ?? '' }}"
             onclick="handleCardClick({{ $i }})">
            @if ($teacher->photo)
                <img src="{{ asset('storage/'.$teacher->photo) }}" alt="{{ $teacher->name }}" draggable="false">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0F2544&color=fff&size=400" alt="{{ $teacher->name }}" draggable="false">
            @endif

            @if ($teacher->whatsapp_number)
            <div class="wa-hint">
                <span class="material-symbols-outlined text-white text-[18px]">chat</span>
            </div>
            @endif

            <div class="card-overlay">
                <p class="font-headline font-bold text-[11px] md:text-lg leading-tight line-clamp-1">{{ $teacher->name }}</p>
                <p class="text-white/70 text-[9px] md:text-sm line-clamp-1">{{ $teacher->title ?: '-' }}</p>
            </div>
        </div>
        @endforeach
    </div>
    </div>

    {{-- Kontrol bawah --}}
    <div class="flex flex-col items-center gap-3 md:gap-4 mt-6 md:mt-10">
    <p class="font-label text-xs md:text-sm text-navy-deep font-bold" id="index-label">-- / --</p>

    <div class="flex items-center gap-3 md:gap-4">
            <button type="button" onclick="goTo(currentIndex - 1)"
                    class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center text-navy-deep hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>

            <div class="flex items-center gap-1.5" id="dots-container">
                @foreach ($teachers as $i => $teacher)
                <div class="dash-dot" data-dot="{{ $i }}" onclick="goTo({{ $i }})"></div>
                @endforeach
            </div>

            <button type="button" onclick="goTo(currentIndex + 1)"
                    class="w-10 h-10 rounded-full bg-navy-deep flex items-center justify-center text-white hover:bg-math-teal transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>
    </div>

    {{-- Panel Jadwal Guru Aktif (di bawah carousel, bukan di dalam foto) --}}
    <div class="w-full max-w-4xl mx-auto mt-10 md:mt-14 px-4 md:px-0">
        <div class="bg-white rounded-2xl md:rounded-3xl border border-outline-variant/30 shadow-md overflow-hidden">

            {{-- Header info guru --}}
            <div class="bg-gradient-to-r from-navy-deep to-primary-container px-5 py-5 md:px-8 md:py-6 flex items-center gap-4">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-math-teal text-2xl md:text-3xl">person</span>
                </div>
                <div class="min-w-0">
                    <p class="font-headline font-bold text-white text-lg md:text-xl truncate" id="active-teacher-name">-</p>
                    <p class="text-white/60 text-xs md:text-sm truncate" id="active-teacher-title">-</p>
                </div>
            </div>

            <div class="p-5 md:p-8">
                <div id="active-teacher-description" class="text-sm text-on-surface leading-relaxed mb-5 pb-5 border-b border-outline-variant/50 hidden"></div>

                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs md:text-sm font-bold text-navy-deep uppercase tracking-wide flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-math-teal">event_note</span> Jadwal Mengajar
                    </p>
                    <span id="schedule-count" class="text-[10px] md:text-xs bg-math-teal/10 text-math-teal font-bold px-2.5 py-1 rounded-full"></span>
                </div>

                <div id="active-teacher-schedule" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 md:gap-3"></div>
            </div>
        </div>
    </div>

    {{-- Data jadwal semua guru untuk dirender via JS --}}
    <script id="teachers-data" type="application/json">
        {!! json_encode($teachers->map(function ($t) {
            return [
                'name' => $t->name,
                'title' => $t->title ?: 'Belum diatur',
                'description' => $t->description,
                'whatsapp' => $t->whatsapp_number,
                'schedules' => $t->classes->map(function ($c) {
                    return [
                        'name' => $c->name,
                        'day' => $c->pivot->day,
                        'start' => $c->pivot->start_time ? substr($c->pivot->start_time, 0, 5) : null,
                        'end' => $c->pivot->end_time ? substr($c->pivot->end_time, 0, 5) : null,
                    ];
                })->values(),
            ];
        })) !!}
    </script>

    @else
    <p class="text-center text-on-surface-variant py-20">Belum ada data guru yang ditambahkan.</p>
    @endif
</section>

<script>
const teachersData = JSON.parse(document.getElementById('teachers-data')?.textContent || '[]');
const totalCards = teachersData.length;
let currentIndex = totalCards > 0 ? Math.floor(totalCards / 2) : 0; // default: guru di tengah array
let autoplayTimer = null;
let isPlaying = true;

const cards = document.querySelectorAll('.team-card');
const dots = document.querySelectorAll('.dash-dot');
const indexLabel = document.getElementById('index-label');

function pad(n) { return n < 10 ? '0' + n : '' + n; }

function renderActiveInfo() {
    const t = teachersData[currentIndex];
    if (!t) return;

    document.getElementById('active-teacher-name').textContent = t.name;
    document.getElementById('active-teacher-title').textContent = t.title;

    const descEl = document.getElementById('active-teacher-description');
    if (t.description) {
        descEl.innerHTML = `
            <p class="text-xs font-bold text-navy-deep uppercase tracking-wide mb-1.5 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-math-teal">info</span> Tentang
            </p>
            <p>${t.description}</p>
        `;
        descEl.classList.remove('hidden');
    } else {
        descEl.classList.add('hidden');
    }

    const countEl = document.getElementById('schedule-count');
    countEl.textContent = t.schedules.length + ' pertemuan';

    const scheduleEl = document.getElementById('active-teacher-schedule');
    if (t.schedules.length === 0) {
        scheduleEl.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-8 text-center">
                <span class="material-symbols-outlined text-outline-variant text-3xl mb-2">event_busy</span>
                <p class="text-xs text-on-surface-variant">Belum ada jadwal mengajar.</p>
            </div>`;
    } else {
        scheduleEl.innerHTML = t.schedules.map(s => `
            <div class="flex items-center gap-3 bg-surface-container-low hover:bg-surface-container rounded-xl px-4 py-3 transition-colors">
                <div class="w-9 h-9 rounded-lg bg-math-teal/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-math-teal text-[18px]">school</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-navy-deep text-sm truncate">${s.name}</p>
                    ${s.day ? `<p class="text-on-surface-variant text-xs flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                        ${s.day} &middot; ${s.start}-${s.end}
                    </p>` : `<p class="text-on-surface-variant text-xs mt-0.5">Jadwal belum diatur</p>`}
                </div>
            </div>
        `).join('');
    }
}

function getShortestOffset(cardIndex, activeIndex, total) {
    let offset = cardIndex - activeIndex;
    // Bungkus supaya jarak selalu di rentang -total/2 sampai +total/2 (jalur terpendek melingkar)
    if (offset > total / 2) offset -= total;
    if (offset < -total / 2) offset += total;
    return offset;
}

function render() {
    const isDesktop = window.innerWidth >= 1280;
    const isTablet = window.innerWidth >= 768 && window.innerWidth < 1280;

    const spacing = isDesktop ? 260 : (isTablet ? 210 : 85);
    const depthStep = isDesktop ? 180 : (isTablet ? 150 : 90);

    cards.forEach((card, i) => {
        const offset = getShortestOffset(i, currentIndex, totalCards);
        const abs = Math.abs(offset);
        card.classList.toggle('is-active', offset === 0);

        if (abs > 3) {
            card.style.opacity = '0';
            card.style.pointerEvents = 'none';
            card.style.zIndex = 0;
            return;
        }
        card.style.pointerEvents = 'auto';

        const translateX = offset * spacing;
        const rotateY = offset * -28;
        const scale = offset === 0 ? 1 : 0.82 - abs * 0.05;
        const translateZ = -abs * depthStep;
        const opacity = offset === 0 ? 1 : 0.55 - abs * 0.1;
        const grayscale = offset === 0 ? 0 : 85;

        card.style.transform = `translate(${translateX}px, 0) translateZ(${translateZ}px) rotateY(${rotateY}deg) scale(${scale})`;
        card.style.opacity = Math.max(opacity, 0.15);
        card.style.filter = `grayscale(${grayscale}%)`;
        card.style.zIndex = Math.min(40 - abs, 40);
    });

    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
    indexLabel.textContent = `${pad(currentIndex + 1)} / ${pad(totalCards)}`;
    renderActiveInfo();
}

function goTo(index) {
    currentIndex = ((index % totalCards) + totalCards) % totalCards;
    render();
}

function handleCardClick(i) {
    if (i === currentIndex) {
        const wa = teachersData[i]?.whatsapp;
        if (wa) {
            window.open(`https://wa.me/${wa}`, '_blank');
        }
    } else {
        goTo(i);
    }
}

function startAutoplay() {
    stopAutoplay();
    autoplayTimer = setInterval(() => goTo(currentIndex + 1), 3500);
}
function stopAutoplay() {
    if (autoplayTimer) clearInterval(autoplayTimer);
}
function toggleAutoplay() {
    isPlaying = !isPlaying;
    document.getElementById('play-pause-icon').textContent = isPlaying ? 'pause' : 'play_arrow';
    if (isPlaying) startAutoplay(); else stopAutoplay();
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') goTo(currentIndex - 1);
    if (e.key === 'ArrowRight') goTo(currentIndex + 1);
});

const stage = document.getElementById('carousel-stage');
let dragStartX = 0, isDragging = false;

stage?.addEventListener('mousedown', (e) => { isDragging = true; dragStartX = e.clientX; stopAutoplay(); });
window.addEventListener('mouseup', (e) => {
    if (!isDragging) return;
    isDragging = false;
    const diff = e.clientX - dragStartX;
    if (diff > 50) goTo(currentIndex - 1);
    else if (diff < -50) goTo(currentIndex + 1);
});

stage?.addEventListener('touchstart', (e) => { dragStartX = e.touches[0].clientX; stopAutoplay(); }, { passive: true });
stage?.addEventListener('touchend', (e) => {
    const diff = e.changedTouches[0].clientX - dragStartX;
    if (diff > 50) goTo(currentIndex - 1);
    else if (diff < -50) goTo(currentIndex + 1);
});

let scrollCooldown = false;
stage?.addEventListener('wheel', (e) => {
    if (scrollCooldown) return;
    scrollCooldown = true;
    if (e.deltaY > 0 || e.deltaX > 0) goTo(currentIndex + 1); else goTo(currentIndex - 1);
    setTimeout(() => scrollCooldown = false, 400);
}, { passive: true });

window.addEventListener('resize', () => render());

if (totalCards > 0) {
    render();
}
</script>
@endsection