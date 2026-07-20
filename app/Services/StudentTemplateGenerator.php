<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentTemplateGenerator
{
    public function generate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'nis');
        $sheet->setCellValue('B1', 'nama');
        $sheet->setCellValue('C1', 'kelas_reguler');
        $sheet->setCellValue('D1', 'kelas_pilihan');

        $sheet->setCellValue('A2', '12345');
        $sheet->setCellValue('B2', 'Contoh Nama Siswa');
        $sheet->setCellValue('C2', 'X-1');
        $sheet->setCellValue('D2', '');

        $sheet->setCellValue('A3', '12346');
        $sheet->setCellValue('B3', 'Contoh Nama Siswa Dua');
        $sheet->setCellValue('C3', 'XI IPA 2');
        $sheet->setCellValue('D3', 'XI Lintas Minat');

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(22);
        }

        $tempPath = storage_path('app/temp-template-siswa.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }
}