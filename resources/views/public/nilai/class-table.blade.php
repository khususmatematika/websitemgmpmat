@extends('layouts.app')
@section('title', 'Nilai Kelas - '.$class->name)

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-4xl mx-auto">
    <a href="{{ route('nilai.show') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Nilai Saya
    </a>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="font-headline text-2xl font-bold text-navy-deep">{{ $topic->title }}</h1>
            <p class="text-on-surface-variant">{{ $class->name }} &middot; Tabel Nilai Seluruh Siswa</p>
        </div>

        @if ($classOverallAverage !== null)
        <div class="bg-white rounded-xl border border-outline-variant/30 px-4 py-2.5 text-center shrink-0">
            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide">Rata-rata Kelas</p>
            <p class="text-xl font-bold text-blue-600">{{ $classOverallAverage }}</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-surface-container-low text-on-surface-variant">
                    <tr>
                        <th class="p-3 text-left">Nama Siswa</th>
                        @foreach ($components as $c)
                        <th class="p-3 text-center">{{ $c->name }}<br><span class="text-[10px]">({{ $c->weight }}%)</span></th>
                        @endforeach
                        <th class="p-3 text-center">Nilai (Bobot)</th>
                        <th class="p-3 text-center">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr class="border-t border-outline-variant {{ $row['is_me'] ? 'bg-blue-50' : '' }}">
                        <td class="p-3 font-medium text-navy-deep">
                            {{ $row['name'] }}
                            @if ($row['is_me'])
                                <span class="text-[10px] bg-blue-600 text-white px-1.5 py-0.5 rounded-full ml-1">Kamu</span>
                            @endif
                        </td>
                        @foreach ($row['scores'] as $s)
                        <td class="p-3 text-center">{{ $s ?? '-' }}</td>
                        @endforeach
                        <td class="p-3 text-center">
                            <span class="font-bold text-navy-deep">{{ $row['base'] ?? '-' }}</span>
                            @if ($row['bonus'] > 0)
                                <span class="text-status-success text-[10px] font-bold ml-1">+{{ $row['bonus'] }}</span>
                            @endif
                            @if ($row['deduction'] > 0)
                                <span class="text-status-error text-[10px] font-bold ml-1">-{{ $row['deduction'] }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-center font-bold text-blue-600">{{ $row['final'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection