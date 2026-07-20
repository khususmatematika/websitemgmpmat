<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['12345', 'Contoh Nama Siswa', 'X-1', ''],
            ['12346', 'Contoh Nama Siswa Dua', 'XI IPA 2', 'XI Lintas Minat'],
        ];
    }

    public function headings(): array
    {
        return ['nis', 'nama', 'kelas_reguler', 'kelas_pilihan'];
    }
}