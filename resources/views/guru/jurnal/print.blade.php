<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Mengajar - {{ $teacher->name }}</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1a1b1e; }

        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 16px; }
        .kop-table td { vertical-align: middle; }
        .kop-logo { width: 75px; }
        .kop-logo img { width: 70px; }
        .kop-text { text-align: center; }

        h2.title { text-align: center; font-size: 14px; margin: 16px 0 4px 0; text-decoration: underline; }
        p.subtitle { text-align: center; margin: 0 0 16px 0; font-size: 11px; }

        table.info { width: 100%; margin-bottom: 12px; }
        table.info td { padding: 2px 0; font-size: 11px; }
        table.info td.label { width: 120px; }

        .class-section { margin-bottom: 24px; page-break-inside: avoid; }
        .class-title { font-size: 13px; font-weight: bold; color: #0F2544; background: #efedf1; padding: 6px 8px; margin-bottom: 8px; border-left: 4px solid #0F2544; }

        table.journal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.journal th, table.journal td {
            border: 1px solid #999;
            padding: 5px;
            font-size: 10px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        table.journal th { background-color: #efedf1; text-align: center; }
        table.journal td.center { text-align: center; }

        .attendance-list { font-size: 9px; }

        table.signature { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        table.signature td { text-align: center; font-size: 11px; vertical-align: top; padding: 0 20px; }
        .signature-space { height: 65px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            @if ($letterhead->logo_path)
            <td class="kop-logo">
                <img src="{{ storage_path('app/public/' . $letterhead->logo_path) }}">
            </td>
            @endif
            <td class="kop-text">
                @for ($i = 1; $i <= 5; $i++)
                    @php $text = $letterhead->{'line'.$i.'_text'}; @endphp
                    @if ($text)
                    <div style="font-size: {{ $letterhead->{'line'.$i.'_size'} }}px; font-weight: {{ $letterhead->{'line'.$i.'_bold'} ? 'bold' : 'normal' }}; margin: 1px 0;">
                        {{ $text }}
                    </div>
                    @endif
                @endfor
            </td>
        </tr>
    </table>

    <h2 class="title">JURNAL MENGAJAR</h2>
    <p class="subtitle">
        Bulan {{ $monthLabel }}
        @if ($isAllClasses) &mdash; Seluruh Kelas @endif
    </p>

    <table class="info">
        <tr>
            <td class="label">Nama Guru</td>
            <td>: {{ $teacher->name }}</td>
        </tr>
        @if (!$isAllClasses && $groupedByClass->count() === 1)
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $groupedByClass->first()['class']->name }}</td>
        </tr>
        @endif
    </table>

    @forelse ($groupedByClass as $group)
    <div class="class-section">
        @if ($isAllClasses)
        <div class="class-title">Kelas {{ $group['class']->name }}</div>
        @endif

        <table class="journal">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 20%;">Materi</th>
                    <th style="width: 40%;">Kegiatan</th>
                    <th style="width: 25%;">Siswa Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($group['journals'] as $i => $j)
                <tr>
                    <td class="center" style="width: 4%;">{{ $i + 1 }}</td>
                    <td class="center" style="width: 11%;">{{ $j->journal_date->translatedFormat('d F Y') }}</td>
                    <td style="width: 20%;">{{ $j->materi ?? '-' }}</td>
                    <td style="width: 40%;">{{ $j->kegiatan ?? '-' }}</td>
                    <td class="attendance-list" style="width: 25%;">
                        @php
                            $notPresent = $j->attendances->filter(fn($a) => $a->status !== 'Hadir');
                        @endphp
                        @forelse ($notPresent as $att)
                            {{ $att->student->name }} ({{ $att->status }})@if(!$loop->last), @endif
                        @empty
                            Nihil (semua hadir)
                        @endforelse
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="center">Tidak ada jurnal tercatat pada bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @empty
    <p style="text-align: center; padding: 20px;">Tidak ada jurnal tercatat pada bulan ini untuk kelas manapun.</p>
    @endforelse

    <table class="signature">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <div class="signature-space"></div>
                <span class="signature-name">{{ $letterhead->headmaster_name ?? '(...........................)' }}</span><br>
                NIP {{ $letterhead->headmaster_nip ?? '-' }}
            </td>
            <td>
                Turen, {{ now()->translatedFormat('d F Y') }}<br>Guru Mata Pelajaran
                <div class="signature-space"></div>
                <span class="signature-name">{{ $teacher->name }}</span><br>
                NIP {{ $teacher->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>