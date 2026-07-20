<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modul Ajar - {{ $module->materi }}</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1a1b1e; }

        h2.title { text-align: center; font-size: 15px; margin: 0 0 4px 0; text-decoration: underline; }
        p.subtitle { text-align: center; margin: 0 0 20px 0; font-size: 11px; }

        table.identity { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        table.identity td { padding: 3px 6px; font-size: 11px; vertical-align: top; }
        table.identity td.label { width: 160px; font-weight: bold; }

        .batch-section { margin-bottom: 24px; page-break-inside: avoid; }
        .batch-title { font-size: 13px; font-weight: bold; color: #0F2544; border-bottom: 1px solid #0F2544; padding-bottom: 4px; margin-bottom: 8px; }
        .batch-content h3 { font-size: 12px; margin: 10px 0 4px 0; }
        .batch-content p { margin: 4px 0; }
        .batch-content ul { margin: 4px 0 4px 18px; }

        table.signature { width: 100%; margin-top: 40px; }
        table.signature td { text-align: center; font-size: 11px; vertical-align: top; padding: 0 20px; }
        .signature-space { height: 65px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <h2 class="title">MODUL AJAR</h2>
    <p class="subtitle">{{ $module->mapel }} &mdash; Kurikulum Merdeka</p>

    <table class="identity">
        <tr><td class="label">Nama Sekolah</td><td>: {{ $module->school_name }}</td></tr>
        <tr><td class="label">Tahun Ajaran</td><td>: {{ $module->academic_year }} &mdash; Semester {{ $module->semester }}</td></tr>
        <tr><td class="label">Fase / Kelas</td><td>: {{ $module->fase }} / {{ $module->kelas }}</td></tr>
        <tr><td class="label">Materi</td><td>: {{ $module->materi }}</td></tr>
        <tr><td class="label">Jumlah Pertemuan</td><td>: {{ $module->meetings_count }} pertemuan</td></tr>
        <tr><td class="label">Alokasi Waktu</td><td>: {{ $module->duration_label }}</td></tr>
        <tr><td class="label">Model Pembelajaran</td><td>: {{ $module->learning_model ?? '-' }}</td></tr>
        <tr><td class="label">Integrasi</td><td>: {{ $module->integration ?? '-' }}</td></tr>
        <tr><td class="label">Capaian Pembelajaran</td><td>: {{ $module->learning_outcomes }}</td></tr>
    </table>

    @foreach ($module->batches as $batch)
    <div class="batch-section">
        <div class="batch-title">Pertemuan {{ $batch['meeting_range'] }}</div>
        <div class="batch-content">
            {!! $batch['content'] !!}
        </div>
    </div>
    @endforeach

    <table class="signature">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <div class="signature-space"></div>
                <span class="signature-name">{{ $module->headmaster_name ?? '(...........................)' }}</span><br>
                NIP {{ $module->headmaster_nip ?? '-' }}
            </td>
            <td>
                {{ $module->signing_place }}, {{ now()->translatedFormat('d F Y') }}<br>Guru Mata Pelajaran
                <div class="signature-space"></div>
                <span class="signature-name">{{ $module->teacher_name }}</span><br>
                NIP {{ $module->teacher_nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>