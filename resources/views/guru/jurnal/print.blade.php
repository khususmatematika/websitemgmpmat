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
        .kop-provinsi { font-size: 13px; font-weight: bold; margin: 0; letter-spacing: 0.3px; }
        .kop-dinas { font-size: 13px; font-weight: bold; margin: 0; letter-spacing: 0.3px; }
        .kop-sekolah { font-size: 20px; font-weight: bold; margin: 2px 0; letter-spacing: 0.5px; }
        .kop-alamat { font-size: 10px; margin: 1px 0; }

        h2.title { text-align: center; font-size: 14px; margin: 16px 0 4px 0; text-decoration: underline; }
        p.subtitle { text-align: center; margin: 0 0 16px 0; font-size: 11px; }

        table.info { width: 100%; margin-bottom: 12px; }
        table.info td { padding: 2px 0; font-size: 11px; }
        table.info td.label { width: 120px; }

        .class-section { margin-bottom: 24px; page-break-inside: avoid; }
        .class-title { font-size: 13px; font-weight: bold; color: #0F2544; background: #efedf1; padding: 6px 8px; margin-bottom: 8px; border-left: 4px solid #0F2544; }

        table.journal { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.journal th, table.journal td { border: 1px solid #999; padding: 5px; font-size: 10px; text-align: left; vertical-align: top; }
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
                <p class="kop-provinsi">PEMERINTAH PROVINSI JAWA TIMUR</p>
                <p class="kop-dinas">DINAS PENDIDIKAN</p>
                <p class="kop-sekolah">{{ strtoupper($letterhead->school_name) }}</p>
                @if ($letterhead->address)
                <p class="kop-alamat">{{ $letterhead->address }}</p>
                @endif
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
                    <th style="width: 30px;">No</th>
                    <th style="width: 80px;">Tanggal</th>
                    <th>Materi</th>
                    <th>Kegiatan</th>
                    <th style="width: 140px;">Siswa Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($group['journals'] as $i => $j)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td class="center">{{ $j->journal_date->translatedFormat('d F Y') }}</td>
                    <td>{{ $j->materi ?? '-' }}</td>
                    <td>{{ $j->kegiatan ?? '-' }}</td>
                    <td class="attendance-list">
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