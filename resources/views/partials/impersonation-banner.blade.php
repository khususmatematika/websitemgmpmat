@php $imp = \App\Support\Impersonation::active(); @endphp
@if ($imp)
<div class="fixed bottom-20 md:bottom-4 left-1/2 -translate-x-1/2 z-[9998] w-[calc(100vw-1.5rem)] max-w-md">
    <form method="POST" action="{{ route('impersonate.stop') }}"
          class="flex items-center justify-between gap-3 bg-navy-deep text-white rounded-full pl-4 pr-1.5 py-1.5 shadow-2xl border border-white/15">
        @csrf
        <span class="text-xs truncate">
            <span class="material-symbols-outlined text-[14px] align-middle text-status-warning">visibility</span>
            Masuk sebagai {{ $imp['type'] === 'guru' ? 'guru' : 'siswa' }}: <strong>{{ $imp['name'] }}</strong>
        </span>
        <button class="shrink-0 bg-math-teal text-white text-xs font-bold px-4 py-2 rounded-full hover:brightness-110">
            Kembali ke Admin
        </button>
    </form>
</div>
@endif