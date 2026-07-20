@extends('layouts.dashboard')
@section('title', 'Laporan Konten')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep">Laporan Konten</h1>
<p class="text-on-surface-variant">Semua laporan dari Karya Siswa maupun Forum. Ditinjau manual, tanpa tindakan otomatis.</p>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                <th class="p-4 text-left">Konten Dilaporkan</th>
                <th class="p-4 text-left">Alasan</th>
                <th class="p-4 text-left">Pelapor</th>
                <th class="p-4 text-left">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
            <tr class="border-t border-outline-variant">
                <td class="p-4">
                    @if ($report->reportable)
                        <span class="font-medium text-navy-deep">
                            {{ Str::afterLast($report->reportable_type, '\\') }}
                        </span>
                        <p class="text-xs text-on-surface-variant mt-1">
                            {{ Str::limit($report->reportable->description ?? $report->reportable->content ?? '-', 60) }}
                        </p>
                    @else
                        <span class="text-on-surface-variant italic">Konten sudah dihapus</span>
                    @endif
                </td>
                <td class="p-4">{{ $report->reason }}</td>
                <td class="p-4">{{ $report->reporter_name ?? 'Anonim' }}</td>
                <td class="p-4">
                    @if ($report->status === 'pending')
                        <span class="px-2 py-1 bg-status-warning/10 text-status-warning rounded text-xs font-bold">Menunggu</span>
                    @else
                        <span class="px-2 py-1 bg-status-success/10 text-status-success rounded text-xs font-bold">Ditinjau</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    @if ($report->status === 'pending' && $report->reportable)
                    <div class="flex gap-2 justify-end">
                        <form action="{{ route('admin.laporan.resolve', $report) }}" method="POST"
                              onsubmit="return confirm('Sembunyikan/hapus konten yang dilaporkan ini?')">
                            @csrf
                            <input type="hidden" name="action" value="hide">
                            <button class="text-status-error text-xs font-bold">Hapus Konten</button>
                        </form>
                        <form action="{{ route('admin.laporan.resolve', $report) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="dismiss">
                            <button class="text-math-teal text-xs font-bold">Tandai Selesai</button>
                        </form>
                    </div>
                    @elseif ($report->status === 'pending')
                    <form action="{{ route('admin.laporan.resolve', $report) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="dismiss">
                        <button class="text-math-teal text-xs font-bold">Tandai Selesai</button>
                    </form>
                    @else
                        <span class="text-xs text-on-surface-variant">{{ $report->admin_action }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">Belum ada laporan masuk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection