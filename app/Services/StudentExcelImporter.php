<?php
namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentExcelImporter
{
    public array $errors = [];
    public int $successCount = 0;

    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // toArray dengan index kolom huruf (A, B, C, D)
        $rows = $sheet->toArray(null, true, true, true);

        // Baris pertama adalah header: A=nis, B=nama, C=kelas_reguler, D=kelas_pilihan
        array_shift($rows);
        $rowNum = 1;

        foreach ($rows as $row) {
            $rowNum++;

            $nis = trim((string) ($row['A'] ?? ''));
            $name = trim((string) ($row['B'] ?? ''));
            $kelasReguler = trim((string) ($row['C'] ?? ''));
            $kelasPilihan = trim((string) ($row['D'] ?? ''));

            if (empty($name)) {
                $this->errors[] = "Baris {$rowNum}: Nama wajib diisi, dilewati.";
                continue;
            }

            $student = Student::updateOrCreate(
                $nis ? ['nis' => $nis] : ['name' => $name],
                ['name' => $name, 'nis' => $nis ?: null]
            );

            $classIds = [];

            if ($kelasReguler) {
                $class = SchoolClass::where('name', $kelasReguler)->first();
                if ($class) {
                    $classIds[] = $class->id;
                } else {
                    $this->errors[] = "Baris {$rowNum}: Kelas reguler '{$kelasReguler}' tidak ditemukan.";
                }
            }

            if ($kelasPilihan) {
                $class = SchoolClass::where('name', $kelasPilihan)->first();
                if ($class) {
                    $classIds[] = $class->id;
                } else {
                    $this->errors[] = "Baris {$rowNum}: Kelas pilihan '{$kelasPilihan}' tidak ditemukan.";
                }
            }

            if (!empty($classIds)) {
                $student->classes()->syncWithoutDetaching($classIds);
            }

            $this->successCount++;
        }
    }
}