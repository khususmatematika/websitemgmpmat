@extends('layouts.app')
@section('title', 'Nilai Kelas - '.$class->name)

@section('content')
<section class="py-16 px-margin-mobile md:px-margin-desktop max-w-4xl mx-auto">
    <a href="{{ route('nilai.show') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-math-teal mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Nilai Saya
    </a>

    <h1 class="font-headline text-2xl font-bold text-navy-deep">{{ $topic->title }}</h1>
    <p class="text-on-surface-variant mb-6">{{ $class->name }} &middot; Tabel Nilai Seluruh Siswa</p>

    <div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-surface-container-low text-on-surface-variant">
                    <tr>
                        <th class="p-3 text-left">Nama Siswa</th>
                        @foreach ($components as $c)
                        <th class="p-3 text-center">{{ $c->name }}<br><span class="text-[10px]">({{ $c->weight }}%)</span></th>
                        @endforeach
                        <th class="p-3 text-center">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr class="border-t border-outline-variant {{ $row['is_me'] ? 'bg-math-teal/5' : '' }}">
                        <td class="p-3 font-medium text-navy-deep">
                            {{ $row['name'] }}
                            @if ($row['is_me'])
                                <span class="text-[10px] bg-math-teal text-white px-1.5 py-0.5 rounded-full ml-1">Kamu</span>
                            @endif
                        </td>
                        @foreach ($row['scores'] as $s)
                        <td class="p-3 text-center">{{ $s ?? '-' }}</td>
                        @endforeach
                        <td class="p-3 text-center font-bold text-navy-deep">{{ $row['final'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection